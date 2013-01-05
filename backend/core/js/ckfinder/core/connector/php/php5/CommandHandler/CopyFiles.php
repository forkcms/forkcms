<?php
/*
 * CKFinder
 * ========
 * http://cksource.com/ckfinder
 * Copyright (C) 2007-2013, CKSource - Frederico Knabben. All rights reserved.
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
 * Include base XML command handler
 */
require_once CKFINDER_CONNECTOR_LIB_DIR . "/CommandHandler/XmlCommandHandlerBase.php";

/**
 * Handle CopyFiles command
 *
 * @package CKFinder
 * @subpackage CommandHandlers
 * @copyright CKSource - Frederico Knabben
 */
class CKFinder_Connector_CommandHandler_CopyFiles extends CKFinder_Connector_CommandHandler_XmlCommandHandlerBase
{
    /**
     * Command name
     *
     * @access private
     * @var string
     */
    private $command = "CopyFiles";


    /**
     * handle request and build XML
     * @access protected
     *
     */
    protected function buildXml()
    {
        if (empty($_POST['CKFinderCommand']) || $_POST['CKFinderCommand'] != 'true') {
            $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_REQUEST);
        }

        $clientPath = $this->_currentFolder->getClientPath();
        $sServerDir = $this->_currentFolder->getServerPath();
        $currentResourceTypeConfig = $this->_currentFolder->getResourceTypeConfig();
        $_config =& CKFinder_Connector_Core_Factory::getInstance("Core_Config");
        $_aclConfig = $_config->getAccessControlConfig();
        $aclMasks = array();
        $_resourceTypeConfig = array();

