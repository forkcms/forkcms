<?php

/*
 * CKFinder
 * ========
 * http://cksource.com/ckfinder
 * Copyright (C) 2007-2016, CKSource - Frederico Knabben. All rights reserved.
 *
 * The software, this file and its contents are subject to the CKFinder
 * License. Please read the license.txt file before using, installing, copying,
 * modifying or distribute this file or part of its contents. The contents of
 * this file is part of the Source Code of CKFinder.
 */

namespace CKSource\CKFinder;

use CKSource\CKFinder\Exception\InvalidConfigException;
use CKSource\CKFinder\Exception\InvalidResourceTypeException;

/**
 * The Config class.
 *
 * Contains all configuration options and a set of config helper methods.
 *
 * @copyright 2016 CKSource - Frederico Knabben
 */
class Config
{
    /**
     * An array containing configuration options.
     *
     * @var array $options
     */
    protected $options;

    /**
     * Constructor.
     *
     * Depending on the type of the parameter passed to this function,
     * config array is used directly or it is loaded from a file.
     *
     * <b>Important</b>: If you use a PHP file to store your config, remember to use
     *                   the <code>return</code> statement inside the file scope to return
     *                   the array.
     *
     * @param array|string $config
     *
     * @throws InvalidConfigException if config was not loaded properly.
     */
    public function __construct($config)
    {
        setlocale(LC_ALL, "en_US.utf8");

        // Check if default timezone was set
        try {
            new \DateTime();
        } catch (\Exception $e) {
            date_default_timezone_set('UTC');
        }

        if (is_string($config) && is_readable($config)) {
            $options = require $config;
        } else {
            $options = $config;
        }

        if (!is_array($options)) {
            throw new InvalidConfigException("Couldn't load configuration. Please check configuration file.");
        }

        $this->options = $this->mergeDefaultOptions($options);

        $this->validate();
        $this->process();
    }

