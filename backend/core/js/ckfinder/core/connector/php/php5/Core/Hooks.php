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
class CKFinder_Connector_Core_Hooks
{

    /**
     * Run user defined hooks
     *
     * @param string $event
     * @param object $errorHandler
     * @param array $args
     * @return boolean (true to continue processing, false otherwise)
     */
    public static function run($event, $args = array())
    {
        $config = $GLOBALS['config'];
        if (!isset($config['Hooks'])) {
            return true;
        }
        $hooks =& $config['Hooks'];

        if (!is_array($hooks) || !array_key_exists($event, $hooks) || !is_array($hooks[$event])) {
            return true;
        }

        $errorHandler = $GLOBALS['connector']->getErrorHandler();

        foreach ($hooks[$event] as $i => $hook) {

            $object = NULL;
            $method = NULL;
            $function = NULL;
            $data = NULL;
            $passData = false;

            /* $hook can be: a function, an object, an array of $functiontion and $data,
            * an array of just a function, an array of object and method, or an
            * array of object, method, and data.
            */
            //function
            if (is_string($hook)) {
                $function = $hook;
            }
            //object
            else if (is_object($hook)) {
                $object = $hooks[$event][$i];
                $method = "on" . $event;
            }
            //array of...
            else if (is_array($hook)) {
                $count = count($hook);
                if ($count) {
                    //...object
                    if (is_object($hook[0])) {
                        $object = $hooks[$event][$i][0];
                        if ($count < 2) {
                            $method = "on" . $event;
                        } else {
                            //...object and method
                            $method = $hook[1];
                            if (count($hook) > 2) {
                                //...object, method and data
                                $passData = true;
                                $data = $hook[2];
                            }
                        }
                    }
                    //...function
                    else if (is_string($hook[0])) {
                        $function = $hook[0];
                        if ($count > 1) {
                            //...function with data
                            $passData = true;
                            $data = $hook[1];
                        }
                    }
                }
            }

            /* If defined, add data to the arguments array */
            if ($passData) {
                $args = array_merge(array($data), $args);
            }

            if (isset($object)) {
                $callback = array($object, $method);
            }
            else if (false !== ($pos = strpos($function, '::'))) {
                $callback = array(substr($function, 0, $pos), substr($function, $pos + 2));
            }
            else {
                $callback = $function;
            }

            if (is_callable($callback)) {
                $ret = call_user_func_array($callback, $args);
            }
            else {
                $functionName = CKFinder_Connector_Core_Hooks::_printCallback($callback);
                $errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_CUSTOM_ERROR,
                "CKFinder failed to call a hook: " . $functionName);
                return false;
            }

            //String return is a custom error
            if (is_string($ret)) {
                $errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_CUSTOM_ERROR, $ret);
                return false;
            }
            //hook returned an error code, user error codes start from 50000
            //error codes are important because this way it is possible to create multilanguage extensions
            //TODO: two custom extensions may be popular and return the same error codes
            //recomendation: create a function that calculates the error codes starting number
            //for an extension, a pool of 100 error codes for each extension should be safe enough
            else if (is_int($ret)) {
                $errorHandler->throwError($ret);
                return false;
            }
            //no value returned
            else if( $ret === null ) {
                $functionName = CKFinder_Connector_Core_Hooks::_printCallback($callback);
                $errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_CUSTOM_ERROR,
                "CKFinder extension returned an invalid value (null)." .
                "Hook " . $functionName . " should return a value.");
                return false;
            }
            else if (!$ret) {
                return false;
            }
        }

        return true;
    }

    /**
     * Print user friendly name of a callback
     *
     * @param mixed $callback
     * @return string
     */
    public static function _printCallback($callback)
    {
        if (is_array($callback)) {
            if (is_object($callback[0])) {
                $className = get_class($callback[0]);
            } else {
                $className = strval($callback[0]);
            }
            $functionName = $className . '::' . strval($callback[1]);
        }
        else {
            $functionName = strval($callback);
        }
        return $functionName;
    }
}
