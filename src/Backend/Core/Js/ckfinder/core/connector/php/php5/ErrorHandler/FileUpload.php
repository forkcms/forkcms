<?php

/*
 * CKFinder
 * ========
 * http://cksource.com/ckfinder
 * Copyright (C) 2007-2014, CKSource - Frederico Knabben. All rights reserved.
 *
 * The software, this file and its contents are subject to the CKFinder
 * License. Please read the license.txt file before using, installing, copying,
 * modifying or distribute this file or part of its contents. The contents of
 * this file is part of the Source Code of CKFinder.
 */
if (!defined('IN_CKFINDER')) exit;

/**
 * @package CKFinder
 * @subpackage ErrorHandler
 * @copyright CKSource - Frederico Knabben
 */

/**
 * Include base error handling class
 */
require_once CKFINDER_CONNECTOR_LIB_DIR . "/ErrorHandler/Base.php";

/**
 * File upload error handler
 *
 * @package CKFinder
 * @subpackage ErrorHandler
 * @copyright CKSource - Frederico Knabben
 */
class CKFinder_Connector_ErrorHandler_FileUpload extends CKFinder_Connector_ErrorHandler_Base {
    /**
     * Throw file upload error, return true if error has been thrown, false if error has been catched
     *
     * @param int $number
     * @param string $text
     * @access public
     */
    public function throwError($number, $uploaded = false, $exit = true) {
        if ($this->_catchAllErrors || in_array($number, $this->_skipErrorsArray)) {
            return false;
        }

        $oRegistry = & CKFinder_Connector_Core_Factory :: getInstance("Core_Registry");
        $sFileName = $oRegistry->get("FileUpload_fileName");
        $sFileUrl = $oRegistry->get("FileUpload_url");
        $sEncodedFileName = CKFinder_Connector_Utils_FileSystem::convertToConnectorEncoding($sFileName);

        header('Content-Type: text/html; charset=utf-8');

        $errorMessage = CKFinder_Connector_Utils_Misc::getErrorMessage($number, $sEncodedFileName);
        if (!$uploaded) {
            $sFileName = "";
            $sEncodedFileName = "";
        }
        if (!empty($_GET['response_type']) && $_GET['response_type'] == 'txt') {
            echo $sFileName."|".$errorMessage;
        }
        else {
            echo "<script type=\"text/javascript\">";
            if (!empty($_GET['CKFinderFuncNum'])) {

                if (!$uploaded) {
                    $sFileUrl = "";
                    $sFileName = "";
                }

                $funcNum = preg_replace("/[^0-9]/", "", $_GET['CKFinderFuncNum']);
                echo "window.parent.CKFinder.tools.callFunction($funcNum, '" . str_replace("'", "\\'", $sFileUrl . $sFileName) . "', '" .str_replace("'", "\\'", $errorMessage). "');";
            }
            else {
                echo "window.parent.OnUploadCompleted('" . str_replace("'", "\\'", $sEncodedFileName) . "', '" . str_replace("'", "\\'", $errorMessage) . "') ;";
            }
            echo "</script>";
        }

        if ($exit) {
            exit;
        }
    }
}