    /**
     * Merges default or missing configuration options.
     *
     * @param array $options options passed to CKFinder
     *
     * @return array
     */
    protected function mergeDefaultOptions($options)
    {
        $defaults = array(
            'authentication' => function () {
                return false;
            },
            'licenseName' => '',
            'licenseKey'  => '',
            'privateDir'  => array(
                'backend' => 'default',
                'tags'    => '.ckfinder/tags',
                'logs'    => '.ckfinder/logs',
                'cache'   => '.ckfinder/cache',
                'thumbs'  => '.ckfinder/cache/thumbs'
            ),
            'images' => array(
                'maxWidth'  => 500,
                'maxHeight' => 400,
                'quality'   => 80,
                'sizes' => array(
                    'small'  => array('width' => 480, 'height' => 320, 'quality' => 80),
                    'medium' => array('width' => 600, 'height' => 480, 'quality' => 80),
                    'large'  => array('width' => 800, 'height' => 600, 'quality' => 80)
                ),
                'threshold' => array('pixels'=> 80, 'percent' => 10)
            ),
            'thumbnails' => array(
                'enabled' => true,
                'sizes' => array(
                    array('width' => '150', 'height' => '150', 'quality' => 80),
                    array('width' => '300', 'height' => '300', 'quality' => 80),
                    array('width' => '500', 'height' => '500', 'quality' => 80),
                ),
                'bmpSupported' => true,
            ),
            'backends' => array(
                array(
                    'name'               => 'default',
                    'adapter'            => 'local',
                    'baseUrl'            => '/userfiles/',
                    'chmodFiles'         => 0777,
                    'chmodFolders'       => 0777,
                    'filesystemEncoding' => 'UTF-8'
                ),
            ),
            'defaultResourceTypes' => '',
            'resourceTypes' =>array(
                array(
                    'name'              => 'Files',
                    'directory'         => 'files',
                    'maxSize'           => 0,
                    'allowedExtensions' => '7z,aiff,asf,avi,bmp,csv,doc,docx,fla,flv,gif,gz,gzip,jpeg,jpg,mid,mov,mp3,mp4,mpc,mpeg,mpg,ods,odt,pdf,png,ppt,pptx,pxd,qt,ram,rar,rm,rmi,rmvb,rtf,sdc,sitd,swf,sxc,sxw,tar,tgz,tif,tiff,txt,vsd,wav,wma,wmv,xls,xlsx,zip',
                    'deniedExtensions'  => '',
                    'backend'           => 'default'
                ),
                array(
                    'name'              => 'Images',
                    'directory'         => 'images',
                    'maxSize'           => 0,
                    'allowedExtensions' => 'bmp,gif,jpeg,jpg,png',
                    'deniedExtensions'  => '',
                    'backend'           => 'default'
                )
            ),
            'roleSessionVar' => 'CKFinder_UserRole',
            'accessControl' => array(
                array(
                    'role'          => '*',
                    'resourceType'  => '*',
                    'folder'        => '/',

                    'FOLDER_VIEW'        => true,
                    'FOLDER_CREATE'      => true,
                    'FOLDER_RENAME'      => true,
                    'FOLDER_DELETE'      => true,

                    'FILE_VIEW'          => true,
                    'FILE_CREATE'        => true,
                    'FILE_RENAME'        => true,
                    'FILE_DELETE'        => true,

                    'IMAGE_RESIZE'        => true,
                    'IMAGE_RESIZE_CUSTOM' => true
                ),
            ),
            'overwriteOnUpload'        => false,
            'checkDoubleExtension'     => true,
            'disallowUnsafeCharacters' => false,
            'secureImageUploads'       => true,
            'checkSizeAfterScaling'    => true,
            'htmlExtensions'           => array('html', 'htm', 'xml', 'js'),
            'hideFolders'              => array(".*", "CVS", "__thumbs"),
            'hideFiles'                => array(".*"),
            'forceAscii'               => false,
            'xSendfile'                => false,
            'debug'                    => false,
            'pluginsDirectory'         => __DIR__ . '/plugins',
            'plugins'                  => array(),
            'debugLoggers'            => array('ckfinder_log', 'error_log', 'firephp'),
            'tempDirectory' => sys_get_temp_dir(),
            'sessionWriteClose' => true,
            'csrfProtection' => true
        );

        $options = array_merge($defaults, $options);

        foreach (array('privateDir', 'images', 'thumbnails') as $key) {
            $options[$key] = array_merge($defaults[$key], $options[$key]);
        }

        $resourceTypeDefaults = array(
            'name'              => '',
            'directory'         => '',
            'maxSize'           => 0,
            'allowedExtensions' => '',
            'deniedExtensions'  => '',
            'backend'           => 'default'
        );

        foreach ($options['resourceTypes'] as &$resourceType) {
            $resourceType = array_merge($resourceTypeDefaults, $resourceType);
        }

        $localBackendDefaults = array(
            'chmodFiles'   => 0755,
            'chmodFolders' => 0755,
            'filesystemEncoding' => 'UTF-8'
        );

        foreach ($options['backends'] as &$backend) {
            if ($backend['adapter'] === 'local') {
                $backend = array_merge($localBackendDefaults, $backend);
            }
        }

        $cacheDefaults = array(
            'imagePreview' => 24 * 3600,
            'thumbnails'   => 24 * 3600 * 365,
            'proxyCommand' => 0
        );

        $options['cache'] = array_replace($cacheDefaults, isset($options['cache']) ? $options['cache'] : array());

        // #205 Backward compatibility for old debug_loggers option
        if (isset($options['debug_loggers'])) {
            $options['debugLoggers'] = $options['debug_loggers'];
        }

        return $options;
    }

    /**
     * Returns the configuration node under the path defined in the parameter.
     *
     * For easier access to nested configuration options the config `$name`
     * parameter can be passed also as a dot-separated path.
     * For example, to check if thumbnails are enabled you can use:
     *
     * $config->get('thumbnails.enabled')
     *
     * @param string $name config node name
     *
     * @return mixed config node value
     *
     */
    public function get($name)
    {
        if (isset($this->options[$name])) {
            return $this->options[$name];
        }

        $keys = explode('.', $name);
        $array = $this->options;

        do {
            $key = array_shift($keys);
            if (isset($array[$key])) {
                if ($keys) {
                    if (is_array($array[$key])) {
                        $array = $array[$key];
                    } else {
                        break;
                    }
                } else {
                    return $array[$key];
                }
            } else {
                break;
            }
        } while ($keys);

        return null;
    }