        if (!$this->_currentFolder->checkAcl(CKFINDER_CONNECTOR_ACL_FILE_RENAME | CKFINDER_CONNECTOR_ACL_FILE_UPLOAD | CKFINDER_CONNECTOR_ACL_FILE_DELETE)) {
            $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_UNAUTHORIZED);
        }

        // Create the "Errors" node.
        $oErrorsNode = new CKFinder_Connector_Utils_XmlNode("Errors");
        $errorCode = CKFINDER_CONNECTOR_ERROR_NONE;
        $copied = 0;
        $copiedAll = 0;
        if (!empty($_POST['copied'])) {
            $copiedAll = intval($_POST['copied']);
        }
        $checkedPaths = array();

        $oCopyFilesNode = new Ckfinder_Connector_Utils_XmlNode("CopyFiles");

        if (!empty($_POST['files']) && is_array($_POST['files'])) {
            foreach ($_POST['files'] as $index => $arr) {
                if (empty($arr['name'])) {
                    continue;
                }
                if (!isset($arr['name'], $arr['type'], $arr['folder'])) {
                    $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_REQUEST);
                }

                // file name
                $name = CKFinder_Connector_Utils_FileSystem::convertToFilesystemEncoding($arr['name']);
                // resource type
                $type = $arr['type'];
                // client path
                $path = CKFinder_Connector_Utils_FileSystem::convertToFilesystemEncoding($arr['folder']);
                // options
                $options = (!empty($arr['options'])) ? $arr['options'] : '';

                $destinationFilePath = $sServerDir.$name;

                // check #1 (path)
                if (!CKFinder_Connector_Utils_FileSystem::checkFileName($name) || preg_match(CKFINDER_REGEX_INVALID_PATH, $path)) {
                    $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_REQUEST);
                }

                // get resource type config for current file
                if (!isset($_resourceTypeConfig[$type])) {
                    $_resourceTypeConfig[$type] = $_config->getResourceTypeConfig($type);
                }

                // check #2 (resource type)
                if (is_null($_resourceTypeConfig[$type])) {
                    $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_REQUEST);
                }

                // check #3 (extension)
                if (!$_resourceTypeConfig[$type]->checkExtension($name, false)) {
                    $errorCode = CKFINDER_CONNECTOR_ERROR_INVALID_EXTENSION;
                    $this->appendErrorNode($oErrorsNode, $errorCode, $name, $type, $path);
                    continue;
                }

                // check #4 (extension) - when moving to another resource type, double check extension
                if ($currentResourceTypeConfig->getName() != $type) {
                    if (!$currentResourceTypeConfig->checkExtension($name, false)) {
                        $errorCode = CKFINDER_CONNECTOR_ERROR_INVALID_EXTENSION;
                        $this->appendErrorNode($oErrorsNode, $errorCode, $name, $type, $path);
                        continue;
                    }
                }

                // check #5 (hidden folders)
                // cache results
                if (empty($checkedPaths[$path])) {
                    $checkedPaths[$path] = true;

                    if ($_resourceTypeConfig[$type]->checkIsHiddenPath($path)) {
                        $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_REQUEST);
                    }
                }

                $sourceFilePath = $_resourceTypeConfig[$type]->getDirectory().$path.$name;

                // check #6 (hidden file name)
                if ($currentResourceTypeConfig->checkIsHiddenFile($name)) {
                    $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_REQUEST);
                }

                // check #7 (Access Control, need file view permission to source files)
                if (!isset($aclMasks[$type."@".$path])) {
                    $aclMasks[$type."@".$path] = $_aclConfig->getComputedMask($type, $path);
                }

                $isAuthorized = (($aclMasks[$type."@".$path] & CKFINDER_CONNECTOR_ACL_FILE_VIEW) == CKFINDER_CONNECTOR_ACL_FILE_VIEW);
                if (!$isAuthorized) {
                    $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_UNAUTHORIZED);
                }

                // check #8 (invalid file name)
                if (!file_exists($sourceFilePath) || !is_file($sourceFilePath)) {
                    $errorCode = CKFINDER_CONNECTOR_ERROR_FILE_NOT_FOUND;
                    $this->appendErrorNode($oErrorsNode, $errorCode, $name, $type, $path);
                    continue;
                }

                // check #9 (max size)
                if ($currentResourceTypeConfig->getName() != $type) {
                    $maxSize = $currentResourceTypeConfig->getMaxSize();
                    $fileSize = filesize($sourceFilePath);
                    if ($maxSize && $fileSize>$maxSize) {
                        $errorCode = CKFINDER_CONNECTOR_ERROR_UPLOADED_TOO_BIG;
                        $this->appendErrorNode($oErrorsNode, $errorCode, $name, $type, $path);
                        continue;
                    }
                }

                //$overwrite
                // finally, no errors so far, we may attempt to copy a file
                // protection against copying files to itself
                if ($sourceFilePath == $destinationFilePath) {
                    $errorCode = CKFINDER_CONNECTOR_ERROR_SOURCE_AND_TARGET_PATH_EQUAL;
                    $this->appendErrorNode($oErrorsNode, $errorCode, $name, $type, $path);
                    continue;
                }
                // check if file exists if we don't force overwriting
                else if (file_exists($destinationFilePath) && strpos($options, "overwrite") === false) {
                    if (strpos($options, "autorename") !== false) {
                        $fileName = CKFinder_Connector_Utils_FileSystem::autoRename($sServerDir, $name);
                        $destinationFilePath = $sServerDir.$fileName;
                        if (!@copy($sourceFilePath, $destinationFilePath)) {
                            $errorCode = CKFINDER_CONNECTOR_ERROR_ACCESS_DENIED;
                            $this->appendErrorNode($oErrorsNode, $errorCode, $name, $type, $path);
                            continue;
                        }
                        else {
                            $copied++;
                        }
                    }
                    else {
                        $errorCode = CKFINDER_CONNECTOR_ERROR_ALREADY_EXIST;
                        $this->appendErrorNode($oErrorsNode, $errorCode, $name, $type, $path);
                        continue;
                    }
                }
                // copy() overwrites without warning
                else {
                    if (!@copy($sourceFilePath, $destinationFilePath)) {
                        $errorCode = CKFINDER_CONNECTOR_ERROR_ACCESS_DENIED;
                        $this->appendErrorNode($oErrorsNode, $errorCode, $name, $type, $path);
                        continue;
                    }
                    else {
                        $copied++;
                    }
                }
            }
        }

        $this->_connectorNode->addChild($oCopyFilesNode);
        if ($errorCode != CKFINDER_CONNECTOR_ERROR_NONE) {
            $this->_connectorNode->addChild($oErrorsNode);
        }
        $oCopyFilesNode->addAttribute("copied", $copied);
        $oCopyFilesNode->addAttribute("copiedTotal", $copiedAll + $copied);

        /**
         * Note: actually we could have more than one error.
         * This is just a flag for CKFinder interface telling it to check all errors.
         */
        if ($errorCode != CKFINDER_CONNECTOR_ERROR_NONE) {
            $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_COPY_FAILED);
        }
    }

    private function appendErrorNode($oErrorsNode, $errorCode, $name, $type, $path)
    {
        $oErrorNode = new CKFinder_Connector_Utils_XmlNode("Error");
        $oErrorNode->addAttribute("code", $errorCode);
        $oErrorNode->addAttribute("name", CKFinder_Connector_Utils_FileSystem::convertToConnectorEncoding($name));
        $oErrorNode->addAttribute("type", $type);
        $oErrorNode->addAttribute("folder", $path);
        $oErrorsNode->addChild($oErrorNode);
    }
}
