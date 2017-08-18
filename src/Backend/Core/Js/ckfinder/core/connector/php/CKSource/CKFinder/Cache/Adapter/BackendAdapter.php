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

namespace CKSource\CKFinder\Cache\Adapter;

use CKSource\CKFinder\Backend\Backend;
use CKSource\CKFinder\Filesystem\Path;

/**
 * The BackendAdapter class.
 */
class BackendAdapter implements AdapterInterface
{
    /**
     * @var Backend
     */
    protected $backend;

    /**
     * @var string
     */
    protected $cachePath;

    /**
     * Constructor.
     *
     * @param Backend     $backend
     * @param string|null $path
     */
    public function __construct(Backend $backend, $path = null)
    {
        $this->backend = $backend;
        $this->cachePath = $path;
    }

    /**
     * Creates backend-relative path for cache file for given key
     *
     * @param string $key
     * @param bool   $prefix
     *
     * @return string
     */
    public function createCachePath($key, $prefix = false)
    {
        return Path::combine($this->cachePath, trim($key, '/') . ($prefix ? '' : '.cache'));
    }

    /**
     * Sets the value in cache under given key
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return bool true if successful
     */
    public function set($key, $value)
    {
        return $this->backend->put($this->createCachePath($key), serialize($value));
    }

    /**
     * Returns value under given key from cache
     *
     * @param string $key
     *
     * @return null|array
     */
    public function get($key)
    {
        $cachePath = $this->createCachePath($key);

        if (!$this->backend->has($cachePath)) {
            return null;
        }

        return unserialize($this->backend->read($cachePath));
    }

    /**
     * Deletes value under given key  from cache
     *
     * @param string $key
     *
     * @return bool true if successful
     */
    public function delete($key)
    {
        $cachePath = $this->createCachePath($key);

        if (!$this->backend->has($cachePath)) {
            return false;
        }

        $this->backend->delete($cachePath);

        $dirs = explode('/', dirname($cachePath));

        do {
            $dirPath = implode('/', $dirs);
            $contents = $this->backend->listContents($dirPath);

            if (!empty($contents)) {
                break;
            }

            $this->backend->deleteDir($dirPath);
            array_pop($dirs);
        } while (!empty($dirs));
    }

    /**
     * Deletes all cache entries with given key prefix
     *
     * @param string $keyPrefix
     *
     * @return bool true if successful
     */
    public function deleteByPrefix($keyPrefix)
    {
        $cachePath = $this->createCachePath($keyPrefix, true);
        if ($this->backend->hasDirectory($cachePath)) {
            return $this->backend->deleteDir($cachePath);
        }

        return false;
    }

    /**
     * Changes prefix for all entries given key prefix
     *
     * @param string $sourcePrefix
     * @param string $targetPrefix
     *
     * @return bool true if successful
     */
    public function changePrefix($sourcePrefix, $targetPrefix)
    {
        $sourceCachePath = $this->createCachePath($sourcePrefix, true);

        if (!$this->backend->hasDirectory($sourceCachePath)) {
            return false;
        }

        $targetCachePath = $this->createCachePath($targetPrefix, true);

        return $this->backend->rename($sourceCachePath, $targetCachePath);
    }
}
