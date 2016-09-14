<?php
namespace RestClient\Response;

/**
 * @package   RestClient\Response
 * @author    Sarjono Mukti Aji <me@simukti.net>
 */
class ApiResponse implements ResponseInterface
{
    /**
     * @var int
     */
    private $statusCode = 0;
    
    /**
     * @var array
     */
    private $headers = [];
    
    /**
     * @var string
     */
    private $content = '';
    
    /**
     * @var bool
     */
    private $isError = false;
    
    /**
     * @var bool
     */
    private $isClientError = false;
    
    /**
     * @var bool
     */
    private $isServerError = false;
    
    /**
     * @inheritDoc
     */
    public function setStatusCode(int $statusCode) : ResponseInterface
    {
        $this->statusCode = $statusCode;
        
        if ($statusCode >= 400 && $statusCode < 499) {
            $this->isError       = true;
            $this->isClientError = true;
        } elseif ($statusCode > 499 && $statusCode < 520) {
            $this->isError       = true;
            $this->isServerError = true;
        } elseif ($statusCode == 0) {
            $this->isError       = true;
            $this->isServerError = true;
        }
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function getStatusCode() : int
    {
        return $this->statusCode;
    }
    
    /**
     * @inheritDoc
     */
    public function setHeaders(array $headers) : ResponseInterface
    {
        $this->headers = array_change_key_case($headers, CASE_LOWER);
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function getHeaders() : array
    {
        return $this->headers;
    }
    
    /**
     * @inheritDoc
     */
    public function setContent(string $content) : ResponseInterface
    {
        $this->content = $content;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function getContent() : string
    {
        return $this->content;
    }
    
    /**
     * @inheritDoc
     */
    public function getContentType() : string
    {
        $contentType = $this->getHeader('content-type');
        
        if (!$contentType) {
            return (string)$contentType;
        }
        
        $contentTypeExplode = explode(';', $contentType);
        
        if (count($contentTypeExplode) > 1) {
            return $contentTypeExplode[0];
        }
        
        return $contentType;
    }
    
    /**
     * @inheritDoc
     */
    public function getHeader(string $name, $defaultValue = null) : string
    {
        $name = strtolower($name);
        
        if (isset($this->headers[$name])) {
            return (string)$this->headers[$name];
        }
        
        return (string)$defaultValue;
    }
    
    /**
     * @inheritDoc
     */
    public function isError() : bool
    {
        return $this->isError;
    }
    
    /**
     * @inheritDoc
     */
    public function isClientError() : bool
    {
        return $this->isClientError;
    }
    
    /**
     * @inheritDoc
     */
    public function isServerError() : bool
    {
        return $this->isServerError;
    }
    
}