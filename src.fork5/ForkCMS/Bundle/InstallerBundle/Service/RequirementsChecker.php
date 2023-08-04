<?php

namespace ForkCMS\Bundle\InstallerBundle\Service;

use ForkCMS\Bundle\InstallerBundle\Requirement\Requirement;
use ForkCMS\Bundle\InstallerBundle\Requirement\RequirementCategory;
use ForkCMS\Bundle\InstallerBundle\Requirement\RequirementStatus;

/**
 * Requirements checker
 */
final class RequirementsChecker
{
    /**
     * The root dir of our project
     *
     * @var string
     */
    private $rootDir;

    /**
     * @var RequirementCategory[]
     */
    private $requirementCategories;

    public function __construct(string $rootDir)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * Are all requirements met?
     *
     * @return bool
     */
    public function passes(): bool
    {
        return $this->checkRequirements();
    }

    /**
     * Get all requirements by category after running the tests
     *
     * @return RequirementCategory[]
     */
    public function getRequirementCategories(): array
    {
        if (empty($this->requirementCategories)) {
            $this->checkRequirements();
        }

        return $this->requirementCategories;
    }

    /**
     * Are there any issues with status error?
     *
     * @return bool
     */
    public function hasErrors(): bool
    {
        if (empty($this->requirementCategories)) {
            $this->checkRequirements();
        }

        return in_array(
            true,
            array_map(
                function (RequirementCategory $requirementCategory) {
                    return $requirementCategory->hasErrors();
                },
                $this->requirementCategories
            ),
            true
        );
    }

    /**
     * Are there any issues with status warning?
     *
     * @return bool
     */
    public function hasWarnings(): bool
    {
        if (empty($this->requirementCategories)) {
            $this->checkRequirements();
        }

        return in_array(
            true,
            array_map(
                function (RequirementCategory $requirementCategory) {
                    return $requirementCategory->hasWarnings();
                },
                $this->requirementCategories
            ),
            true
        );
    }

    /**
     * Check all requirements and returns if everything has passed.
     *
     * @return bool
     */
    private function checkRequirements(): bool
    {
        $this->requirementCategories = [
            $this->checkWebServer(),
            $this->checkPHPExtensions(),
            $this->checkPHPIniSettings(),
            $this->checkAvailableFunctions(),
            $this->checkRequiredPermissionsAndFiles(),
        ];

        // error status
        return !$this->hasErrors() && !$this->hasWarnings();
    }

    private function checkWebServer(): RequirementCategory
    {
        $reasoningBehindTheMinimumPHPVersion = 'At this moment we require php 7.4 as we follow the <a href="http://php.net/supported-versions.php">supported versions timeline of php</a>';

        return new RequirementCategory(
            'Web server',
            Requirement::check(
                'php version',
                version_compare(PHP_VERSION, '7.4.0', '>='),
                'Your server is running at least php 7.4.0. <br>' . $reasoningBehindTheMinimumPHPVersion,
                'PHP version must be at least 7.4.0, Before using Fork CMS, upgrade your PHP installation, preferably to the latest version.<br>' . $reasoningBehindTheMinimumPHPVersion,
                RequirementStatus::error()
            ),
            Requirement::check(
                'subfolder',
                // If we don't know for sure but we shall assume that it isn't in a subfolder
                array_key_exists('REQUEST_URI', $_SERVER)
                    ? mb_substr($_SERVER['REQUEST_URI'], 0, 8) === '/install' : true,
                'Fork CMS is as far as we can detect not running is a subfolder',
                'Fork CMS can\'t be installed in subfolders',
                RequirementStatus::error()
            ),
            Requirement::check(
                'mod_rewrite',
                php_sapi_name() === "cli" || (bool) (getenv('MOD_REWRITE') || getenv('REDIRECT_MOD_REWRITE') || strtolower($_SERVER['HTTP_MOD_REWRITE'] ?? 'Off') === 'on'),
                'Fork CMS is able to rewrite the urls using mod_rewrite',
                'Fork CMS will not be able to run if mod_rewrite can not be applied. Please make sure that the .htaccess file is present (the file starts with a dot, so it may be hidden on your filesystem), being read (AllowOverride directive) and the mod_rewrite module is enabled in Apache. If you are installing Fork CMS on another web server than Apache, make sure you have manually configured your web server to properly rewrite urls.
                 More information can be found in our <a href="http://www.fork-cms.com/knowledge-base/detail/fork-cms-and-webservers" title="Fork CMS and web servers">knowledge base</a>.
                 If you are certain that your server is well configured, you may proceed the installation despite this warning.',
                RequirementStatus::warning()
            )
        );
    }