    /**
     * Validates the config array structure.
     *
     * @throws InvalidConfigException if config structure is invalid.
     */
    protected function validate()
    {
        $checkMissingNodes = function (array $required, array $actual, $prefix = '') {
            $missing = array_keys(array_diff_key(array_flip($required), $actual));

            if (!empty($missing)) {
                throw new InvalidConfigException(sprintf(
                    "CKFinder configuration doesn't contain all required fields. " .
                    "Please check configuration file. Missing fields: %s",
                    ($prefix ? "{$prefix}: " : '') . implode(', ', $missing)));
            }
        };

        $requiredRootNodes = array('authentication', 'licenseName', 'licenseKey', 'privateDir', 'images',
            'backends', 'defaultResourceTypes', 'resourceTypes', 'roleSessionVar', 'accessControl',
            'checkDoubleExtension', 'disallowUnsafeCharacters', 'secureImageUploads', 'checkSizeAfterScaling',
            'htmlExtensions', 'hideFolders', 'hideFiles', 'forceAscii', 'xSendfile', 'debug', 'pluginsDirectory', 'plugins');

        $checkMissingNodes($requiredRootNodes, $this->options);
        $checkMissingNodes(array('backend', 'tags', 'logs', 'cache', 'thumbs'), $this->options['privateDir'], '[privateDir]');
        $checkMissingNodes(array('maxWidth', 'maxHeight', 'quality'), $this->options['images'], '[images]');

        $backends = array();

        foreach ($this->options['backends'] as $i => $backendConfig) {
            $checkMissingNodes(array('name', 'adapter'), $backendConfig, "[backends][{$i}]");
            $backends[] = $backendConfig['name'];
        }

        foreach ($this->options['resourceTypes'] as $i => $resourceTypeConfig) {
            $checkMissingNodes(array('name', 'directory', 'maxSize', 'allowedExtensions', 'deniedExtensions', 'backend'),
                $resourceTypeConfig, "[resourceTypes][{$i}]");

            if (!in_array($resourceTypeConfig['backend'], $backends)) {
                throw new InvalidConfigException("Backend '{$resourceTypeConfig['backend']}' is not defined: [resourceTypes][{$i}]");
            }
        }

        foreach ($this->options['accessControl'] as $i => $aclConfig) {
            $checkMissingNodes(array('role', 'resourceType', 'folder'), $aclConfig, "[accessControl][{$i}]");
        }

        if (!is_callable($this->options['authentication'])) {
            throw new InvalidConfigException("CKFinder Authentication config field must be a PHP callable");
        }

        if (!is_writable($this->options['tempDirectory'])) {
            throw new InvalidConfigException("The temporary folder is not writable for CKFinder");
        }
    }

    /**
     * Processes the configuration array.
     */
    protected function process()
    {
        $this->options['defaultResourceTypes'] =
            array_filter(
                array_map('trim',
                    explode(',', $this->options['defaultResourceTypes'])
                ),
                'strlen');


        $formatToArray = function ($input) {
            $input = is_array($input) ? $input : explode(',', $input);

            return
                array_filter(
                    array_map('strtolower',
                        array_map('trim', $input)
                    ),
                    'strlen');
        };

        foreach ($this->options['resourceTypes'] as $resourceTypeKey => $resourceTypeConfig) {
            $resourceTypeConfig['allowedExtensions'] = $formatToArray($resourceTypeConfig['allowedExtensions']);
            $resourceTypeConfig['deniedExtensions'] = $formatToArray($resourceTypeConfig['deniedExtensions']);
            $resourceTypeConfig['maxSize'] = Utils::returnBytes((string) $resourceTypeConfig['maxSize']);

            $this->options['resourceTypes'][$resourceTypeConfig['name']] = $resourceTypeConfig;

            if ($resourceTypeKey !== $resourceTypeConfig['name']) {
                unset($this->options['resourceTypes'][$resourceTypeKey]);
            }
        }

        foreach ($this->options['backends'] as $backendKey => $backendConfig) {
            $this->options['backends'][$backendConfig['name']] = $backendConfig;

            if ($backendKey !== $backendConfig['name']) {
                unset($this->options['backends'][$backendKey]);
            }
        }

        $this->options['htmlExtensions'] = $formatToArray($this->options['htmlExtensions']);
    }

    /**
     * Returns the default resource types names.
     *
     * @return array
     */
    public function getDefaultResourceTypes()
    {
        return $this->options['defaultResourceTypes'];
    }

    /**
     * Returns all defined resource types names.
     *
     * @return array
     */
    public function getResourceTypes()
    {
        return array_keys($this->options['resourceTypes']);
    }

