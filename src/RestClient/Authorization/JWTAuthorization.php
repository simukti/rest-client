<?php
namespace RestClient\Authorization;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer;

/**
 * @package   RestClient\Authorization
 * @author    Sarjono Mukti Aji <me@simukti.net>
 */
final class JWTAuthorization extends AuthorizationAbstract
{
    /**
     * Default JWT expired in seconds (time() + expired_in)
     */
    const DEFAULT_JWT_EXPIRED_IN = 30;
    
    /**
     * @var string
     */
    private $key = '';
    
    /**
     * @var string
     */
    private $secret = '';
    
    /**
     * @var array
     */
    private $extraPayload = [];
    
    /**
     * @var int
     */
    private $expiredIn = self::DEFAULT_JWT_EXPIRED_IN;
    
    /**
     * @var Signer
     */
    private $jwtSigner;
    
    /**
     * @param string      $key
     * @param string      $secret
     * @param array       $extraPayload
     * @param int         $expiredIn
     * @param Signer|null $jwtSigner
     * @param string      $name
     * @param string      $separator
     * @param bool        $includeName
     */
    public function __construct(
        string $key,
        string $secret,
        array $extraPayload = [],
        int $expiredIn = self::DEFAULT_JWT_EXPIRED_IN,
        Signer $jwtSigner = null,
        string $name = self::DEFAULT_NAME,
        string $separator = self::DEFAULT_SEPARATOR,
        bool $includeName = true
    ) {
        $this->key          = $key;
        $this->secret       = $secret;
        $this->extraPayload = $extraPayload;
        $this->jwtSigner    = $jwtSigner;
        $this->expiredIn    = $expiredIn;
        $this->name         = $name;
        $this->separator    = $separator;
        $this->includeName  = $includeName;
    }
    
    /**
     * @inheritDoc
     */
    public function getContent() : string
    {
        if (!empty($this->value)) {
            if ($this->includeName) {
                return sprintf("%s%s%s", $this->name, $this->separator, $this->value);
            }
            
            return $this->value;
        }
        
        if (!$this->jwtSigner) {
            $this->jwtSigner = new Signer\Hmac\Sha256();
        }
        
        $tokenBuilder = new Builder();
        $tokenBuilder->setIssuer($this->key)
            ->setIssuedAt(time())
            ->setNotBefore(time())
            ->setExpiration(time() + $this->expiredIn);
        
        if (count($this->extraPayload)) {
            foreach ($this->extraPayload as $key => $value) {
                $tokenBuilder->set($key, $value);
            }
        }
        
        $token       = $tokenBuilder->sign($this->jwtSigner, $this->secret)->getToken();
        $this->value = $token->__toString();
        
        if ($this->includeName) {
            return sprintf("%s%s%s", $this->name, $this->separator, $this->value);
        }
        
        return $this->value;
    }
}