    private function checkPHPExtensions(): RequirementCategory
    {
        $pcreVersion = defined('PCRE_VERSION') ? (float) PCRE_VERSION : null;

        return new RequirementCategory(
            'PHP extensions',
            ...array_map(
                function (array $extension) {
                    return Requirement::check(
                        $extension['name'],
                        $extension['check'],
                        $extension['message'],
                        $extension['message'],
                        RequirementStatus::error()
                    );
                },
                [
                    [
                        'name' => 'cURL',
                        'check' => extension_loaded('curl'),
                        'message' => 'cURL is a library that allows you to connect and communicate to many different type of servers.
                                      More information can be found on: <a href="http://php.net/curl">http://php.net/curl</a>.',
                    ],
                    [
                        'name' => 'libxml',
                        'check' => extension_loaded('libxml'),
                        'message' => 'libxml is a software library for parsing XML documents.
                                      More information can be found on: <a href="http://php.net/libxml">http://php.net/libxml</a>.',
                    ],
                    [
                        'name' => 'dom',
                        'check' => extension_loaded('DOM'),
                        'message' => 'The DOM extension allows you to operate on XML documents through the DOM API with PHP.
                                      More information can be found on: <a href="http://php.net/dom">http://php.net/dom</a>.',
                    ],
                    [
                        'name' => 'SimpleXML',
                        'check' => extension_loaded('SimpleXML'),
                        'message' => 'The SimpleXML extension provides a very simple and easily usable toolset to convert XML to an object that can be processed with normal property selectors and array iterators.
                                      More information can be found on: <a href="http://php.net/simplexml">http://php.net/simplexml</a>.',
                    ],
                    [
                        'name' => 'SPL',
                        'check' => extension_loaded('SPL'),
                        'message' => 'SPL is a collection of interfaces and classes that are meant to solve standard problems.
                                      More information can be found on: <a href="http://php.net/SPL">http://php.net/SPL</a>.',
                    ],
                    [
                        'name' => 'PDO',
                        'check' => extension_loaded('PDO'),
                        'message' => 'PDO provides a data-access abstraction layer, which means that, regardless of which database you\'re using, you use the same functions to issue queries and fetch data.
                                      More information can be found on: <a href="http://php.net/pdo">http://php.net/pdo</a>.',
                    ],
                    [
                        'name' => 'PDO MySQL driver',
                        'check' => extension_loaded('PDO') && in_array('mysql', \PDO::getAvailableDrivers()),
                        'message' => 'PDO_MYSQL is a driver that implements the PHP Data Objects (PDO) interface to enable access from PHP to MySQL databases.
                                      More information can be found on: <a href="http://www.php.net/manual/en/ref.pdo-mysql.php">http://www.php.net/manual/en/ref.pdo-mysql.php</a>.',
                    ],
                    [
                        'name' => 'mb_string',
                        'check' => extension_loaded('mbstring'),
                        'message' => 'mb_string provides multibyte specific string functions that help you deal with multibyte encodings in PHP. In addition to that, mb_string handles character encoding conversion between the possible encoding pairs. mb_string is designed to handle Unicode-based encodings.
                                      More information can be found on: <a href="http://php.net/mb_string">http://php.net/mb_string</a>.',
                    ],
                    [
                        'name' => 'iconv',
                        'check' => extension_loaded('iconv'),
                        'message' => 'This module contains an interface to iconv character set conversion facility. With this module, you can turn a string represented by a local character set into the one represented by another character set, which may be the Unicode character set.
                                      More information can be found on: <a href="http://php.net/iconv">http://php.net/iconv</a>.',
                    ],
                    [
                        'name' => 'GD2',
                        'check' => extension_loaded('gd') && function_exists('gd_info'),
                        'message' => 'PHP is not limited to creating just HTML output. It can also be used to create and manipulate image files in a variety of different image formats.
                                      More information can be found on: <a href="http://php.net/gd">http://php.net/gd</a>.',
                    ],
                    [
                        'name' => 'json',
                        'check' => extension_loaded('json'),
                        'message' => 'This extension implements the JavaScript Object Notation (JSON) data-interchange format. The decoding is handled by a parser based on the JSON_checker by Douglas Crockford.
                                      More information can be found on: <a href="http://php.net/json">http://php.net/json</a>.',
                    ],
                    [
                        'name' => 'PCRE (8.0+)',
                        'check' => extension_loaded('pcre') && version_compare($pcreVersion, '8.0', '>'),
                        'message' => 'The PCRE library is a set of functions that implement regular expression pattern matching using the same syntax and semantics as Perl 5, with just a few differences (see below). The current implementation corresponds to Perl 5.005. We require at least 8.0.
                                      More information can be found on: <a href="http://php.net/pcre">http://php.net/pcre</a>.',
                    ],
                    [
                        'name' => 'Intl',
                        'check' => extension_loaded('intl'),
                        'message' => 'Internationalization extension (Intl) is a wrapper for ICU library, enabling PHP programmers to perform UCA-conformant collation and date/time/number/currency formatting in their scripts.
                                      More information can be found on: <a href="http://php.net/intl">http://php.net/intl</a>.',
                    ],
                ]
            )
        );
    }

