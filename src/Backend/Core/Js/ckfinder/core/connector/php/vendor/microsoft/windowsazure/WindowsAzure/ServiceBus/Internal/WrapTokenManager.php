<?php

/**
 * LICENSE: Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * 
 * PHP version 5
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceBus\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/WindowsAzure/azure-sdk-for-php
 */
 
namespace WindowsAzure\ServiceBus\Internal;
use WindowsAzure\Common\Configuration;
use WindowsAzure\Common\ServicesBuilder;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\Validate;
use WindowsAzure\ServiceBus\Internal\WrapRestProxy;
use WindowsAzure\ServiceBus\Internal\ActiveToken;

/**
 * Manages WRAP tokens. 
 *
 * @category  Microsoft
 * @package   WindowsAzure\ServiceBus\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/WindowsAzure/azure-sdk-for-php
 */

class WrapTokenManager
{
    /** 
     * The Uri of the WRAP service.
     * 
     * @var string
     */
    private $_wrapUri;

    /** 
     * The user name of the WRAP service.
     * 
     * @var string
     */
    private $_wrapName;

    /** 
     * The password of the WRAP service.
     * 
     * @var string
     */
    private $_wrapPassword;

    /** 
     * The proxy of the WRAP service.
     * 
     * @var string
     */
    private $_wrapRestProxy;

    /** 
     * The active WRAP access tokens.
     * 
     * @var array
     */
    private $_activeTokens;

    /**
     * Creates a WRAP token manager with specified parameters. 
     *
     * @param string $wrapUri       The URI of the WRAP service.
     * @param string $wrapName      The user name of the WRAP service.
     * @param string $wrapPassword  The password of the WRAP service.
     * @param IWrap  $wrapRestProxy The WRAP service REST proxy.
     */
    public function __construct($wrapUri, $wrapName, $wrapPassword, $wrapRestProxy)
    {
        Validate::isString($wrapUri, 'wrapUri');
        Validate::isString($wrapName, 'wrapName');
        Validate::isString($wrapPassword, 'wrapPassword');
        Validate::notNullOrEmpty($wrapRestProxy, 'wrapRestProxy');

        $this->_wrapUri       = $wrapUri;
        $this->_wrapName      = $wrapName;
        $this->_wrapPassword  = $wrapPassword;
        $this->_wrapRestProxy = $wrapRestProxy;
        $this->_activeTokens  = array();
        
    }    

    /** 
     * Gets WRAP access token with sepcified target Uri. 
     * 
     * @param string $targetUri The target Uri of the WRAP access Token. 
     * 
     * @return string
     */
    public function getAccessToken($targetUri) 
    {
        Validate::isString($targetUri, '$targetUri');

        $this->_sweepExpiredTokens();
        $scopeUri = $this->_createScopeUri($targetUri);

        if (array_key_exists($scopeUri, $this->_activeTokens)) {
            $activeToken = $this->_activeTokens[$scopeUri];
            return $activeToken->getWrapAccessTokenResult()->getAccessToken();
        }

        $wrapAccessTokenResult = $this->_wrapRestProxy->wrapAccessToken(
            $this->_wrapUri, 
            $this->_wrapName,
            $this->_wrapPassword,
            $scopeUri
        );

        $expirationDateTime = new \DateTime("now");
        $expiresIn          = intval($wrapAccessTokenResult->getExpiresIn() / 2); 
        $expirationDateTime = $expirationDateTime->add(
            new \DateInterval('PT'.$expiresIn.'S')
        );

        $acquiredActiveToken = new ActiveToken($wrapAccessTokenResult);
        $acquiredActiveToken->setExpirationDateTime($expirationDateTime); 
        $this->_activeTokens[$scopeUri] = $acquiredActiveToken;

        return $wrapAccessTokenResult->getAccessToken(); 
    }
    
    /** 
     * Removes the expired WRAP access tokens. 
     * 
     * @return none
     */
    private function _sweepExpiredTokens()
    {
        foreach ($this->_activeTokens as $scopeUri => $activeToken) {
            $currentDateTime = new \DateTime("now");
            if ($activeToken->getExpirationDateTime() < $currentDateTime ) {
                unset($this->_activeTokens[$scopeUri]);
            }
        }
    }

    /**
     * Creates a SCOPE URI with specified target URI. 
     *
     * @param array $targetUri The target URI.
     * 
     * @return string
     */   
    private function _createScopeUri($targetUri)
    {   
        $targetUriComponents = parse_url($targetUri);

        $scopeUri  = Resources::EMPTY_STRING;
        $authority = Resources::EMPTY_STRING;
        if ($this->_containsValidAuthority($targetUriComponents)) {
            $authority = $this->_createAuthority($targetUriComponents);
        }

        $scopeUri = 'http://'
            .$authority
            .$targetUriComponents[Resources::PHP_URL_HOST];

        if (array_key_exists(Resources::PHP_URL_PATH, $targetUriComponents)) {
            $scopeUri .= $targetUriComponents[Resources::PHP_URL_PATH];
        }

        return $scopeUri;
    }

    /** 
     * Gets whether the authority related elements are valid. 
     * 
     * @param array $uriComponents The components of an URI.
     * 
     * @return boolean
     */
    private function _containsValidAuthority($uriComponents)
    {
        if (! array_key_exists(Resources::PHP_URL_USER, $uriComponents)) {
            return false;
        }

        if (empty($uriComponents[Resources::PHP_URL_USER])) {
            return false;
        }

        if (! array_key_exists(Resources::PHP_URL_PASS, $uriComponents)) {
            return false;
        }

        if (empty($uriComponents[Resources::PHP_URL_PASS])) {
            return false;
        }

        return true;
    }

    /** 
     * Creates an authority string with specified Uri components. 
     *
     * @param array $uriComponents The URI components
     *
     * @return string 
     */
    private function _createAuthority($uriComponents)
    {
        $authority = sprintf(
            Resources::AUTHORITY_FORMAT,
            $uriComponents[Resources::PHP_URL_USER],
            $uriComponents[Resources::PHP_URL_PASS]
        );

        return $authority;
    }
}


