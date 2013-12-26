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
 * Handle DeleteFile command
 *
 * @package CKFinder
 * @subpackage CommandHandlers
 * @copyright CKSource - Frederico Knabben
 */
class CKFinder_Connector_CommandHandler_LoadCookies extends CKFinder_Connector_CommandHandler_XmlCommandHandlerBase
{
    /**
     * Command name
     *
     * @access private
     * @var string
     */
    private $command = "LoadCookies";


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

        if (!$this->_currentFolder->checkAcl(CKFINDER_CONNECTOR_ACL_FILE_VIEW)) {
            $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_UNAUTHORIZED);
        }

        $oCookiesNode = new Ckfinder_Connector_Utils_XmlNode("Cookies");
        $this->_connectorNode->addChild($oCookiesNode);
        $i = 0;
        foreach ($_COOKIE as $name => $value) {
            if (!is_array($value) && strpos($name, "CKFinder_") !== 0) {
                $oCookieNode[$i] = new Ckfinder_Connector_Utils_XmlNode("Cookie");
                $oCookiesNode->addChild($oCookieNode[$i]);
                $oCookieNode[$i]->addAttribute("name", $name);
                $oCookieNode[$i]->addAttribute("value", $value);
                $i++;
            }
        }
    }
}