    private function checkPHPIniSettings(): RequirementCategory
    {
        return new RequirementCategory(
            'PHP ini-settings',
            Requirement::check(
                'Open Basedir',
                ini_get('open_basedir') === '',
                'You are not using open_basedir just like we recommend for forward compatibility',
                'For forward compatibility we highly recommend you not to use open_basedir.',
                RequirementStatus::warning()
            ),
            Requirement::check(
                'date.timezone',
                ini_get('date.timezone') === '' || in_array(
                    date_default_timezone_get(),
                    \DateTimeZone::listIdentifiers()
                ),
                'date.timezone is set',
                'date.timezone setting must be set. Make sure your default timezone is supported by your installation of PHP.
                 Check for typos in your php.ini file and have a look at the list of deprecated timezones at <a href="http://php.net/manual/en/timezones.others.php">http://php.net/manual/en/timezones.others.php</a>.',
                RequirementStatus::warning()
            )
        );
    }

    private function checkAvailableFunctions(): RequirementCategory
    {
        return new RequirementCategory(
            'Functions',
            Requirement::check(
                'json_encode',
                function_exists('json_encode'),
                'json_encode() is available',
                'json_encode() must be available, install and enable the JSON extension.',
                RequirementStatus::error()
            ),
            Requirement::check(
                'session_start',
                function_exists('session_start'),
                'session_start() is available',
                'session_start() must be available, install and enable the session extension.',
                RequirementStatus::error()
            ),
            Requirement::check(
                'ctype_alpha',
                function_exists('ctype_alpha'),
                'ctype_alpha() is available',
                'ctype_alpha() must be available, install and enable the ctype extension.',
                RequirementStatus::error()
            ),
            Requirement::check(
                'token_get_all',
                function_exists('token_get_all'),
                'token_get_all() is available',
                'token_get_all() must be available, install and enable the Tokenizer extension.',
                RequirementStatus::error()
            ),
            Requirement::check(
                'simplexml_import_dom',
                function_exists('simplexml_import_dom'),
                'simplexml_import_dom() is available',
                'simplexml_import_dom() must be available, install and enable the SimpleXML extension.',
                RequirementStatus::error()
            )
        );
    }

