<?php

namespace ForkCMS\Bundle\InstallerBundle\Service;

/**
 * Requirements checker
 */
class RequirementsChecker
{
    /**
     * Requirements error statuses
     */
    const STATUS_OK = 'success';
    const STATUS_WARNING = 'warning';
    const STATUS_ERROR = 'danger';

    /**
     * The root dir of our project
     *
     * @var string
     */
    private $rootDir;

    private $errors;

    /**
     * RequirementsChecker constructor.
     *
     * @param $rootDir
     */
    public function __construct($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * Are all requirements met?
     *
     * @return bool
     */
    public function passes()
    {
        return $this->checkRequirements();
    }

    /**
     * Get all errors created by the requirements test
     *
     * @return array
     */
    public function getErrors()
    {
        if (empty($this->errors)) {
            $this->checkRequirements();
        }

        return $this->errors;
    }

    /**
     * Are there any issues with status error?
     *
     * @return bool
     */
    public function hasErrors()
    {
        if (empty($this->errors)) {
            $this->checkRequirements();
        }

        return in_array(self::STATUS_ERROR, $this->errors);
    }

    /**
     * Are there any issues with status warning?
     */
    public function hasWarnings()
    {
        if (empty($this->errors)) {
            $this->checkRequirements();
        }

        return in_array(self::STATUS_WARNING, $this->errors);
    }

    /**
     * Check all requirements and returns if everything has passed.
     *
     * @return bool
     */
    protected function checkRequirements()
    {
        $this->checkPhpVersion();
        $this->checkPhpExtensions();
        $this->checkPhpIniSettings();
        $this->checkAvailableFunctions();

        $this->checkSubFolder();

        $this->checkFilePermissions();
        $this->checkAvailableFiles();

        $this->checkApacheRewrites();

        // error status
        return !$this->hasErrors() && !$this->hasWarnings();
    }

    /*
     * At first we're going to check to see if the PHP version meets the minimum
     * requirements for Fork CMS. We require at least PHP 7.0.0, because we don't
     * want to be responsible for security issues in PHP itself.
     *
     * We follow this timeline: http://php.net/supported-versions.php
     */
    protected function checkPhpVersion()
    {
        $this->checkRequirement(
            'phpVersion',
            version_compare(PHP_VERSION, '7.0.0', '>='),
            self::STATUS_ERROR
        );
    }

    /**
     * A couple extensions need to be loaded in order to be able to use Fork CMS. Without these
     * extensions, we can't guarantee that everything will work.
     */
    protected function checkPhpExtensions()
    {
        $pcreVersion = defined('PCRE_VERSION') ? (float) PCRE_VERSION : null;
        $extensionsArray = array(
            'extensionCURL' => extension_loaded('curl'),
            'extensionLibXML' => extension_loaded('libxml'),
            'extensionDOM' => extension_loaded('dom'),
            'extensionSimpleXML' => extension_loaded('SimpleXML'),
            'extensionSPL' => extension_loaded('SPL'),
            'extensionPDO' => extension_loaded('PDO'),
            'extensionPDOMySQL' => extension_loaded('PDO') && in_array('mysql', \PDO::getAvailableDrivers()),
            'extensionMBString' => extension_loaded('mbstring'),
            'extensionIconv' => extension_loaded('iconv'),
            'extensionGD2' => extension_loaded('gd') && function_exists('gd_info'),
            'extensionJSON' => extension_loaded('json'),
            'extensionPCRE' => (extension_loaded('pcre') && (null !== $pcreVersion && $pcreVersion > 8.0)),
            'extensionIntl' => extension_loaded('intl'),
        );

        // not installed extensions give an error
        foreach ($extensionsArray as $errorName => $requirement) {
            $this->checkRequirement($errorName, $requirement, self::STATUS_ERROR);
        }
    }

    /**
     * A couple of php.ini settings should be configured in a specific way to make sure that
     * they don't intervene with Fork CMS.
     */
    protected function checkPhpIniSettings()
    {
        $this->checkRequirement('settingsOpenBasedir', ini_get('open_basedir') == '', self::STATUS_WARNING);
        $this->checkRequirement(
            'settingsDateTimezone',
            (ini_get('date.timezone') == '' || (in_array(
                date_default_timezone_get(),
                \DateTimeZone::listIdentifiers()
            ))),
            self::STATUS_WARNING
        );
    }

    /**
     * Some functions should be available
     */
    protected function checkAvailableFunctions()
    {
        $this->checkRequirement('functionJsonEncode', function_exists('json_encode'), self::STATUS_ERROR);
        $this->checkRequirement('functionSessionStart', function_exists('session_start'), self::STATUS_ERROR);
        $this->checkRequirement('functionCtypeAlpha', function_exists('ctype_alpha'), self::STATUS_ERROR);
        $this->checkRequirement('functionTokenGetAll', function_exists('token_get_all'), self::STATUS_ERROR);
        $this->checkRequirement(
            'functionSimplexmlImportDom',
            function_exists('simplexml_import_dom'),
            self::STATUS_ERROR
        );
    }

    /**
     * Fork can't be installed in subfolders, so we should check that.
     */
    protected function checkSubFolder()
    {
        if (array_key_exists('REQUEST_URI', $_SERVER)) {
            $this->checkRequirement(
                'subfolder',
                (mb_substr($_SERVER['REQUEST_URI'], 0, 8) == '/install'),
                self::STATUS_ERROR
            );
        } else {
            $this->checkRequirement('subfolder', true, self::STATUS_ERROR);
        }
    }

    /**
     * Make sure the filesystem is prepared for the installation and everything can be read/
     * written correctly.
     */
    protected function checkFilePermissions()
    {
        $this->checkRequirement(
            'fileSystemBackendCache',
            $this->isRecursivelyWritable($this->rootDir . 'src/Backend/Cache/'),
            self::STATUS_ERROR
        );
        $this->checkRequirement(
            'fileSystemBackendModules',
            $this->isWritable($this->rootDir . 'src/Backend/Modules/'),
            self::STATUS_WARNING
        );
        $this->checkRequirement(
            'fileSystemFrontendCache',
            $this->isRecursivelyWritable($this->rootDir . 'src/Frontend/Cache/'),
            self::STATUS_ERROR
        );
        $this->checkRequirement(
            'fileSystemFrontendFiles',
            $this->isRecursivelyWritable($this->rootDir . 'src/Frontend/Files/'),
            self::STATUS_ERROR
        );
        $this->checkRequirement(
            'fileSystemFrontendModules',
            $this->isWritable($this->rootDir . 'src/Frontend/Modules/'),
            self::STATUS_WARNING
        );
        $this->checkRequirement(
            'fileSystemFrontendThemes',
            $this->isWritable($this->rootDir . 'src/Frontend/Themes/'),
            self::STATUS_WARNING
        );
        $this->checkRequirement(
            'fileSystemAppCache',
            $this->isRecursivelyWritable($this->rootDir . 'app/cache/'),
            self::STATUS_ERROR
        );
        $this->checkRequirement(
            'fileSystemAppLogs',
            $this->isRecursivelyWritable($this->rootDir . 'app/logs/'),
            self::STATUS_ERROR
        );
        $this->checkRequirement(
            'fileSystemAppConfig',
            $this->isWritable($this->rootDir . 'app/config/'),
            self::STATUS_ERROR
        );
    }

    protected function checkAvailableFiles()
    {
        $this->checkRequirement(
            'fileSystemParameters',
            file_exists($this->rootDir . 'app/config/parameters.yml.dist')
            && is_readable($this->rootDir . 'app/config/parameters.yml.dist'),
            self::STATUS_ERROR
        );
    }

    /**
     * Ensure that Apache .htaccess file is written and mod_rewrite does its job
     */
    protected function checkApacheRewrites()
    {
        $this->checkRequirement(
            'modRewrite',
            (bool) (getenv('MOD_REWRITE') || getenv('REDIRECT_MOD_REWRITE')),
            self::STATUS_WARNING
        );
    }

    /**
     * Check if a specific requirement is satisfied
     *
     * @param  string $name        The "name" of the check.
     * @param  bool   $requirement The result of the check.
     * @param  string $severity    The severity of the requirement.
     *
     * @return bool
     */
    protected function checkRequirement($name, $requirement, $severity = self::STATUS_ERROR)
    {
        // set status
        $this->errors[$name] = $requirement ? self::STATUS_OK : $severity;

        return $this->errors[$name] == self::STATUS_OK;
    }

    /**
     * Check if a directory and its sub-directories and its subdirectories and ... are writable.
     *
     * @param  string $path The path to check.
     *
     * @return bool
     */
    private function isRecursivelyWritable($path)
    {
        $path = rtrim((string) $path, '/');

        // check if path is writable
        if (!$this->isWritable($path)) {
            return false;
        }

        // loop child directories
        foreach ((array) scandir($path) as $file) {
            // no '.' and '..'
            if (($file != '.') && ($file != '..')) {
                // directory
                if (is_dir($path . '/' . $file)) {
                    // check if children are readable
                    if (!$this->isRecursivelyWritable($path . '/' . $file)) {
                        return false;
                    }
                }
            }
        }

        // we were able to read all sub-directories
        return true;
    }

    /**
     * Check if a directory is writable.
     * The default is_writable function has problems due to Windows ACLs "bug"
     *
     * @param  string $path The path to check.
     *
     * @return bool
     */
    private function isWritable($path)
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
