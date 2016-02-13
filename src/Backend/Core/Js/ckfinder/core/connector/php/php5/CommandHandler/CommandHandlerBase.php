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
 * @subpackage CommandHandlers
 * @copyright CKSource - Frederico Knabben
 */

/**
 * Base commands handler
 *
 * @package CKFinder
 * @subpackage CommandHandlers
 * @copyright CKSource - Frederico Knabben
 * @abstract
 *
 */
class CKFinder_Connector_CommandHandler_CommandHandlerBase
{
    /**
     * CKFinder_Connector_Core_Connector object
     *
     * @access protected
     * @var CKFinder_Connector_Core_Connector
     */
    protected $_connector;
    /**
     * CKFinder_Connector_Core_FolderHandler object
     *
     * @access protected
     * @var CKFinder_Connector_Core_FolderHandler
     */
    protected $_currentFolder;
    /**
     * Error handler object
     *
     * @access protected
     * @var CKFinder_Connector_ErrorHandler_Base|CKFinder_Connector_ErrorHandler_FileUpload|CKFinder_Connector_ErrorHandler_Http
     */
    protected $_errorHandler;

    function __construct()
    {
        $this->_currentFolder =& CKFinder_Connector_Core_Factory::getInstance("Core_FolderHandler");
        $this->_connector =& CKFinder_Connector_Core_Factory::getInstance("Core_Connector");
        $this->_errorHandler =&  $this->_connector->getErrorHandler();
    }

    /**
     * Get Folder Handler
     *
     * @access public
     * @return CKFinder_Connector_Core_FolderHandler
     */
    public function getFolderHandler()
    {
        if (is_null($this->_currentFolder)) {
            $this->_currentFolder =& CKFinder_Connector_Core_Factory::getInstance("Core_FolderHandler");
        }

        return $this->_currentFolder;
    }

    /**
     * Check whether Connector is enabled
     * @access protected
     *
     */
    protected function checkConnector()
    {
        $_config =& CKFinder_Connector_Core_Factory::getInstance("Core_Config");
        if (!$_config->getIsEnabled()) {
            $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_CONNECTOR_DISABLED);
        }
    }

    /**
     * Check request
     * @access protected
     *
     */
    protected function checkRequest()
    {
        if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') {
            /* @var $_config CKFinder_Connector_Core_Config */
            $_config =& CKFinder_Connector_Core_Factory::getInstance("Core_Config");

            if ($_config->getEnableCsrfProtection() && !$this->checkCsrfToken()) {
                $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_REQUEST);
            }
        }

        if (preg_match(CKFINDER_REGEX_INVALID_PATH, $this->_currentFolder->getClientPath())) {
            $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_NAME);
        }

        $_resourceTypeConfig = $this->_currentFolder->getResourceTypeConfig();

        if (is_null($_resourceTypeConfig)) {
            $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_TYPE);
        }

        $_clientPath = $this->_currentFolder->getClientPath();
        $_clientPathParts = explode("/", trim($_clientPath, "/"));
        if ($_clientPathParts) {
            foreach ($_clientPathParts as $_part) {
                if ($_resourceTypeConfig->checkIsHiddenFolder($_part)) {
                    $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_REQUEST);
                }
            }
        }

        if (!is_dir($this->_currentFolder->getServerPath())) {
            if ($_clientPath == "/") {
                if (!CKFinder_Connector_Utils_FileSystem::createDirectoryRecursively($this->_currentFolder->getServerPath())) {
                    /**
                     * @todo handle error
                     */
                }
            }
            else {
                $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_FOLDER_NOT_FOUND);
            }
        }
    }

    /**
     * Checks if the request contains a valid token that matches the value sent in the cookie.
     * @see https://www.owasp.org/index.php/Cross-Site_Request_Forgery_(CSRF)_Prevention_Cheat_Sheet#Double_Submit_Cookies
     *
     * @access protected
     *
     * @param string $tokenParamName
     * @param string $tokenCookieName
     * @param int    $minTokenLength
     *
     * @return bool true if token is valid, false otherwise
     */
    protected function checkCsrfToken($tokenParamName = 'ckCsrfToken', $tokenCookieName = 'ckCsrfToken', $minTokenLength = 32)
    {
        $paramToken = isset($_POST[$tokenParamName]) ? trim((string) $_POST[$tokenParamName]) : '';
        $cookieToken = isset($_COOKIE[$tokenCookieName]) ? trim((string) $_COOKIE[$tokenCookieName]) : '';

        if (strlen($paramToken) >= $minTokenLength && strlen($cookieToken) >= $minTokenLength) {
            return $paramToken === $cookieToken;
        }

        return false;
    }
}