    private function checkRequiredPermissionsAndFiles(): RequirementCategory
    {
        $distParametersFile = $this->rootDir . 'app/config/parameters.yml.dist';

        return new RequirementCategory(
            'Required permissions and files',
            Requirement::check(
                $this->rootDir . 'src/Backend/Cache/*',
                $this->isRecursivelyWritable($this->rootDir . 'src/Backend/Cache/'),
                'In this location all files created by the backend will be stored. This location and all subdirectories are be writable.',
                'In this location all files created by the backend will be stored. This location and all subdirectories must be writable.',
                RequirementStatus::error()
            ),
            Requirement::check(
                $this->rootDir . 'src/Backend/Modules/',
                $this->isWritable($this->rootDir . 'src/Backend/Modules/'),
                'In this location modules will be installed.',
                'In this location modules will be installed. You can continue the installation, but installing a module will then require a manual upload.',
                RequirementStatus::warning()
            ),
            Requirement::check(
                $this->rootDir . 'src/Frontend/Cache/*',
                $this->isRecursivelyWritable($this->rootDir . 'src/Frontend/Cache/'),
                'In this location all files created by the frontend will be stored. This location and all subdirectories are be writable.',
                'In this location all files created by the frontend will be stored. This location and all subdirectories must be writable.',
                RequirementStatus::error()
            ),
            Requirement::check(
                $this->rootDir . 'src/Frontend/Files/*',
                $this->isRecursivelyWritable($this->rootDir . 'src/Frontend/Files/'),
                'In this location all files uploaded by the user/modules will be stored. This location and all subdirectories are be writable.',
                'In this location all files uploaded by the user/modules will be stored. This location and all subdirectories must be writable.',
                RequirementStatus::error()
            ),
            Requirement::check(
                $this->rootDir . 'src/Frontend/Modules/',
                $this->isWritable($this->rootDir . 'src/Frontend/Modules/'),
                'In this location modules will be installed.',
                'In this location modules will be installed. You can continue the installation, but installing a module will then require a manual upload.',
                RequirementStatus::warning()
            ),
            Requirement::check(
                $this->rootDir . 'src/Frontend/Themes/',
                $this->isWritable($this->rootDir . 'src/Frontend/Themes/'),
                'In this location themes will be installed.',
                'In this location themes will be installed. You can continue the installation, but installing a theme will then require a manual upload.',
                RequirementStatus::warning()
            ),
            Requirement::check(
                $this->rootDir . 'var/cache/*',
                $this->isWritable($this->rootDir . 'var/cache/'),
                'In this location the global cache will be stored. This location and all subdirectories are be writable.',
                'In this location the global cache will be stored. This location and all subdirectories must be writable.',
                RequirementStatus::error()
            ),
            Requirement::check(
                $this->rootDir . 'var/logs/*',
                $this->isWritable($this->rootDir . 'var/logs/'),
                'In this location the global logs will be stored. This location and all subdirectories are be writable.',
                'In this location the global logs will be stored. This location and all subdirectories must be writable.',
                RequirementStatus::error()
            ),
            Requirement::check(
                $this->rootDir . 'app/config/*',
                $this->isWritable($this->rootDir . 'app/config/'),
                'In this location the global configuration will be stored. This location and all subdirectories are be writable.',
                'In this location the global configuration will be stored. This location and all subdirectories must be writable.',
                RequirementStatus::error()
            ),
            Requirement::check(
                $distParametersFile,
                file_exists($distParametersFile) && is_readable($distParametersFile),
                'This file is used to create the global configuration file.',
                'This file is used to create the global configuration file. This file must be readable.',
                RequirementStatus::error()
            )
        );
    }

    /**
     * Check if a directory and its sub-directories and its subdirectories and ... are writable.
     *
     * @param string $path The path to check.
     *
     * @return bool
     */
    private function isRecursivelyWritable(string $path): bool
    {
        $path = rtrim((string) $path, '/');

        // check if path is writable
        if (!$this->isWritable($path)) {
            return false;
        }

        // loop child directories
        foreach ((array) scandir($path) as $file) {
            // no '.' and '..'
            if ($file === '.' || $file === '..') {
                continue;
            }

            // we only check directories
            if (!is_dir($path . '/' . $file)) {
                continue;
            }

            // check if children are readable
            if (!$this->isRecursivelyWritable($path . '/' . $file)) {
                return false;
            }
        }

        // we were able to read all sub-directories
        return true;
    }

    /**
     * Check if a directory is writable.
     * The default is_writable function has problems due to Windows ACLs "bug"
     *
     * @param string $path The path to check.
     *
     * @return bool
     */
    private function isWritable(string $path): bool
    {
        // redefine argument
        $path = rtrim((string) $path, '/');

        // create random file
        $file = uniqid('', true) . '.tmp';

        $return = @file_put_contents($path . '/' . $file, 'temporary file', FILE_APPEND);

        if ($return === false) {
            return false;
        }

        // unlink the random file
        @unlink($path . '/' . $file);

        // return
        return true;
    }
}
