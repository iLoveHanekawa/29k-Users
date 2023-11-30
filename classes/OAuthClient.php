<?php
    namespace _29kUsers;
    require_once __DIR__ . '/OAuthUser.php';
    abstract class OAuthClient {
        /** 
            *@var string
        */
        protected $clientId;
        /** 
            *@var string 
        */
        protected $clientSecret;
        /** 
            *@var string  
        */
        protected $codeUrl;
        /** 
            *@var string  
        */
        protected $tokenUrl;
        /** 
            *@var string  
        */
        protected $scopes;
        /** 
            *@var string  
        */
        protected $apiEndpoint;
                /** 
            *@var string  
        */
        protected $redirectUrl;
        protected function __construct(string $clientId, string $clientSecret, string $codeUrl, string $tokenUrl, string $apiEndpoint, string $scopes, string $redirectUrl) {
            $this->clientId = $clientId;
            $this->clientSecret = $clientSecret;
            $this->codeUrl = $codeUrl;
            $this->tokenUrl = $tokenUrl;
            $this->apiEndpoint = $apiEndpoint;
            $this->scopes = $scopes;
            $this->redirectUrl = $redirectUrl;
        }
        /** 
            *@return string
        */
        abstract public function getCodeUrl(): string;
        /** 
            *@return string
        */ 
        abstract protected function exchangeCode(string $code): string;
        /** 
            *@return OAuthUser|Error
        */
        abstract public function getUser(string $code): OAuthUser|Error;
    }
?>