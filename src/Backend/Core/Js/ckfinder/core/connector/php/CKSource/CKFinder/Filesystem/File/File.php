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

namespace CKSource\CKFinder\Filesystem\File;

use CKSource\CKFinder\Backend\Backend;
use CKSource\CKFinder\Cache\CacheManager;
use CKSource\CKFinder\CKFinder;
use CKSource\CKFinder\Config;
use CKSource\CKFinder\Filesystem\Path;

/**
 * The File class.
 *
 * Base class for processed files.
 */
abstract class File
{
    /**
     * File name.
     *
     * @var string $fileName
     */
    protected $fileName;

    /**
     * CKFinder configuration.
     *
     * @var Config $config
     */
    protected $config;

    /**
     * @var CKFinder $app
     */
    protected $app;

    /**
     * Constructor.
     *
     * @param string   $fileName
     * @param CKFinder $app
     */
    public function __construct($fileName, CKFinder $app)
    {
        $this->fileName = $fileName;
        $this->config = $app['config'];
        $this->app = $app;
    }

    /**
     * Validates current file name.
     *
     * @return bool `true` if the file name is valid.
     */
    public function hasValidFilename()
    {
        return static::isValidName($this->fileName, $this->config->get('disallowUnsafeCharacters'));
    }

    /**
     * Returns current file name.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->fileName;
    }

    /**
     * Returns current file extension.
     *
     * @return string
     */
    public function getExtension()
    {
        return strtolower(pathinfo($this->fileName, PATHINFO_EXTENSION));
    }

    /**
     * Returns a list of current file extensions.
     *
     * For example for a file named `file.foo.bar.baz` it will return an array containing
     * `['foo', 'bar', 'baz']`.
     *
     * @param null $newFileName the file name to check if it is different than the current file name (for example for validation of
     *                          a new file name in edited files).
     *
     * @return array
     */
    public function getExtensions($newFileName = null)
    {
        $fileName = $newFileName ?: $this->fileName;

        if (strpos($fileName, '.') === false) {
            return true;
        }

        $pieces = explode('.', $fileName);

        array_shift($pieces); // Remove file base name

        return array_map('strtolower', $pieces);
    }

    /**
     * Renames the current file by adding a number to the file name.
     *
     * Renaming is done by adding a number in parenthesis provided that the file name does
     * not collide with any other file existing in the target backend/path.
     * For example, if the target backend path contains a file named `foo.txt`
     * and the current file name is `foo.txt`, this method will change the current file
     * name to `foo(1).txt`.
     *
     * @param Backend $backend target backend
     * @param string  $path    target backend-relative path
     *
     * @return bool `true` if file was renamed.
     */
    public function autorename(Backend $backend = null, $path = '')
    {
        $filePath = Path::combine($path, $this->fileName);

        if (!$backend->has($filePath)) {
            return false;
        }

        $pieces = explode('.', $this->fileName);
        $basename = array_shift($pieces);
        $extension = implode('.', $pieces);

        $i = 0;
        while (true) {
            $i++;
            $this->fileName = "{$basename}({$i}).{$extension}";

            $filePath = Path::combine($path, $this->fileName);

            if (!$backend->has($filePath)) {
                break;
            }
        }

        return true;
    }

    /**
     * Check whether `$fileName` is a valid file name. Returns `true` on success.
     *
     * @param string $fileName
     * @param bool   $disallowUnsafeCharacters
     *
     * @return boolean `true` if `$fileName` is a valid file name.
     */
    public static function isValidName($fileName, $disallowUnsafeCharacters = true)
    {
        if (null === $fileName || !strlen(trim($fileName)) || substr($fileName, -1, 1) == "." || false !== strpos($fileName, "..")) {
            return false;
        }

        if (preg_match(',[[:cntrl:]]|[/\\\\:\*\?\"\<\>\|],', $fileName)) {
            return false;
        }

        if ($disallowUnsafeCharacters) {
            if (strpos($fileName, ";") !== false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks if the current file has an image extension.
     *
     * @return bool `true` if the file name has an image extension.
     */
    public function isImage()
    {
        $imagesExtensions = array('gif', 'jpeg', 'jpg', 'png', 'psd', 'bmp', 'tiff', 'tif',
            'swc', 'iff', 'jpc', 'jp2', 'jpx', 'jb2', 'xbm', 'wbmp');

        return in_array($this->getExtension(), $imagesExtensions);
    }

    /**
     * Secures the file name from unsafe characters.
     *
     * @param string $fileName
     * @param bool   $disallowUnsafeCharacters
     *
     * @return string
     */
    public static function secureName($fileName, $disallowUnsafeCharacters)
    {
        $fileName = str_replace(array(":", "*", "?", "|", "/"), "_", $fileName);

        if ($disallowUnsafeCharacters) {
            $fileName = str_replace(";", "_", $fileName);
        }

        return $fileName;
    }

    /**
     * @return CacheManager
     */
    public function getCache()
    {
        return $this->app['cache'];
    }
}
