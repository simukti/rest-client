<?php
namespace RestClient\Authorization;

/**
 * @package   RestClient\Authorization
 * @author    Sarjono Mukti Aji <me@simukti.net>
 */
abstract class AuthorizationAbstract implements AuthorizationInterface
{
    /**
     * Default authorization header name value prefix
     */
    const DEFAULT_NAME = 'Bearer';
    
    /**
     * Default separator is one space
     */
    const DEFAULT_SEPARATOR = ' ';
    
    /**
     * @var string
     */
    protected $name = self::DEFAULT_NAME;
    
    /**
     * @var string
     */
    protected $value = '';
    
    /**
     * @var string
     */
    protected $separator = self::DEFAULT_SEPARATOR;
    
    /**
     * @var bool
     */
    protected $includeName = true;
    
    /**
     * @inheritdoc
     */
    public function setName(string $name) : AuthorizationInterface
    {
        $this->name = $name;
        
        return $this;
    }
    
    /**
     * @inheritdoc
     */
    public function setValue(string $value) : AuthorizationInterface
    {
        $this->value = $value;
        
        return $this;
    }
    
    /**
     * @inheritdoc
     */
    public function setSeparator(string $separator) : AuthorizationInterface
    {
        $this->separator = $separator;
        
        return $this;
    }
    
    /**
     * @inheritdoc
     */
    public function includeName(bool $flag) : AuthorizationInterface
    {
        $this->includeName = $flag;
        
        return $this;
    }
}