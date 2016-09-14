<?php
namespace RestClient\Authorization;

/**
 * @package   RestClient\Authorization
 * @author    Sarjono Mukti Aji <me@simukti.net>
 */
interface AuthorizationInterface
{
    /**
     * Set prefixed name before authorization header value.
     *
     * @param string $name
     *
     * @return AuthorizationInterface
     */
    public function setName(string $name) : AuthorizationInterface;
    
    /**
     * Set authorization header value string.
     *
     * @param string $value
     *
     * @return AuthorizationInterface
     */
    public function setValue(string $value) : AuthorizationInterface;
    
    /**
     * Set string separator between name prefix and value
     *
     * @param string $separator
     *
     * @return AuthorizationInterface
     */
    public function setSeparator(string $separator) : AuthorizationInterface;
    
    /**
     * Whether include prefix name in authorization header value
     *
     * @param bool $flag
     *
     * @return AuthorizationInterface
     */
    public function includeName(bool $flag) : AuthorizationInterface;
    
    /**
     * Get authorization header string content.
     * This include (optional) name, (optional) separator, and value
     *
     * @return string
     */
    public function getContent() : string;
}