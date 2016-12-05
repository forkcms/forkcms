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
class CKFinder_Connector_ErrorHandler_QuickUpload extends CKFinder_Connector_ErrorHandler_Base {
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
        $errorMessage = CKFinder_Connector_Utils_Misc::getErrorMessage($number, $sEncodedFileName);

        if (!empty($_GET['responseType']) && $_GET['responseType'] == 'json') {
            header('Content-Type: application/json; charset=utf-8');

            $data = array(
                'fileName' => $sEncodedFileName,
                'uploaded' => (int)$uploaded
            );

            if ($uploaded) {
                $data['url'] = $sFileUrl . CKFinder_Connector_Utils_Misc::encodeURIComponent($sEncodedFileName);
            }

            if ($number !== CKFINDER_CONNECTOR_ERROR_NONE) {
                $data['error'] = array(
                    'number'  => $number,
                    'message' => $errorMessage,
                );
            }

            echo json_encode($data);
        }
        else {
            header('Content-Type: text/html; charset=utf-8');

            /**
             * echo <script> is not called before CKFinder_Connector_Utils_Misc::getErrorMessage
             * because PHP has problems with including files that contain BOM character.
             * Having BOM character after <script> tag causes a javascript error.
             */
            echo "<script type=\"text/javascript\">";
            if (!empty($_GET['CKEditor'])) {

                if (!$uploaded) {
                    $sFileUrl = "";
                    $sFileName = "";
                    $sEncodedFileName = "";
                }

                $funcNum = preg_replace("/[^0-9]/", "", $_GET['CKEditorFuncNum']);
                echo "window.parent.CKEDITOR.tools.callFunction($funcNum, '" . str_replace("'", "\\'", $sFileUrl . CKFinder_Connector_Utils_Misc::encodeURIComponent($sEncodedFileName)) . "', '" .str_replace("'", "\\'", $errorMessage). "');";
            }
            else {
                if (!$uploaded) {
                    echo "window.parent.OnUploadCompleted(" . $number . ", '', '', '') ;";
                } else {
                    echo "window.parent.OnUploadCompleted(" . $number . ", '" . str_replace("'", "\\'", $sFileUrl . CKFinder_Connector_Utils_Misc::encodeURIComponent($sEncodedFileName)) . "', '" . str_replace("'", "\\'", $sEncodedFileName) . "', '') ;";
                }
            }
            echo "</script>";
        }

        if ($exit) {
            exit;
        }
    }
}
