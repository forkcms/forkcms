<?php
/*
* CKFinder
* ========
* http://ckfinder.com
* Copyright (C) 2007-2012, CKSource - Frederico Knabben. All rights reserved.
*
* The software, this file and its contents are subject to the CKFinder
* License. Please read the license.txt file before using, installing, copying,
* modifying or distribute this file or part of its contents. The contents of
* this file is part of the Source Code of CKFinder.
*
* CKFinder extension: prodives command that saves edited file.
*/
if (!defined('IN_CKFINDER')) exit;

/**
 * Include base XML command handler
 */
require_once CKFINDER_CONNECTOR_LIB_DIR . "/CommandHandler/XmlCommandHandlerBase.php";

class CKFinder_Connector_CommandHandler_FileEditor extends CKFinder_Connector_CommandHandler_XmlCommandHandlerBase
{
    /**
     * handle request and build XML
     * @access protected
     */
    function buildXml()
    {
        if (empty($_POST['CKFinderCommand']) || $_POST['CKFinderCommand'] != 'true') {
            $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_REQUEST);
        }

        $this->checkConnector();
        $this->checkRequest();

        // Saving empty file is equal to deleting a file, that's why FILE_DELETE permissions are required
        if (!$this->_currentFolder->checkAcl(CKFINDER_CONNECTOR_ACL_FILE_DELETE)) {
            $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_UNAUTHORIZED);
        }

        if (!isset($_POST["fileName"])) {
            $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_NAME);
        }
        if (!isset($_POST["content"])) {
            $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_REQUEST);
        }

        $fileName = CKFinder_Connector_Utils_FileSystem::convertToFilesystemEncoding($_POST["fileName"]);
        $resourceTypeInfo = $this->_currentFolder->getResourceTypeConfig();

        if (!$resourceTypeInfo->checkExtension($fileName)) {
            $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_EXTENSION);
        }

        if (!CKFinder_Connector_Utils_FileSystem::checkFileName($fileName) || $resourceTypeInfo->checkIsHiddenFile($fileName)) {
            $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_REQUEST);
        }

        $filePath = CKFinder_Connector_Utils_FileSystem::combinePaths($this->_currentFolder->getServerPath(), $fileName);

        if (!file_exists($filePath) || !is_file($filePath)) {
            $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_FILE_NOT_FOUND);
        }

        if (!is_writable(dirname($filePath))) {
            $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_ACCESS_DENIED);
        }

        $fp = @fopen($filePath, 'wb');
        if ($fp === false || !flock($fp, LOCK_EX)) {
            $result = false;
        }
        else {
            $result = fwrite($fp, $_POST["content"]);
            flock($fp, LOCK_UN);
            fclose($fp);
        }
        if ($result === false) {
            $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_ACCESS_DENIED);
        }
    }

    /**
     * @access public
     */
    function onBeforeExecuteCommand( &$command )
    {
        if ( $command == 'SaveFile' )
        {
            $this->sendResponse();
            return false;
        }

        return true ;
    }
}

$CommandHandler_FileEditor = new CKFinder_Connector_CommandHandler_FileEditor();
$config['Hooks']['BeforeExecuteCommand'][] = array($CommandHandler_FileEditor, "onBeforeExecuteCommand");
$config['Plugins'][] = 'fileeditor';
