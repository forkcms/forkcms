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

namespace CKSource\CKFinder\Backend\Adapter;

use CKSource\CKFinder\Acl\Acl;
use CKSource\CKFinder\Acl\Permission;
use CKSource\CKFinder\Backend\Backend;
use CKSource\CKFinder\Exception\AccessDeniedException;
use CKSource\CKFinder\Exception\FolderNotFoundException;
use CKSource\CKFinder\Filesystem\Path;
use CKSource\CKFinder\ResourceType\ResourceType;
use CKSource\CKFinder\Utils;
use League\Flysystem\Config as FSConfig;

/**
 * Local file system adapter.
 *
 * A wrapper class for \League\Flysystem\Adapter\Local with
 * additions for `chmod` permissions management and conversions
 * between the file system and connector file name encoding.
 */
class Local extends \League\Flysystem\Adapter\Local
{
    /**
     * Backend configuration node.
     *
     * @var array $backendConfig
     */
    protected $backendConfig;

    /**
     * Constructor.
     *
     * @param array $backendConfig
     *
     * @throws \Exception if the root folder is not writable
     */
    public function __construct(array $backendConfig)
    {
        $this->backendConfig = $backendConfig;

        if (!isset($backendConfig['root']) || empty($backendConfig['root'])) {
            $baseUrl = $backendConfig['baseUrl'];
            $baseUrl = preg_replace("|^http(s)?://[^/]+|i", "", $baseUrl);
            $backendConfig['root'] = Path::combine(Utils::getRootPath(), Utils::decodeURLParts($baseUrl));
        }

        if (!is_dir($backendConfig['root'])) {
            @mkdir($backendConfig['root'], $backendConfig['chmodFolders'], true);
            if (!is_dir($backendConfig['root'])) {
                throw new FolderNotFoundException(sprintf('The root folder of backend "%s" not found (%s)', $backendConfig['name'], $backendConfig['root']));
            }
        }

        if (!is_readable($backendConfig['root'])) {
            throw new AccessDeniedException(sprintf('The root folder of backend "%s" is not readable (%s)', $backendConfig['name'], $backendConfig['root']));
        }

        parent::__construct($backendConfig['root']);
    }

    /**
     * Creates a directory.
     *
     * @param string   $dirname
     * @param FSConfig $config
     *
     * @return array|bool|false
     *
     */
    public function createDir($dirname, FSConfig $config)
    {
        $location = $this->applyPathPrefix($dirname);
        $umask = umask(0);

        $chmodFolders = $this->backendConfig['chmodFolders'];

        if (!is_dir($location) && !mkdir($location, $chmodFolders, true)) {
            $return = false;
        } else {
            $return = array('path' => $dirname, 'type' => 'dir');
        }

        umask($umask);

        return $return;
    }

    /**
     * Writes a file.
     *
     * @param string   $path
     * @param string   $contents
     * @param FSConfig $config
     *
     * @return array|bool
     */
    public function write($path, $contents, FSConfig $config)
    {
        $location = $this->applyPathPrefix($path);
        $this->ensureDirectory(dirname($location));

        $result = parent::write($path, $contents, $config);

        $chmodFiles = $this->backendConfig['chmodFiles'];

        $oldUmask = umask(0);
        chmod($location, $chmodFiles);
        umask($oldUmask);

        return $result;
    }

    /**
     * Writes a file using stream.
     *
     * @param string   $path
     * @param resource $resource
     * @param FSConfig $config
     *
     * @return array|bool
     */
    public function writeStream($path, $resource, FSConfig $config)
    {
        $location = $this->applyPathPrefix($path);
        $this->ensureDirectory(dirname($location));

        $result = parent::writeStream($path, $resource, $config);

        $chmodFiles = $this->backendConfig['chmodFiles'];

        $oldUmask = umask(0);
        chmod($location, $chmodFiles);
        umask($oldUmask);

        return $result;
    }

