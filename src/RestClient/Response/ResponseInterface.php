<?php
namespace RestClient\Response;

/**
 * @package   RestClient\Response
 * @author    Sarjono Mukti Aji <me@simukti.net>
 */
interface ResponseInterface
{
    /**
     * @param int $statusCode
     *
     * @return ResponseInterface
     */
    public function setStatusCode(int $statusCode) : ResponseInterface;
    
    /**
     * @return int
     */
    public function getStatusCode() : int;
    
    /**
     * @param array $headers
     *
     * @return ResponseInterface
     */
    public function setHeaders(array $headers) : ResponseInterface;
    
    /**
     * @return array
     */
    public function getHeaders() : array;
    
    /**
     * @param string $content
     *
     * @return ResponseInterface
     */
    public function setContent(string $content) : ResponseInterface;
    
    /**
     * @return string
     */
    public function getContent() : string;
    
    /**
     * @return string
     */
    public function getContentType() : string;
    
    /**
     * @param string $name
     * @param null   $defaultValue
     *
     * @return string
     */
    public function getHeader(string $name, $defaultValue = null) : string;
    
    /**
     * @return bool
     */
    public function isError() : bool;
    
    /**
     * @return bool
     */
    public function isClientError() : bool;
    
    /**
     * @return bool
     */
    public function isServerError() : bool;
}