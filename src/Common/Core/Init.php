<?php

namespace Common\Core;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This class will initiate the application
 *
 * @author Ghazi Triki <ghazi.triki@inhanx.com>
 */
abstract class Init extends \KernelLoader
{
    /**
     * Current type
     *
     * @var    string
     */
    protected $type;

    /**
     * Allowed types
     *
     * @var array
     */
    protected $allowedTypes;

    /**
     * @param string $type The type of init to load, possible values are: frontend, frontend_ajax, frontend_js.
     */
    public function initialize($type)
    {
        $type = (string) $type;

        // check if this is a valid type
        if (!in_array($type, $this->allowedTypes)) {
            exit('Invalid init-type');
        }
        $this->type = $type;

        // set a default timezone if no one was set by PHP.ini
        if (ini_get('date.timezone') == '') {
            date_default_timezone_set('Europe/Brussels');
        }

        // get last modified time for globals
        $lastModifiedTime = @filemtime(PATH_WWW . '/app/config/parameters.yml');

        // reset last modified time if needed when invalid or debug is active
        if ($lastModifiedTime === false || $this->getContainer()->getParameter('kernel.debug')) {
            $lastModifiedTime = time();
        }

        // define as a constant
        defined('LAST_MODIFIED_TIME') || define('LAST_MODIFIED_TIME', $lastModifiedTime);

        $this->setDebugging();
    }

    /**
     * A custom error-handler so we can handle warnings about undefined labels
     *
     * @param int    $errorNumber The level of the error raised, as an integer.
     * @param string $errorString The error message, as a string.
     * @return null|false
     */
    public static function errorHandler($errorNumber, $errorString)
    {
        $errorString = (string) $errorString;
        // is this an undefined index?
        if (mb_substr_count($errorString, 'Undefined index:') > 0) {
            // cleanup
            $index = trim(str_replace('Undefined index:', '', $errorString));

            // get the type
            $type = mb_substr($index, 0, 3);

            // is the index locale?
            if (in_array($type, array('act', 'err', 'lbl', 'msg'))) {
                echo '{$' . $index . '}';
            } else {
                // return false, so the standard error handler isn't bypassed
                return false;
            }
        } else {
            // return false, so the standard error handler isn't bypassed
            return false;
        }
    }

    /**
     * This method will be called by the Spoon Exception handler and is specific for exceptions thrown in AJAX-actions
     *
     * @param object $exception The exception that was thrown.
     * @param string $output    The output that should be mailed.
     */
    public static function exceptionAJAXHandler($exception, $output)
    {
        header('content-type: application/json');
        $response = array(
            'code' => ($exception->getCode() != 0) ? $exception->getCode() : 500,
            'message' => $exception->getMessage()
        );
        echo json_encode($response);
        exit;
    }

    /**
     * This method will be called by the Spoon Exception handler
     *
     * @param object $exception The exception that was thrown.
     * @param string $output    The output that should be mailed.
     */
    public static function exceptionHandler($exception, $output)
    {
        $output = (string) $output;

        // mail it?
        if (self::getContainer()->getParameter('fork.debug_email') != '') {
            $headers = "MIME-Version: 1.0\n";
            $headers .= "Content-type: text/html; charset=iso-8859-15\n";
            $headers .= "X-Priority: 3\n";
            $headers .= "X-MSMail-Priority: Normal\n";
            $headers .= "X-Mailer: SpoonLibrary Webmail\n";
            $headers .= "From: Spoon Library <no-reply@spoon-library.com>\n";

            // send email
            @mail(self::getContainer()->getParameter('fork.debug_email'), 'Exception Occurred (' . SITE_DOMAIN . ')', $output, $headers);
        }

        echo '<html><body>Something went wrong.</body></html>';
        exit;
    }

    /**
     * @inheritdoc
     */
    protected function setDebugging()
    {
        if ($this->getContainer()->getParameter('kernel.debug') === false) {
            // set error reporting as low as possible
            error_reporting(0);

            // don't show error on the screen
            ini_set('display_errors', 'Off');

            // add callback for the spoon exceptionhandler
            switch ($this->type) {
                case 'BackendAjax':
                    \Spoon::setExceptionCallback(__CLASS__ . '::exceptionAJAXHandler');
                    break;

                default:
                    \Spoon::setExceptionCallback(__CLASS__ . '::exceptionHandler');
            }
        }
    }
}
