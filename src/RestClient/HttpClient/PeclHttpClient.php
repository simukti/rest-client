<?php
namespace RestClient\HttpClient;

use http\Client;
use http\Client\Curl;
use http\Encoding\Stream\Inflate;
use http\Message\Body;
use http\QueryString;
use RestClient\Request\RequestInterface;
use RestClient\Response\ResponseInterface;

/**
 * @package   RestClient\HttpClient
 * @author    Sarjono Mukti Aji <me@simukti.net>
 */
class PeclHttpClient implements HttpClientInterface
{
    /**
     * Default pecl http client options
     *
     * @link https://mdref.m6w6.name/http/Client/getAvailableConfiguration
     * @var array
     */
    private $clientConfigs = [
        'maxconnects'                 => -1,
        'max_host_connections'        => 4,
        'max_pipeline_length'         => 6,
        'max_total_connections'       => 0,
        'pipelining'                  => true,
        'chunk_length_penalty_size'   => 0,
        'content_length_penalty_size' => 0,
        'pipelining_server_bl'        => null,
        'pipelining_site_bl'          => null,
        'use_eventloop'               => true,
    ];
    
    /**
     * Default pecl http request options
     *
     * @link https://mdref.m6w6.name/http/Client/getAvailableOptions
     * @var array
     */
    private $requestOptions = [
        'proxyport'         => 0,
        'proxyauthtype'     => Curl\AUTH_ANY,
        'proxytunnel'       => false,
        'connecttimeout'    => 15,
        'timeout'           => 15,
        'dns_cache_timeout' => 30,
        'fresh_connect'     => false,
        'maxfilesize'       => 0,
        'redirect'          => 3,
        'retrycount'        => 0,
        'retrydelay'        => 0,
        'autoreferer'       => false,
        'useragent'         => 'simukti/rest-client',
        'lastmodified'      => 0,
        'tcp_nodelay'       => true,
        'ipresolve'         => Curl\IPRESOLVE_ANY,
        'certtype'          => 'PEM',
        'keytype'           => 'PEM',
        'version'           => Curl\SSL_VERSION_ANY,
        'verifypeer'        => true,
        'verifyhost'        => true,
        'verifystatus'      => false,
        'certinfo'          => false,
        'enable_npn'        => true,
        'enable_alpn'       => true,
    ];
    
    /**
     * @var Client
     */
    private $httpClient = null;
    
    /**
     * All params will be merged with default params
     *
     * @param array $clientConfigs
     * @param array $requestOptions
     */
    public function __construct(array $clientConfigs = [], array $requestOptions = [])
    {
        $this->clientConfigs  = array_merge($this->clientConfigs, $clientConfigs);
        $this->requestOptions = array_merge($this->requestOptions, $requestOptions);
    }
    
    /**
     * @inheritdoc
     */
    public function send(RequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        $endpoint    = $request->getBaseUrl().$request->getPath();
        $method      = strtoupper(trim($request->getMethod()));
        $httpRequest = new Client\Request();
        $httpRequest->setOptions($this->requestOptions);
        $httpRequest->setRequestMethod($method);
        $httpRequest->setRequestUrl($endpoint);
        
        $headers = $request->getHeaders();
        if (count($headers)) {
            foreach ($headers as $key => $value) {
                $httpRequest->setHeader($key, $value);
            }
        }
        
        // always include url queries
        $httpRequest->setQuery($request->getQuery());
        
        // send HEAD request immediately
        if ($method === RequestInterface::METHOD_HEAD) {
            return $this->sendSingleRequest($httpRequest, $response);
        }
        
        // immediate send GET | DELETE, without further checking for form data
        if (in_array(
            $method,
            [
                RequestInterface::METHOD_GET,
                RequestInterface::METHOD_DELETE,
            ]
        )) {
            return $this->sendSingleRequest($httpRequest, $response);
        }
        
        $data  = $request->getData();
        $files = $request->getFiles();
        
        if (count($files)) {
            /* @var $requestBody Body */
            $requestBody   = $httpRequest->getBody();
            $preparedFiles = [];
            // file upload detected, set to multipart form
            foreach ($files as $fieldName => $filepath) {
                // filepath should be validated before this step
                $preparedFiles[] = [
                    'name' => $fieldName,
                    'type' => finfo_file(finfo_open(FILEINFO_MIME_TYPE), $filepath),
                    'file' => basename($filepath),
                    'data' => fopen($filepath, 'r'),
                ];
            }
            
            /**
             * This will automatically set content-type to multipart-form
             *
             * @link https://mdref.m6w6.name/http/Client#Submitting.a.multipart.form:
             */
            $requestBody->addForm($request->getData(), $preparedFiles);
            
            // immediate send multipart form request, it always include files and data
            return $this->sendSingleRequest($httpRequest, $response);
        }
        
        $dataRaw = $request->getDataRaw();
        
        if ($dataRaw != null) {
            /* @var $requestBody Body */
            $requestBody = $httpRequest->getBody();
            $requestBody->append($dataRaw);
            
            // send raw body string as is
            return $this->sendSingleRequest($httpRequest, $response);
        }
        
        if (count($data)) {
            /* @var $requestBody Body */
            $requestBody = $httpRequest->getBody();
            $contentType = $headers['content-type'] ?? '';
            
            if (mb_substr(trim($contentType), 0, mb_strlen('application/json')) === 'application/json') {
                $requestBody->append(json_encode($data));
            } else {
                // fall back to urlencoded form
                $requestBody->append(new QueryString($data));
            }
        }
        
        return $this->sendSingleRequest($httpRequest, $response);
    }
    
    /**
     * @param Client\Request    $httpRequest
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    private function sendSingleRequest(Client\Request $httpRequest, ResponseInterface $response) : ResponseInterface
    {
        $client = $this->getHttpClient();
        $client->enqueue($httpRequest);
        
        try {
            $client->send();
            
            /* @var $rawResponse Client\Response */
            $rawResponse     = $client->getResponse($httpRequest);
            $responseHeaders = $rawResponse->getHeaders();
            $responseCode    = $rawResponse->getResponseCode();
            
            /**
             * Handle gziped/deflated result
             *
             * @link https://pecl.php.net/package-info.php?package=pecl_http&version=0.20.0
             */
            if (in_array(
                $rawResponse->getHeader('x-original-content-encoding'),
                [
                    'gzip',
                    'deflate',
                ]
            )) {
                $responseContent = Inflate::decode($rawResponse->getBody()->toString());
            } else {
                $responseContent = $rawResponse->getBody()->toString();
            }
            
            $response->setStatusCode($responseCode)
                ->setHeaders($responseHeaders)
                ->setContent($responseContent);
        } catch (\Exception $exc) {
            $response->setStatusCode($exc->getCode())
                ->setHeaders([])
                ->setContent($exc->getMessage());
        }
        
        /**
         * @link https://mdref.m6w6.name/http/Client/dequeue
         */
        $client->dequeue($httpRequest);
        
        return $response;
    }
    
    /**
     * @return Client
     */
    private function getHttpClient() : Client
    {
        if (!$this->httpClient) {
            $client = new Client('curl');
            $client->configure($this->clientConfigs);
            $this->httpClient = $client;
        }
        
        return $this->httpClient;
    }
}