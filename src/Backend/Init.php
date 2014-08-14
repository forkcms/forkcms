<?php

namespace Backend;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This class will initiate the backend-application
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Matthias Mullie <forkcms@mullie.eu>
 */
class Init extends \KernelLoader
{
    /**
     * Current type
     *
     * @var    string
     */
    private $type;

    /**
     * @param string $type The type of init to load, possible values are:
     *                     Backend, BackendAjax, BackendCronjob.
     */
    public function initialize($type)
    {
        $allowedTypes = array('Backend', 'BackendAjax', 'BackendCronjob');
        $type = (string) $type;

        // check if this is a valid type
        if (!in_array($type, $allowedTypes)) {
            exit('Invalid init-type');
        }
        $this->type = $type;

        // set a default timezone if no one was set by PHP.ini
        if (ini_get('date.timezone') == '') {
            date_default_timezone_set('Europe/Brussels');
        }

        // get last modified time for globals
        $lastModifiedTime = @filemtime(PATH_WWW . '/app/config/parameters.yml');

        // reset lastmodified time if needed (SPOON_DEBUG is enabled or we don't get a decent timestamp)
        if ($lastModifiedTime === false || SPOON_DEBUG) {
            $lastModifiedTime = time();
        }

        // define as a constant
        define('LAST_MODIFIED_TIME', $lastModifiedTime);

        $this->definePaths();
        $this->defineURLs();
        $this->setDebugging();

        // require spoon
        require_once 'spoon/spoon.php';

        \SpoonFilter::disableMagicQuotes();
    }

    /**
     * Define paths
     */
    private function definePaths()
    {
        // general paths
        define('BACKEND_PATH', PATH_WWW . '/src/' . APPLICATION);
        define('BACKEND_CACHE_PATH', BACKEND_PATH . '/Cache');
        define('BACKEND_CORE_PATH', BACKEND_PATH . '/Core');
        define('BACKEND_MODULES_PATH', BACKEND_PATH . '/Modules');

        define('FRONTEND_PATH', PATH_WWW . '/src/Frontend');
        define('FRONTEND_CACHE_PATH', FRONTEND_PATH . '/Cache');
        define('FRONTEND_CORE_PATH', FRONTEND_PATH . '/Core');
        define('FRONTEND_MODULES_PATH', FRONTEND_PATH . '/Modules');
        define('FRONTEND_FILES_PATH', FRONTEND_PATH . '/Files');
    }

    /**
     * Define URLs
     */
    private function defineURLs()
    {
        define('BACKEND_CORE_URL', '/src/' . APPLICATION . '/Core');
        define('BACKEND_CACHE_URL', '/src/' . APPLICATION . '/Cache');
        define('FRONTEND_FILES_URL', '/src/Frontend/Files');
    }

    /**
     * A custom error-handler so we can handle warnings about undefined labels
     *
     * @param int    $errorNumber The level of the error raised, as an integer.
     * @param string $errorString The error message, as a string.
     * @return bool
     */
    public static function errorHandler($errorNumber, $errorString)
    {
        $errorString = (string) $errorString;
        if (mb_substr_count($errorString, 'Undefined index:') > 0) {
            $index = trim(str_replace('Undefined index:', '', $errorString));
            $type = mb_substr($index, 0, 3);
            if (in_array($type, array('act', 'err', 'lbl', 'msg'))) {
                echo '{$' . $index . '}';
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * This method will be called by the Spoon Exceptionhandler and is specific for exceptions thrown in AJAX-actions
     *
     * @param object $exception The exception that was thrown.
     * @param string $output    The output that should be mailed.
     */
    public static function exceptionAJAXHandler($exception, $output)
    {
        \SpoonHTTP::setHeaders('content-type: application/json');
        $response = array(
            'code' => ($exception->getCode() != 0) ? $exception->getCode() : 500,
            'message' => $exception->getMessage()
        );
        echo json_encode($response);
        exit;
    }

    /**
     * This method will be called by the Spoon Exceptionhandler
     *
     * @param object $exception The exception that was thrown.
     * @param string $output    The output that should be mailed.
     */
    public static function exceptionHandler($exception, $output)
    {
        $output = (string) $output;

        // mail it?
        if (SPOON_DEBUG_EMAIL != '') {
            $headers = "MIME-Version: 1.0\n";
            $headers .= "Content-type: text/html; charset=iso-8859-15\n";
            $headers .= "X-Priority: 3\n";
            $headers .= "X-MSMail-Priority: Normal\n";
            $headers .= "X-Mailer: SpoonLibrary Webmail\n";
            $headers .= "From: Spoon Library <no-reply@spoon-library.com>\n";

            @mail(SPOON_DEBUG_EMAIL, 'Exception Occured (' . SITE_DOMAIN . ')', $output, $headers);
        }

        // build HTML for nice error
        $html = '
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
                "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml">
                <head>
                    <title>Fork CMS - Error</title>
                    <style type="text/css" media="screen">

                        body {
                            background: #FFF;
                            font-family: Arial, sans-serif;
                            font-size: 13px;
                            text-align: center;
                            width: 75%;
                            margin: 0 auto;
                        }

                        p {
                            padding: 0 0 12px;
                            margin: 0;
                        }

                        h2 {
                            font-size: 20px;
                            margin: 0
                            padding: 0 0 10px;
                        }
                    </style>
                </head>
                <body>
                    <h2>Internal error</h2>
                    <p>
                        There was an internal error while processing your request.
                        We have been notified of this error and will resolve it
                        shortly. We\'re sorry for the inconvenience.
                    </p>
                </body>
            </html>
        ';

        echo $html;
        exit;
    }

    /**
     * Set debugging
     */
    private function setDebugging()
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
