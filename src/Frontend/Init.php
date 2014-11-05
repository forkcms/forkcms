<?php

namespace Frontend;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This class will initiate the frontend-application
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
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
     * @param string $type The type of init to load, possible values are: frontend, frontend_ajax, frontend_js.
     */
    public function initialize($type)
    {
        $allowedTypes = array('Frontend', 'FrontendAjax');
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

        // reset last modified time if needed (SPOON_DEBUG is enabled or we don't get a decent timestamp)
        if ($lastModifiedTime === false || \Spoon::getDebug()) {
            $lastModifiedTime = time();
        }

        // define as a constant
        define('LAST_MODIFIED_TIME', $lastModifiedTime);

        $this->definePaths();
        $this->defineURLs();
        $this->setDebugging();

        // require spoon
        require_once 'spoon/spoon.php';

        $this->requireFrontendClasses();
        \SpoonFilter::disableMagicQuotes();
    }

    /**
     * Define paths
     */
    private function definePaths()
    {
        // general paths
        define('FRONTEND_PATH', PATH_WWW . '/src/' . APPLICATION);
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
        define('FRONTEND_CORE_URL', '/src/' . APPLICATION . '/Core');
        define('FRONTEND_CACHE_URL', '/src/' . APPLICATION . '/Cache');
        define('FRONTEND_FILES_URL', '/src/' . APPLICATION . '/Files');
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

        // is this an undefined index?
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
            // e-mail headers
            $headers = "MIME-Version: 1.0\n";
            $headers .= "Content-type: text/html; charset=iso-8859-15\n";
            $headers .= "X-Priority: 3\n";
            $headers .= "X-MSMail-Priority: Normal\n";
            $headers .= "X-Mailer: SpoonLibrary Webmail\n";
            $headers .= "From: Spoon Library <no-reply@spoon-library.com>\n";

            // send email
            @mail(SPOON_DEBUG_EMAIL, 'Exception Occurred (' . SITE_DOMAIN . ')', $output, $headers);
        }

        // build HTML for nice error
        $html = '<html><body>Something went wrong.</body></html>';

        // output
        echo $html;
        exit;
    }

    /**
     * Require all needed classes
     */
    private function requireFrontendClasses()
    {
        switch ($this->type) {
            case 'Frontend':
            case 'FrontendAjax':
                require_once FRONTEND_CORE_PATH . '/Engine/TemplateCustom.php';
                require_once FRONTEND_PATH . '/Modules/Tags/Engine/Model.php';
                break;
        }
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
