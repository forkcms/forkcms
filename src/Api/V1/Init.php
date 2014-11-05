<?php

namespace API\V1;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This class will initiate the API.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
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
     * @param string $type The type of init to load, possible values: Backend, BackendAjax, BackendCronjob
     */
    public function initialize($type)
    {
        $allowedTypes = array('Api');
        $type = (string) $type;

        // check if this is a valid type
        if (!in_array($type, $allowedTypes)) {
            exit('Invalid init-type');
        }

        // set type
        $this->type = $type;

        // set a default timezone if no one was set by PHP.ini
        if (ini_get('date.timezone') == '') {
            date_default_timezone_set('Europe/Brussels');
        }

        $this->definePaths();
        $this->setDebugging();

        // get spoon
        require_once 'spoon/spoon.php';

        \SpoonFilter::disableMagicQuotes();
        $this->initSession();
    }

    /**
     * Define paths
     */
    private function definePaths()
    {
        define('API_CORE_PATH', PATH_WWW . '/' . APPLICATION);
        define('BACKEND_PATH', PATH_WWW . '/src/Backend');
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
     * A custom error-handler so we can handle warnings about undefined labels
     *
     * @param int    $errorNumber The level of the error raised, as an integer.
     * @param string $errorString The error message, as a string.
     * @return bool
     */
    public static function errorHandler($errorNumber, $errorString)
    {
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
        \SpoonHTTP::setHeaders('content-type: application/json');
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
        if (SPOON_DEBUG_EMAIL != '') {
            $headers = "MIME-Version: 1.0\n";
            $headers .= "Content-type: text/html; charset=iso-8859-15\n";
            $headers .= "X-Priority: 3\n";
            $headers .= "X-MSMail-Priority: Normal\n";
            $headers .= "X-Mailer: SpoonLibrary Webmail\n";
            $headers .= "From: Spoon Library <no-reply@spoon-library.com>\n";

            // send email
            @mail(SPOON_DEBUG_EMAIL, 'Exception Occurred (' . SITE_DOMAIN . ')', $output, $headers);
        }

        echo '<html><body>Something went wrong.</body></html>';
        exit;
    }

    /**
     * Start session
     */
    private function initSession()
    {
        \SpoonSession::start();
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