    /**
     * Returns the configuration node for a given resource type.
     *
     * @param string $resourceType resource type name
     *
     * @return array configuration node for the resource type
     *
     * @throws InvalidResourceTypeException if the resource type does not exist
     */
    public function getResourceTypeNode($resourceType)
    {
        if (array_key_exists($resourceType, $this->options['resourceTypes'])) {
            return $this->options['resourceTypes'][$resourceType];
        } else {
            throw new InvalidResourceTypeException("Invalid resource type: {$resourceType}");
        }
    }

    /**
     * Returns the regex used for hidden files check.
     * @return string
     */
    public function getHideFilesRegex()
    {
        static $hideFilesRegex;

        if (!isset($hideFilesRegex)) {
            $hideFilesConfig = $this->options['hideFiles'];

            if ($hideFilesConfig && is_array($hideFilesConfig)) {
                $hideFilesRegex = join("|", $hideFilesConfig);
                $hideFilesRegex = strtr($hideFilesRegex, array("?" => "__QMK__", "*" => "__AST__", "|" => "__PIP__"));
                $hideFilesRegex = preg_quote($hideFilesRegex, "/");
                $hideFilesRegex = strtr($hideFilesRegex, array("__QMK__" => ".", "__AST__" => ".*", "__PIP__" => "|"));
                $hideFilesRegex = "/^(?:" . $hideFilesRegex . ")$/uim";
            } else {
                $hideFilesRegex = "";
            }
        }

        return $hideFilesRegex;
    }

    /**
     * Returns the regex used for hidden folders check.
     * @return string
     */
    public function getHideFoldersRegex()
    {
        static $hideFoldersRegex;

        if (!isset($hideFoldersRegex)) {
            $hideFoldersConfig = $this->options['hideFolders'];

            if ($hideFoldersConfig && is_array($hideFoldersConfig)) {
                $hideFoldersRegex = join("|", $hideFoldersConfig);
                $hideFoldersRegex = strtr($hideFoldersRegex, array("?" => "__QMK__", "*" => "__AST__", "|" => "__PIP__"));
                $hideFoldersRegex = preg_quote($hideFoldersRegex, "/");
                $hideFoldersRegex = strtr($hideFoldersRegex, array("__QMK__" => ".", "__AST__" => ".*", "__PIP__" => "|"));
                $hideFoldersRegex = "/^(?:" . $hideFoldersRegex . ")$/uim";
            } else {
                $hideFoldersRegex = "";
            }
        }

        return $hideFoldersRegex;
    }

    /**
     * If the config node does not exist, creates the node with a given name and values.
     * Otherwise extends the config node with additional (default) values.
     *
     * @param string $nodeName
     * @param array  $values
     */
    public function extend($nodeName, array $values)
    {
        if (!isset($this->options[$nodeName])) {
            $this->options[$nodeName] = $values;
        } else {
            $this->options[$nodeName] = array_replace_recursive($values, $this->options[$nodeName]);
        }
    }

    /**
     * Returns the backend-relative private directory path.
     *
     * @param string $privateDirIdentifier
     *
     * @return mixed
     */
    public function getPrivateDirPath($privateDirIdentifier)
    {
        if (!array_key_exists($privateDirIdentifier, $this->options['privateDir'])) {
            throw new \InvalidArgumentException(sprintf('Private dir with identifier %s not found. Please check configuration file.', $privateDirIdentifier));
        }

        $privateDir = $this->options['privateDir'][$privateDirIdentifier];

        if (is_array($privateDir) && array_key_exists('path', $privateDir)) {
            return $privateDir['path'];
        }

        return $privateDir;
    }

    /**
     * Checks if the debug logger with a given name is enabled.
     * @param string $loggerName debug logger name
     *
     * @return bool `true` if enabled
     */
    public function isDebugLoggerEnabled($loggerName)
    {
        return in_array($loggerName, $this->options['debugLoggers']);
    }

    /**
     * Returns backend configuration by name.
     *
     * @param string $backendName
     *
     * @return array backend configuration node
     *
     * @throws \InvalidArgumentException
     */
    public function getBackendNode($backendName)
    {
        if (array_key_exists($backendName, $this->options['backends'])) {
            return $this->options['backends'][$backendName];
        } else {
            throw new \InvalidArgumentException(sprintf('Backend %s not found. Please check configuration file.', $backendName));
        }
    }
}
