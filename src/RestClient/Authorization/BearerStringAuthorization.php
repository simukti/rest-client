<?php
namespace RestClient\Authorization;

/**
 * @package   RestClient\Authorization
 * @author    Sarjono Mukti Aji <me@simukti.net>
 */
final class BearerStringAuthorization extends AuthorizationAbstract
{
    /**
     * @param string $value
     * @param string $name
     * @param string $separator
     * @param bool   $includeName
     */
    public function __construct(
        string $value,
        string $name = self::DEFAULT_NAME,
        string $separator = self::DEFAULT_SEPARATOR,
        bool $includeName = true
    ) {
        $this->value       = $value;
        $this->name        = $name;
        $this->separator   = $separator;
        $this->includeName = $includeName;
    }
    
    /**
     * @inheritDoc
     */
    public function getContent() : string
    {
        if ($this->includeName) {
            return sprintf("%s%s%s", $this->name, $this->separator, $this->value);
        }
        
        return $this->value;
    }
}