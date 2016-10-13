<?php
/*
 * CKFinder
 * ========
 * http://cksource.com/ckfinder
 * Copyright (C) 2007-2015, CKSource - Frederico Knabben. All rights reserved.
 *
 * The software, this file and its contents are subject to the CKFinder
 * License. Please read the license.txt file before using, installing, copying,
 * modifying or distribute this file or part of its contents. The contents of
 * this file is part of the Source Code of CKFinder.
 */
if (!defined('IN_CKFINDER')) exit;

/**
 * @package CKFinder
 * @subpackage Config
 * @copyright CKSource - Frederico Knabben
 */

/**
 * This class keeps thumbnails configuration
 *
 * @package CKFinder
 * @subpackage Config
 * @copyright CKSource - Frederico Knabben
 */
class CKFinder_Connector_Core_ThumbnailsConfig
{

    /**
     * Url to thumbnails directory
     *
     * @var string
     * @access private
     */
    private $_url = "";
    /**
	 * Directory where thumbnails are stored
	 *
	 * @var string
	 * @access private
	 */
    private $_directory = "";
    /**
	 * Are thumbnails enabled
	 *
	 * @var boolean
	 * @access private
	 */
    private $_isEnabled = false;
    /**
	 * Direct access to thumbnails?
	 *
	 * @var boolean
	 * @access private
	 */
    private $_directAccess = false;
    /**
     * Max width for thumbnails
     *
     * @var int
     * @access private
     */
    private $_maxWidth = 100;
    /**
     * Max height for thumbnails
     *
     * @var int
     * @access private
     */
    private $_maxHeight = 100;
    /**
     * Quality of thumbnails
     *
     * @var int
     * @access private
     */
    private $_quality = 100;
    /**
     * Are thumbnails of bitmap files enabled?
     *
     * @var boolean
     * @access private
     */
    private $_bmpSupported = false;

    function __construct($thumbnailsNode)
    {
        if(extension_loaded('gd') && isset($thumbnailsNode['enabled'])) {
            $this->_isEnabled = CKFinder_Connector_Utils_Misc::booleanValue($thumbnailsNode['enabled']);
        }
        if( isset($thumbnailsNode['directAccess'])) {
            $this->_directAccess = CKFinder_Connector_Utils_Misc::booleanValue($thumbnailsNode['directAccess']);
        }
        if( isset($thumbnailsNode['bmpSupported'])) {
            $this->_bmpSupported = CKFinder_Connector_Utils_Misc::booleanValue($thumbnailsNode['bmpSupported']);
        }
        if(isset($thumbnailsNode['maxWidth'])) {
            $_maxWidth = intval($thumbnailsNode['maxWidth']);
            if($_maxWidth>=0) {
                $this->_maxWidth = $_maxWidth;
            }
        }
        if(isset($thumbnailsNode['maxHeight'])) {
            $_maxHeight = intval($thumbnailsNode['maxHeight']);
            if($_maxHeight>=0) {
                $this->_maxHeight = $_maxHeight;
            }
        }
        if(isset($thumbnailsNode['quality'])) {
            $_quality = intval($thumbnailsNode['quality']);
            if($_quality>0 && $_quality<=100) {
                $this->_quality = $_quality;
            }
        }

        if(isset($thumbnailsNode['url'])) {
            $this->_url = $thumbnailsNode['url'];
        }
        if (!strlen($this->_url)) {
            $this->_url = "/";
        }
        else if(substr($this->_url,-1,1) != "/") {
            $this->_url .= "/";
        }

        if(isset($thumbnailsNode['directory'])) {
            $this->_directory = $thumbnailsNode['directory'];
        }
    }

    /**
     * Get URL
     *
     * @access public
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * Get directory
     *
     * @access public
     * @return string
     */
    public function getDirectory()
    {
        return $this->_directory;
    }

    /**
     * Get is enabled setting
     *
     * @access public
     * @return boolean
     */
    public function getIsEnabled()
    {
        return $this->_isEnabled;
    }

    /**
     * Get is enabled setting
     *
     * @access public
     * @return boolean
     */
    public function getBmpSupported()
    {
        return $this->_bmpSupported;
    }

    /**
     * Is direct access to thumbnails allowed?
     *
     * @access public
     * @return boolean
     */
    public function getDirectAccess()
    {
        return $this->_directAccess;
    }

    /**
     * Get maximum width of a thumbnail
     *
     * @access public
     * @return int
     */
    public function getMaxWidth()
    {
        return $this->_maxWidth;
    }

    /**
     * Get maximum height of a thumbnail
     *
     * @access public
     * @return int
     */
    public function getMaxHeight()
    {
        return $this->_maxHeight;
    }

    /**
     * Get quality of a thumbnail (1-100)
     *
     * @access public
     * @return int
     */
    public function getQuality()
    {
        return $this->_quality;
    }
}
