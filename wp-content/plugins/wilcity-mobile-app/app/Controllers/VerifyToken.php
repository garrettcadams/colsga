<?php

namespace WILCITY_APP\Controllers;

use ReallySimpleJWT\Token;
use ReallySimpleJWT\TokenValidator;
use Symfony\Component\HttpFoundation\Request;
use WilokeListingTools\Framework\Helpers\General;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Frontend\User;

trait VerifyToken
{
    protected $oValidator;
    protected $oPayLoad;
    protected $aRoles;
    protected $userID;
    use GetSecurityKey;
    
    protected function getAuthorizationHeader()
    {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders =
                array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        
        return $headers;
    }
    
    /**
     * get access token from header
     * */
    protected function getBearerToken()
    {
        $headers = $this->getAuthorizationHeader();
        
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        
        return null;
    }
    
    public function tokenExpiration()
    {
        return [
            'status' => 'error',
            'msg'    => 'tokenExpired'
        ];
    }
    
    protected function getPayLoad()
    {
        if ($this->oValidator) {
            $rawPayLoad = $this->oValidator->getPayload();
            if (empty($rawPayLoad)) {
                $this->oPayLoad = false;
            } else {
                $this->oPayLoad = json_decode($rawPayLoad, true);
            }
        } else {
            $this->oPayLoad = false;
        }
        
        return $this;
    }
    
    protected function verifyPermanentToken()
    {
        $token  = $this->getBearerToken();
        $status = $this->verifyToken($token);
        if ($status) {
            if (GetSettings::getUserMeta($this->userID, 'app_token') != $token) {
                return false;
            }
            
            return $this;
        }
        
        return false;
    }
    
    protected function verifyTemporaryToken($token)
    {
        $status = $this->verifyToken($token);
        if ($status === false) {
            return false;
        }
        
        if (GetSettings::getUserMeta($this->userID, 'temporary_app_token') != $token) {
            return false;
        }
        
        if (GetSettings::getUserMeta($this->userID, 'temporary_user_ip') != General::clientIP()) {
            return false;
        }
        
        return $this;
    }
    
    protected function verifyToken($token)
    {
        $this->oValidator = new TokenValidator();
        
        if (empty($token)) {
            $this->oValidator = false;
            
            return false;
        }
        try {
            $this->oValidator->splitToken($token)
                             ->validateExpiration()
                             ->validateSignature($this->getSecurityAuthKey())
            ;
            
            $this->getPayLoad();
        } catch (\Exception $exception) {
            $this->oPayLoad   = false;
            $this->aRoles     = false;
            $this->oValidator = false;
            
            return false;
        }
        $this->getUserID();
        
        return true;
    }
    
    protected function getUserID()
    {
        $this->userID = abs($this->oPayLoad['userID']);
        
        return $this;
    }
    
    protected function getRoles()
    {
        $this->aRoles = User::getField('roles', $this->userID);
        
        return $this;
    }
}
