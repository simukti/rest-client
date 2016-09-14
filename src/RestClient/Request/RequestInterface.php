<?php
namespace RestClient\Request;

use RestClient\Authorization\AuthorizationInterface;

/**
 * @package   RestClient\Request
 * @author    Sarjono Mukti Aji <me@simukti.net>
 */
interface RequestInterface
{
    /**
     * https://tools.ietf.org/html/rfc2616#section-9.3
     */
    const METHOD_GET    = 'GET';
    
    /**
     * https://tools.ietf.org/html/rfc2616#section-9.5
     */
    const METHOD_POST   = 'POST';
    
    /**
     * https://tools.ietf.org/html/rfc2616#section-9.6
     */
    const METHOD_PUT    = 'PUT';
    
    /**
     * https://tools.ietf.org/html/rfc5789
     */
    const METHOD_PATCH  = 'PATCH';
    
    /**
     * https://tools.ietf.org/html/rfc2616#section-9.7
     */
    const METHOD_DELETE = 'DELETE';
    
    /**
     * @param string $baseUrl
     *
     * @return RequestInterface
     */
    public function setBaseUrl(string $baseUrl) : RequestInterface;
    
    /**
     * @return string
     */
    public function getBaseUrl() : string;
    
    /**
     * @param string $method
     *
     * @return RequestInterface
     */
    public function setMethod(string $method) : RequestInterface;
    
    /**
     * @return string
     */
    public function getMethod() : string;
    
    /**
     * @param string $path
     *
     * @return RequestInterface
     */
    public function setPath(string $path) : RequestInterface;
    
    /**
     * @return string
     */
    public function getPath() : string;
    
    /**
     * @param AuthorizationInterface $authorization
     *
     * @return RequestInterface
     */
    public function setAuthorization(AuthorizationInterface $authorization) : RequestInterface;
    
    /**
     * @return AuthorizationInterface
     */
    public function getAuthorization() : AuthorizationInterface;
    
    /**
     * @param string $key
     * @param string $value
     *
     * @return RequestInterface
     */
    public function addHeader(string $key, string $value) : RequestInterface;
    
    /**
     * @return array
     */
    public function getHeaders() : array;
    
    /**
     * @param string     $key
     * @param string|int $value
     *
     * @return RequestInterface
     */
    public function addQuery(string $key, $value) : RequestInterface;
    
    /**
     * @param array $query
     *
     * @return RequestInterface
     */
    public function setQuery(array $query) : RequestInterface;
    
    /**
     * @return array
     */
    public function getQuery() : array;
    
    /**
     * @return RequestInterface
     */
    public function resetQuery() : RequestInterface;
    
    /**
     * @param string $fieldName
     * @param        $value
     *
     * @return RequestInterface
     */
    public function addData(string $fieldName, $value) : RequestInterface;
    
    /**
     * @param array $data
     *
     * @return RequestInterface
     */
    public function setData(array $data) : RequestInterface;
    
    /**
     * @return array
     */
    public function getData() : array;
    
    /**
     * @return RequestInterface
     */
    public function resetData() : RequestInterface;
    
    /**
     * @param string $fieldName
     * @param string $filepath
     *
     * @return RequestInterface
     */
    public function addFile(string $fieldName, string $filepath) : RequestInterface;
    
    /**
     * @return array
     */
    public function getFiles() : array;
    
    /**
     * @return RequestInterface
     */
    public function resetFiles() : RequestInterface;
}