    /**
     * Ensures that the root directory exists.
     *
     * @param string $root root directory path
     *
     * @return  string  real path to root
     */
    protected function ensureDirectory($root)
    {
        if (!is_dir($root)) {
            $oldUmask = umask(0);
            mkdir($root, $this->backendConfig['chmodFolders'], true);
            umask($oldUmask);
        }

        return realpath($root);
    }

    /**
     * Checks whether a file or directory is present.
     *
     * @param string $path
     *
     * @return bool
     */
    public function has($path)
    {
        $location = $this->applyPathPrefix($path);

        return is_file($location) || is_dir($location);
    }

    /**
     * Converts file or directory names to the file system encoding.
     *
     * @param string $fileName
     *
     * @return mixed|string
     */
    public function convertToFilesystemEncoding($fileName)
    {
        $encoding = $this->backendConfig['filesystemEncoding'];

        if (null === $encoding || strcasecmp($encoding, "UTF-8") == 0 || strcasecmp($encoding, "UTF8") == 0) {
            return $fileName;
        }

        if (!function_exists("iconv")) {
            if (strcasecmp($encoding, "ISO-8859-1") == 0 || strcasecmp($encoding, "ISO8859-1") == 0 || strcasecmp($encoding, "Latin1") == 0) {
                return str_replace("\0", "_", utf8_decode($fileName));
            } elseif (function_exists('mb_convert_encoding')) {
                /**
                 * @todo check whether charset is supported - mb_list_encodings
                 */
                $encoded = @mb_convert_encoding($fileName, $encoding, 'UTF-8');
                if (@mb_strlen($fileName, "UTF-8") != @mb_strlen($encoded, $encoding)) {
                    return str_replace("\0", "_", preg_replace("/[^[:ascii:]]/u", "_", $fileName));
                } else {
                    return str_replace("\0", "_", $encoded);
                }
            } else {
                return str_replace("\0", "_", preg_replace("/[^[:ascii:]]/u", "_", $fileName));
            }
        }

        $converted = @iconv("UTF-8", $encoding . "//IGNORE//TRANSLIT", $fileName);
        if ($converted === false) {
            return str_replace("\0", "_", preg_replace("/[^[:ascii:]]/u", "_", $fileName));
        }

        return $converted;
    }

    /**
     * Creates a stream for writing to a file.
     *
     * @param string $path
     *
     * @return resource
     */
    public function createWriteStream($path)
    {
        $location = $this->applyPathPrefix($path);
        $this->ensureDirectory(dirname($location));
        $chmodFiles = $this->backendConfig['chmodFiles'];

        if (!$stream = fopen($location, 'a+')) {
            return false;
        }

        $oldUmask = umask(0);
        chmod($location, $chmodFiles);
        umask($oldUmask);

        return $stream;
    }

    /**
     * Checks if the directory contains subdirectories.
     *
     * @param Backend      $backend
     * @param ResourceType $resourceType
     * @param string       $clientPath
     * @param Acl          $acl
     *
     * @return bool
     */
    public function containsDirectories(Backend $backend, ResourceType $resourceType, $clientPath, Acl $acl)
    {
        $location = rtrim($this->applyPathPrefix(Path::combine($resourceType->getDirectory(), $clientPath)), '/\\') . '/';

        if (!is_dir($location) || (false === $fh = @opendir($location))) {
            return false;
        }

        $hasChildren = false;
        $resourceTypeName = $resourceType->getName();
        $clientPath = rtrim($clientPath, '/\\') . '/';

        while (false !== ($filename = readdir($fh))) {
            if ($filename == '.' || $filename == '..') {
                continue;
            }

            if (is_dir($location . $filename)) {
                if (!$acl->isAllowed($resourceTypeName, $clientPath . $filename, Permission::FOLDER_VIEW)) {
                    continue;
                }
                if ($backend->isHiddenFolder($filename)) {
                    continue;
                }
                $hasChildren = true;
                break;
            }
        }

        closedir($fh);

        return $hasChildren;
    }
}
