<?php
namespace RestClient\Authorization;

/**
 * @package   RestClient\Authorization
 * @author    Sarjono Mukti Aji <me@simukti.net>
 */
final class BasicAuthorization extends AuthorizationAbstract
{
    /**
     * Basic authorization prefix value name
     */
    const DEFAULT_BASIC_NAME = 'Basic';
    
    /**
     * @var string
     */
    private $username;
    
    /**
     * @var string
     */
    private $password;
    
    /**
     * @param string $username
     * @param string $password
     */
    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
        $this->name     = self::DEFAULT_BASIC_NAME;
    }
    
    /**
     * @inheritDoc
     */
    public function getContent() : string
    {
        $credential  = sprintf("%s:%s", $this->username, $this->password);
        $this->value = base64_encode($credential);
        
        return sprintf("%s%s%s", $this->name, $this->separator, $this->value);
    }
}