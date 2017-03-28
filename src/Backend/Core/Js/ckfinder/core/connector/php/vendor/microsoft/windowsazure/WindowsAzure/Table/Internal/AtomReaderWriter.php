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
 * @package   WindowsAzure\Table\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
 
namespace WindowsAzure\Table\Internal;
use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Table\Models\EdmType;
use WindowsAzure\Table\Models\Entity;

/**
 * Serializes and unserializes results from table wrapper calls
 *
 * @category  Microsoft
 * @package   WindowsAzure\Table\Internal
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: 0.4.0_2014-01
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
class AtomReaderWriter implements IAtomReaderWriter
{
    /**
     * @var string
     */
    private $_atomNamespaceName;
    
    /**
     * @var string
     */
    private $_dataServicesNamespaceName;
    
    /**
     * @var string 
     */
    private $_dataServicesMetadataNamespaceName;
    
    /**
     * @var string
     */
    private $_xmlVersion;
    
    /**
     * @var string
     */
    private $_xmlEncoding;
    
    /**
     * @var string
     */
    private $_dataServicesPrefix;
    
    /**
     * @var string
     */
    private $_dataServicesMetadataPrefix;
    
    /**
     * Generates the atom XML properties.
     * 
     * @param \XmlWriter $xmlw       The XML writer.
     * @param array      $properties The atom properties.
     * 
     * @return none
     */
    private function _generateProperties($xmlw, $properties)
    {
        foreach ($properties as $key => $value) {
            $content    = key($value);
            $attributes = $value[$content];
            $xmlw->startElementNS($this->_dataServicesPrefix, $key, null);
            if (!is_null($attributes)) {
                foreach ($attributes as $attribute => $attributeValue) {
                    $xmlw->writeAttributeNS(
                        $this->_dataServicesMetadataPrefix,
                        $attribute,
                        null,
                        $attributeValue
                    );
                }
            }
            $xmlw->text($content);
            $xmlw->endElement();
        }
    }

    /**
     * Serializes the atom into XML representation.
     * 
     * @param array $properties The atom properties.
     * 
     * @return string
     */
    private function _serializeAtom($properties)
    {
        $xmlw = new \XmlWriter();
        $xmlw->openMemory();
        $xmlw->setIndent(true);
        $xmlw->startDocument(
            strtoupper($this->_xmlVersion),
            $this->_xmlEncoding,
            'yes'
        );
        $xmlw->startElementNS(null, 'entry', $this->_atomNamespaceName);
        $xmlw->writeAttribute(
            "xmlns:$this->_dataServicesPrefix",
            $this->_dataServicesNamespaceName
        );
        $xmlw->writeAttribute(
            "xmlns:$this->_dataServicesMetadataPrefix",
            $this->_dataServicesMetadataNamespaceName
        );
        $xmlw->writeElement('title');
        $xmlw->writeElement('updated', Utilities::isoDate());
        $xmlw->startElement('author');
        $xmlw->writeElement('name');
        $xmlw->endElement();
        $xmlw->writeElement('id');
        $xmlw->startElement('content');
        $xmlw->writeAttribute('type', Resources::XML_CONTENT_TYPE);
        $xmlw->startElementNS(
            $this->_dataServicesMetadataPrefix,
            'properties',
            null
        );
        $this->_generateProperties($xmlw, $properties);
        $xmlw->endElement();
        $xmlw->endElement();
        $xmlw->endElement();
        
        return $xmlw->outputMemory(true);
    }
    
    /** 
     * Creates new SimpleXml from Atom XML and registers the namespaces prefixes.
     *
     * @param string $body Response from HTTP call.
     * 
     * @return \SimpleXml
     */
    private function _parseBody($body)
    {
        $xml = simplexml_load_string($body);

        if ($xml !== false) {
            $xml->registerXPathNamespace(
                $this->_dataServicesPrefix,
                $this->_dataServicesNamespaceName
            );
            $xml->registerXPathNamespace(
                $this->_dataServicesMetadataPrefix,
                $this->_dataServicesMetadataNamespaceName
            );
        }

        return $xml;
    }
    
    /**
     * Parses one table entry and returns the table name.
     * 
     * @param \SimpleXml $result The original XML body loaded in XML.
     * 
     * @return string
     */
    private function _parseOneTable($result)
    {        
        $query     = ".//$this->_dataServicesMetadataPrefix:properties/";
        $query    .= "$this->_dataServicesPrefix:TableName";
        $tableName = $result->xpath($query);
        $table     = (string)$tableName[0];
        
        return $table;
    }
    
    /**
     * Gets entry nodes from the XML body.
     * 
     * @param \SimpleXml $body The original XML body loaded in XML.
     * 
     * @return array
     */
    private function _getRawEntries($body)
    {
        $rawEntries = array();
        
        if (!is_null($body) && $body->entry) {
            $rawEntries = $body->entry;
        }
        
        return $rawEntries;
    }
    
    /**
     * Parses an entity entry from given SimpleXML object.
     * 
     * @param \SimpleXML $result The SimpleXML object representing the entity.
     * 
     * @return \WindowsAzure\Table\Models\Entity
     */
    private function _parseOneEntity($result)
    {
        $prefix = $this->_dataServicesMetadataPrefix;
        $prop   = $result->content->xpath(".//$prefix:properties");
        $prop   = $prop[0]->children($this->_dataServicesNamespaceName);
        $entity = new Entity();
        
        // Set ETag
        $etag = $result->attributes($this->_dataServicesMetadataNamespaceName);
        $etag = $etag[Resources::ETAG];
        $entity->setETag((string)$etag);
        
        foreach ($prop as $key => $value) {
            $attributes = $value->attributes(
                $this->_dataServicesMetadataNamespaceName
            );
            $type       = $attributes['type'];
            $isnull     = $attributes['null'];
            $value      = EdmType::unserializeQueryValue((string)$type, $value);
            
            $entity->addProperty(
                (string)$key,
                is_null($type) ? null : (string)$type,
                $isnull ? null : $value
            );
        }
        
        return $entity;
    }
    
    /**
     * Constructs new AtomReaderWriter object. 
     */
    public function __construct()
    {
        $this->_atomNamespaceName                 = Resources::ATOM_XML_NAMESPACE;
        $this->_dataServicesNamespaceName         = Resources::DS_XML_NAMESPACE;
        $this->_dataServicesMetadataNamespaceName = Resources::DSM_XML_NAMESPACE;
        $this->_dataServicesPrefix                = 'd';
        $this->_dataServicesMetadataPrefix        = 'm';
        $this->_xmlVersion                        = '1.0';
        $this->_xmlEncoding                       = 'UTF-8';
    }
    
    /**
     * Constructs XML representation for table entry.
     * 
     * @param string $name The name of the table.
     * 
     * @return string
     */
    public function getTable($name)
    {
        return $this->_serializeAtom(array('TableName' => array($name => null)));
    }
    
    /**
     * Parses one table entry.
     * 
     * @param string $body The HTTP response body.
     * 
     * @return string 
     */
    public function parseTable($body)
    {
        $result = $this->_parseBody($body);
        return $this->_parseOneTable($result);
    }
    
    /**
     * Constructs array of tables from HTTP response body.
     * 
     * @param string $body The HTTP response body.
     * 
     * @return array
     */
    public function parseTableEntries($body)
    {
        $tables     = array();
        $result     = $this->_parseBody($body);
        $rawEntries = $this->_getRawEntries($result);
        
        foreach ($rawEntries as $entry) {
            $tables[] = $this->_parseOneTable($entry);
        }
        
        return $tables;
    }
    
    /**
     * Constructs XML representation for entity.
     * 
     * @param Models\Entity $entity The entity instance.
     * 
     * @return string
     */
    public function getEntity($entity)
    {
        $entityProperties = $entity->getProperties();
        $properties       = array();
        
        foreach ($entityProperties as $name => $property) {
            $attributes = array();
            $edmType    = $property->getEdmType();
            $edmValue   = $property->getValue();
            if (!is_null($edmType)) {
                $attributes['type'] = $edmType;
            }
            if (is_null($edmValue)) {
                $attributes['null'] = 'true';
            }
            $value             = EdmType::serializeValue($edmType, $edmValue);
            $properties[$name] = array($value => $attributes);
        }
        
        return $this->_serializeAtom($properties);
    }
    
    /**
     * Constructs entity from HTTP response body.
     * 
     * @param string $body The HTTP response body.
     * 
     * @return Entity
     */
    public function parseEntity($body)
    {
        $result = $this->_parseBody($body);
        $entity = $this->_parseOneEntity($result);
        return $entity;
    }
    
    /**
     * Constructs array of entities from HTTP response body.
     * 
     * @param string $body The HTTP response body.
     * 
     * @return array
     */
    public function parseEntities($body)
    {
        $result     = $this->_parseBody($body);
        $entities   = array();
        $rawEntries = $this->_getRawEntries($result);
        
        foreach ($rawEntries as $entity) {
            $entities[] = $this->_parseOneEntity($entity);
        }
        
        return $entities;
    }
}


