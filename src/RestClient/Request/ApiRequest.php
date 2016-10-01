<?php
namespace RestClient\Request;

use RestClient\Authorization\AuthorizationInterface;

/**
 * @package   RestClient\Request
 * @author    Sarjono Mukti Aji <me@simukti.net>
 */
class ApiRequest implements RequestInterface
{
    /**
     * Default accept type to send to API server
     */
    const DEFAULT_ACCEPT_TYPE = 'application/json';
    
    /**
     *  Default accept encoding to send to API server
     */
    const DEFAULT_ACCEPT_ENCODING = 'gzip, deflate';
    
    /**
     * @var string
     */
    private $baseUrl;
    
    /**
     * @var string
     */
    private $method = self::METHOD_GET;
    
    /**
     * @var string
     */
    private $path;
    
    /**
     * @var AuthorizationInterface
     */
    private $authorization;
    
    /**
     * @var array
     */
    private $headers = [];
    
    /**
     * @var array
     */
    private $query = [];
    
    /**
     * @var array
     */
    private $data = [];
    
    /**
     * @var string
     */
    private $dataRaw = '';
    
    /**
     * @var array
     */
    private $files = [];
    
    /**
     * @param string $baseUrl Base API server url
     */
    public function __construct(string $baseUrl)
    {
        $this->setBaseUrl($baseUrl);
    }
    
    /**
     * @inheritDoc
     */
    public function setBaseUrl(string $baseUrl) : RequestInterface
    {
        $baseUrl = rtrim($baseUrl, '/.\\ \t\n\r\0\x0B');
        $baseUrl = trim($baseUrl);
        $url     = parse_url($baseUrl);
        $scheme  = strtolower($url['scheme']) ?? '';
        
        $allowedSchemes = [
            'http',
            'https',
        ];
        
        if (!in_array(strtolower($url['scheme']), $allowedSchemes)) {
            throw new \InvalidArgumentException(sprintf("Invalid scheme '%s'", $scheme));
        }
        
        if (!filter_var($baseUrl, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException(sprintf("Invalid url '%s'", $baseUrl));
        }
        
        $this->baseUrl = $baseUrl;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function getBaseUrl() : string
    {
        return $this->baseUrl;
    }
    
    /**
     * @inheritDoc
     */
    public function setMethod(string $method) : RequestInterface
    {
        $this->method = strtoupper(trim($method));
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function getMethod() : string
    {
        return $this->method;
    }
    
    /**
     * @inheritDoc
     */
    public function setPath(string $path) : RequestInterface
    {
        $this->path = implode('/', array_filter(explode('/', trim($path))));
        
        if (null == $this->path) {
            $this->path = '/';
        } else {
            if ('/' !== $this->path[0]) {
                $this->path = '/'.$this->path;
            }
        }
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function getPath() : string
    {
        return $this->path ?? '/';
    }
    
    /**
     * @inheritDoc
     */
    public function setAuthorization(AuthorizationInterface $authorization) : RequestInterface
    {
        $this->authorization = $authorization;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function getAuthorization() : AuthorizationInterface
    {
        return $this->authorization;
    }
    
    /**
     * @inheritDoc
     */
    public function addHeader(string $key, string $value) : RequestInterface
    {
        $this->headers[$key] = $value;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function getHeaders() : array
    {
        $this->headers = array_change_key_case($this->headers, CASE_LOWER);
        
        if (!isset($this->headers['accept'])) {
            $this->headers['accept'] = self::DEFAULT_ACCEPT_TYPE;
        }
        
        if (!isset($this->headers['accept-encoding'])) {
            $this->headers['accept-encoding'] = self::DEFAULT_ACCEPT_ENCODING;
        }
        
        if ($this->authorization) {
            $this->headers['authorization'] = $this->getAuthorization()->getContent();
        }
        
        return $this->headers;
    }
    
    /**
     * @inheritDoc
     */
    public function addQuery(string $key, $value) : RequestInterface
    {
        $this->query[$key] = $value;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setQuery(array $query) : RequestInterface
    {
        $this->query = $query;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function getQuery() : array
    {
        return $this->query;
    }
    
    /**
     * @inheritDoc
     */
    public function resetQuery() : RequestInterface
    {
        return $this->setQuery([]);
    }
    
    /**
     * @inheritDoc
     */
    public function addData(string $fieldName, $value) : RequestInterface
    {
        $this->data[$fieldName] = $value;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setData(array $data) : RequestInterface
    {
        $this->data = $data;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function getData() : array
    {
        return $this->data;
    }
    
    /**
     * @inheritDoc
     */
    public function setDataRaw(string $data) : RequestInterface
    {
        $this->dataRaw = $data;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function getDataRaw() : string
    {
        return $this->dataRaw;
    }
    
    /**
     * @inheritDoc
     */
    public function resetData() : RequestInterface
    {
        $this->data    = [];
        $this->dataRaw = '';
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function addFile(string $fieldName, string $filepath) : RequestInterface
    {
        if (!file_exists($filepath)) {
            throw new \InvalidArgumentException(sprintf("File '%s' does not exists", $filepath));
        }
        
        $this->files[$fieldName] = $filepath;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function getFiles() : array
    {
        return $this->files;
    }
    
    /**
     * @inheritDoc
     */
    public function resetFiles() : RequestInterface
    {
        $this->files = [];
        
        return $this;
    }
}