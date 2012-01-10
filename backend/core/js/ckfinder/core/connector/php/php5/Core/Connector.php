<?php
/*
 * CKFinder
 * ========
 * http://ckfinder.com
 * Copyright (C) 2007-2011, CKSource - Frederico Knabben. All rights reserved.
 *
 * The software, this file and its contents are subject to the CKFinder
 * License. Please read the license.txt file before using, installing, copying,
 * modifying or distribute this file or part of its contents. The contents of
 * this file is part of the Source Code of CKFinder.
 */
if (!defined('IN_CKFINDER')) exit;

/**
 * @package CKFinder
 * @subpackage Core
 * @copyright CKSource - Frederico Knabben
 */

/**
 * Executes all commands
 *
 * @package CKFinder
 * @subpackage Core
 * @copyright CKSource - Frederico Knabben
 */
class CKFinder_Connector_Core_Connector
{
    /**
     * Registry
     *
     * @var CKFinder_Connector_Core_Registry
     * @access private
     */
    private $_registry;

    function __construct()
    {
        $this->_registry =& CKFinder_Connector_Core_Factory::getInstance("Core_Registry");
        $this->_registry->set("errorHandler", "ErrorHandler_Base");
    }

    /**
     * Generic handler for invalid commands
     * @access public
     *
     */
    public function handleInvalidCommand()
    {
        $oErrorHandler =& $this->getErrorHandler();
        $oErrorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_COMMAND);
    }

    /**
     * Execute command
     *
     * @param string $command
     * @access public
     */
    public function executeCommand($command)
    {
        if (!CKFinder_Connector_Core_Hooks::run('BeforeExecuteCommand', array(&$command))) {
            return;
        }

        switch ($command)
        {
            case 'FileUpload':
            $this->_registry->set("errorHandler", "ErrorHandler_FileUpload");
            $obj =& CKFinder_Connector_Core_Factory::getInstance("CommandHandler_".$command);
            $obj->sendResponse();
            break;

            case 'QuickUpload':
            $this->_registry->set("errorHandler", "ErrorHandler_QuickUpload");
            $obj =& CKFinder_Connector_Core_Factory::getInstance("CommandHandler_".$command);
            $obj->sendResponse();
            break;

            case 'DownloadFile':
            case 'Thumbnail':
            $this->_registry->set("errorHandler", "ErrorHandler_Http");
            $obj =& CKFinder_Connector_Core_Factory::getInstance("CommandHandler_".$command);
            $obj->sendResponse();
            break;

            case 'CopyFiles':
            case 'CreateFolder':
            case 'DeleteFile':
            case 'DeleteFolder':
            case 'GetFiles':
            case 'GetFolders':
            case 'Init':
            case 'LoadCookies':
            case 'MoveFiles':
            case 'RenameFile':
            case 'RenameFolder':
            $obj =& CKFinder_Connector_Core_Factory::getInstance("CommandHandler_".$command);
            $obj->sendResponse();
            break;

            default:
            $this->handleInvalidCommand();
            break;
        }
    }

    /**
     * Get error handler
     *
     * @access public
     * @return CKFinder_Connector_ErrorHandler_Base|CKFinder_Connector_ErrorHandler_FileUpload|CKFinder_Connector_ErrorHandler_Http
     */
    public function &getErrorHandler()
    {
        $_errorHandler = $this->_registry->get("errorHandler");
        $oErrorHandler =& CKFinder_Connector_Core_Factory::getInstance($_errorHandler);
        return $oErrorHandler;
    }
}
