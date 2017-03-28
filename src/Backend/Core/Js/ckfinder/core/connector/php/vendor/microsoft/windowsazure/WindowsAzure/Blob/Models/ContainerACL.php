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
 * @package   WindowsAzure\Blob\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */

namespace WindowsAzure\Blob\Models;
use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\Common\Internal\Validate;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Blob\Models\AccessPolicy;
use WindowsAzure\Blob\Models\SignedIdentifier;
use WindowsAzure\Blob\Models\PublicAccessType;
use WindowsAzure\Common\Internal\Serialization\XmlSerializer;

/**
 * Holds conatiner ACL members.
 * 
 * @category  Microsoft
 * @package   WindowsAzure\Blob\Models
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class ContainerAcl
{
    /**
     * All available types can be found in PublicAccessType
     *
     * @var string
     */
    private $_publicAccess;

    /**
     * @var array
     */
    private $_signedIdentifiers = array();
    
    /*
     * The root name of XML elemenet representation.
     * 
     * @var string
     */
    public static $xmlRootName = 'SignedIdentifiers';


    /**
     * Parses the given array into signed identifiers.
     * 
     * @param string $publicAccess The container public access.
     * @param array  $parsed       The parsed response into array representation.
     * 
     * @return none
     */
    public static function create($publicAccess, $parsed)
    {
        $result                     = new ContainerAcl();
        $result->_publicAccess      = $publicAccess;
        $result->_signedIdentifiers = array();
        
        if (!empty($parsed) && is_array($parsed['SignedIdentifier'])) {
            $entries = $parsed['SignedIdentifier'];
            $temp    = Utilities::getArray($entries);

            foreach ($temp as $value) {
                $startString  = urldecode($value['AccessPolicy']['Start']);
                $expiryString = urldecode($value['AccessPolicy']['Expiry']);
                $start        = Utilities::convertToDateTime($startString);
                $expiry       = Utilities::convertToDateTime($expiryString);
                $permission   = $value['AccessPolicy']['Permission'];
                $id           = $value['Id'];
                $result->addSignedIdentifier($id, $start, $expiry, $permission);
            }
        }
        
        return $result;
    }

    /**
     * Gets container signed modifiers.
     *
     * @return array.
     */
    public function getSignedIdentifiers()
    {
        return $this->_signedIdentifiers;
    }

    /**
     * Sets container signed modifiers.
     *
     * @param array $signedIdentifiers value.
     *
     * @return none.
     */
    public function setSignedIdentifiers($signedIdentifiers)
    {
        $this->_signedIdentifiers = $signedIdentifiers;
    }

    /**
     * Gets container publicAccess.
     *
     * @return string.
     */
    public function getPublicAccess()
    {
        return $this->_publicAccess;
    }

    /**
     * Sets container publicAccess.
     *
     * @param string $publicAccess value.
     *
     * @return none.
     */
    public function setPublicAccess($publicAccess)
    {
        Validate::isTrue(
            PublicAccessType::isValid($publicAccess),
            Resources::INVALID_BLOB_PAT_MSG
        );
        $this->_publicAccess = $publicAccess;
    }

    /**
     * Adds new signed modifier
     * 
     * @param string    $id         a unique id for this modifier
     * @param \DateTime $start      The time at which the Shared Access Signature
     * becomes valid. If omitted, start time for this call is assumed to be
     * the time when the Blob service receives the request.
     * @param \DateTime $expiry     The time at which the Shared Access Signature
     * becomes invalid. This field may be omitted if it has been specified as
     * part of a container-level access policy.
     * @param string    $permission The permissions associated with the Shared
     * Access Signature. The user is restricted to operations allowed by the
     * permissions. Valid permissions values are read (r), write (w), delete (d) and
     * list (l).
     * 
     * @return none.
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh508996.aspx
     */
    public function addSignedIdentifier($id, $start, $expiry, $permission)
    {
        Validate::isString($id, 'id');
        Validate::isDate($start);
        Validate::isDate($expiry);
        Validate::isString($permission, 'permission');
        
        $accessPolicy = new AccessPolicy();
        $accessPolicy->setStart($start);
        $accessPolicy->setExpiry($expiry);
        $accessPolicy->setPermission($permission);
        
        $signedIdentifier = new SignedIdentifier();
        $signedIdentifier->setId($id);
        $signedIdentifier->setAccessPolicy($accessPolicy);
        
        $this->_signedIdentifiers[] = $signedIdentifier;
    }
    
    /**
     * Converts this object to array representation for XML serialization 
     * 
     * @return array.
     */
    public function toArray()
    {
        $array = array();
        
        foreach ($this->_signedIdentifiers as $value) {
            $array[] = $value->toArray();
        }
        
        return $array;
    }
    
    /**
     * Converts this current object to XML representation.
     * 
     * @param XmlSerializer $xmlSerializer The XML serializer.
     * 
     * @return string.
     */
    public function toXml($xmlSerializer)
    {
        $properties = array(
            XmlSerializer::DEFAULT_TAG => 'SignedIdentifier',
            XmlSerializer::ROOT_NAME   => self::$xmlRootName
        );
        
        return $xmlSerializer->serialize($this->toArray(), $properties);
    }
}


