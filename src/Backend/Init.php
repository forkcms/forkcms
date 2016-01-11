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
class Init extends \Common\Core\Init
{
    /**
     * @inheritdoc
     */
    protected $allowedTypes = array('Backend', 'BackendAjax', 'BackendCronjob', 'Console');

    /**
     * @inheritdoc
     */
    public function initialize($type)
    {
        parent::initialize($type);

        \SpoonFilter::disableMagicQuotes();
    }

    /**
     * Define paths
     */
    protected function definePaths()
    {
        // general paths
        defined('BACKEND_PATH') || define('BACKEND_PATH', PATH_WWW . '/src/Backend');
        defined('BACKEND_CACHE_PATH') || define('BACKEND_CACHE_PATH', BACKEND_PATH . '/Cache');
        defined('BACKEND_CORE_PATH') || define('BACKEND_CORE_PATH', BACKEND_PATH . '/Core');
        defined('BACKEND_MODULES_PATH') || define('BACKEND_MODULES_PATH', BACKEND_PATH . '/Modules');

        defined('FRONTEND_PATH') || define('FRONTEND_PATH', PATH_WWW . '/src/Frontend');
        defined('FRONTEND_CACHE_PATH') || define('FRONTEND_CACHE_PATH', FRONTEND_PATH . '/Cache');
        defined('FRONTEND_CORE_PATH') || define('FRONTEND_CORE_PATH', FRONTEND_PATH . '/Core');
        defined('FRONTEND_MODULES_PATH') || define('FRONTEND_MODULES_PATH', FRONTEND_PATH . '/Modules');
        defined('FRONTEND_FILES_PATH') || define('FRONTEND_FILES_PATH', FRONTEND_PATH . '/Files');
    }

    /**
     * @inheritdoc
     */
    protected function defineURLs()
    {
        defined('BACKEND_CORE_URL') || define('BACKEND_CORE_URL', '/src/' . APPLICATION . '/Core');
        defined('BACKEND_CACHE_URL') || define('BACKEND_CACHE_URL', '/src/' . APPLICATION . '/Cache');
        defined('FRONTEND_FILES_URL') || define('FRONTEND_FILES_URL', '/src/Frontend/Files');
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
        $debugEmail = self::getContainer()->getParameter('fork.debug_email');

        // mail it?
        if ($debugEmail != '') {
            $headers = "MIME-Version: 1.0\n";
            $headers .= "Content-type: text/html; charset=iso-8859-15\n";
            $headers .= "X-Priority: 3\n";
            $headers .= "X-MSMail-Priority: Normal\n";
            $headers .= "X-Mailer: SpoonLibrary Webmail\n";
            $headers .= "From: Spoon Library <no-reply@spoon-library.com>\n";

            @mail($debugEmail, 'Exception Occured (' . SITE_DOMAIN . ')', $output, $headers);
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
}
