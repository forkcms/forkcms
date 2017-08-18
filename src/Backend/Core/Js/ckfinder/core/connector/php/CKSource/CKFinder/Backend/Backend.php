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

namespace CKSource\CKFinder\Backend;

use CKSource\CKFinder\Acl\AclInterface;
use CKSource\CKFinder\Acl\Permission;
use CKSource\CKFinder\Backend\Adapter\AwsS3;
use CKSource\CKFinder\Backend\Adapter\EmulateRenameDirectoryInterface;
use CKSource\CKFinder\CKFinder;
use CKSource\CKFinder\Config;
use CKSource\CKFinder\Filesystem\Path;
use CKSource\CKFinder\ResizedImage\ResizedImage;
use CKSource\CKFinder\ResourceType\ResourceType;
use CKSource\CKFinder\Utils;
use League\Flysystem\Adapter\Ftp;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Plugin\GetWithMetadata;
use League\Flysystem\Filesystem;

/**
 * The Backend file system class.
 *
 * A wrapper class for League\Flysystem\Filesystem with
 * CKFinder customizations.
 */
class Backend extends Filesystem
{
    /**
     * The CKFinder application container.
     *
     * @var CKFinder $app
     */
    protected $app;

    /**
     * Access Control Lists.
     *
     * @var AclInterface $acl
     */
    protected $acl;

    /**
     * Configuration.
     *
     * @var Config $ckConfig
     */
    protected $ckConfig;

    /**
     * Backend configuration array.
     */
    protected $backendConfig;

    /**
     * Constructor.
     *
     * @param array            $backendConfig    the backend configuration node
     * @param CKFinder         $app              the CKFinder app container
     * @param AdapterInterface $adapter          the adapter
     * @param array|null       $filesystemConfig the configuration
     */
    public function __construct(array $backendConfig, CKFinder $app, AdapterInterface $adapter, $filesystemConfig = null)
    {
        $this->app = $app;
        $this->backendConfig = $backendConfig;
        $this->acl = $app['acl'];
        $this->ckConfig = $app['config'];

        parent::__construct($adapter, $filesystemConfig);

        $this->addPlugin(new GetWithMetadata());
    }

    /**
     * Returns the name of the backend.
     *
     * @return string name of the backend
     */
    public function getName()
    {
        return $this->backendConfig['name'];
    }

    /**
     * Returns an array of commands that should use operation tracking.
     *
     * @return array
     */
    public function getTrackedOperations()
    {
        return isset($this->backendConfig['trackedOperations']) ? $this->backendConfig['trackedOperations'] : array();
    }

    /**
     * Returns a path based on the resource type and the resource type relative path.
     *
     * @param ResourceType $resourceType the resource type
     * @param string       $path         the resource type relative path
     *
     * @return string path to be used with the backend adapter.
     */
    public function buildPath(ResourceType $resourceType, $path)
    {
        return Path::combine($resourceType->getDirectory(), $path);
    }

    /**
     * Returns a filtered list of directories for a given resource type and path.
     *
     * @param ResourceType $resourceType
     * @param string       $path
     * @param bool         $recursive
     *
     * @return array
     */
    public function directories(ResourceType $resourceType, $path = '', $recursive = false)
    {
        $directoryPath = $this->buildPath($resourceType, $path);
        $contents = $this->listContents($directoryPath, $recursive);

        foreach ($contents as &$entry) {
            $entry['acl'] = $this->acl->getComputedMask($resourceType->getName(), Path::combine($path, $entry['basename']));
        }

        return array_filter($contents, function ($v) {
            return isset($v['type']) &&
                   $v['type'] === 'dir' &&
                   !$this->isHiddenFolder($v['basename']) &&
                   $v['acl'] & Permission::FOLDER_VIEW;
        });
    }

    /**
     * Returns a filtered list of files for a given resource type and path.
     *
     * @param ResourceType $resourceType
     * @param string       $path
     * @param bool         $recursive
     *
     * @return array
     */
    public function files(ResourceType $resourceType, $path = '', $recursive = false)
    {
        $directoryPath = $this->buildPath($resourceType, $path);
        $contents = $this->listContents($directoryPath, $recursive);

        return array_filter($contents, function ($v) use ($resourceType) {
            return isset($v['type']) &&
                   $v['type'] === 'file' &&
                   !$this->isHiddenFile($v['basename']) &&
                   $resourceType->isAllowedExtension(isset($v['extension']) ? $v['extension'] : '');
        });
    }

    /**
     * Check if the directory for a given path contains subdirectories.
     *
     * @param ResourceType $resourceType
     * @param string       $path
     *
     * @return bool `true` if the directory contains subdirectories.
     */
    public function containsDirectories(ResourceType $resourceType, $path = '')
    {
        $baseAdapter = $this->getBaseAdapter();
        if (method_exists($baseAdapter, 'containsDirectories')) {
            return $baseAdapter->containsDirectories($this, $resourceType, $path, $this->acl);
        }

        $directoryPath = $this->buildPath($resourceType, $path);
        $contents = $this->listContents($directoryPath);

        foreach ($contents as $entry) {
            if ($entry['type'] === 'dir' &&
                !$this->isHiddenFolder($entry['basename']) &&
                $this->acl->isAllowed($resourceType->getName(), Path::combine($path, $entry['basename']), Permission::FOLDER_VIEW)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if the file with a given name is hidden.
     *
     * @param string $fileName
     *
     * @return bool `true` if the file is hidden.
     */
    public function isHiddenFile($fileName)
    {
        $hideFilesRegex = $this->ckConfig->getHideFilesRegex();

        if ($hideFilesRegex) {
            return (bool) preg_match($hideFilesRegex, $fileName);
        }

        return false;
    }

    /**
     * Checks if the directory with a given name is hidden.
     *
     * @param string $folderName
     *
     * @return bool `true` if the directory is hidden.
     */
    public function isHiddenFolder($folderName)
    {
        $hideFoldersRegex = $this->ckConfig->getHideFoldersRegex();

        if ($hideFoldersRegex) {
            return (bool) preg_match($hideFoldersRegex, $folderName);
        }

        return false;
    }

    /**
     * Checks if the path is hidden.
     *
     * @param string $path
     *
     * @return bool `true` if the path is hidden.
     */
    public function isHiddenPath($path)
    {
        $pathParts = explode('/', trim($path, '/'));
        if ($pathParts) {
            foreach ($pathParts as $part) {
                if ($this->isHiddenFolder($part)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Deletes a directory.
     *
     * @param string $dirname
     *
     * @return bool
     */
    public function deleteDir($dirname)
    {
        $baseAdapter = $this->getBaseAdapter();

        // For FTP first remove recursively all directory contents
        if ($baseAdapter instanceof Ftp) {
            $this->deleteContents($dirname);
        }

        return parent::deleteDir($dirname);
    }

    /**
     * Delete all contents of the given directory.
     *
     * @param string $dirname
     */
    public function deleteContents($dirname)
    {
        $contents = $this->listContents($dirname);

        foreach ($contents as $entry) {
            if ($entry['type'] === 'dir') {
                $this->deleteContents($entry['path']);
                $this->deleteDir($entry['path']);
            } else {
                $this->delete($entry['path']);
            }
        }
    }

    /**
     * Checks if a backend contains a directory.
     *
     * The Backend::has() method is not always reliable and may
     * work differently for various adapters. Checking for directory
     * should be done with this method.
     *
     * @param string $directoryPath
     *
     * @return bool
     */
    public function hasDirectory($directoryPath)
    {
        $pathParts = array_filter(explode('/', $directoryPath), 'strlen');
        $dirName = array_pop($pathParts);
        $contents = $this->listContents(implode('/', $pathParts));

        foreach ($contents as $c) {
            if (isset($c['type']) && isset($c['basename']) && $c['type'] === 'dir' && $c['basename'] === $dirName) {
                return true;
            }
        }
    }

    /**
     * Returns a URL to a file.
     *
     * If the useProxyCommand option is set for a backend, the returned
     * URL will point to the CKFinder connector Proxy command.
     *
     * @param ResourceType $resourceType      the file resource type
     * @param string       $folderPath        the resource-type relative folder path
     * @param string       $fileName          the file name
     * @param string|null  $thumbnailFileName the thumbnail file name - if the file is a thumbnail
     *
     * @return string|null URL to a file or `null` if the backend does not support it.
     */
    public function getFileUrl(ResourceType $resourceType, $folderPath, $fileName, $thumbnailFileName = null)
    {
        if (isset($this->backendConfig['useProxyCommand'])) {
            $connectorUrl = $this->app->getConnectorUrl();

            $queryParameters = array(
                'command' => 'Proxy',
                'type' => $resourceType->getName(),
                'currentFolder' => $folderPath,
                'fileName' => $fileName
            );

            if ($thumbnailFileName) {
                $queryParameters['thumbnail'] = $thumbnailFileName;
            }

            $proxyCacheLifetime = (int) $this->ckConfig->get('cache.proxyCommand');

            if ($proxyCacheLifetime > 0) {
                $queryParameters['cache'] = $proxyCacheLifetime;
            }

            return $connectorUrl . '?' . http_build_query($queryParameters, '', '&');
        }

        $path = $thumbnailFileName
            ? Path::combine($resourceType->getDirectory(), $folderPath, ResizedImage::DIR, $fileName, $thumbnailFileName)
            : Path::combine($resourceType->getDirectory(), $folderPath, $fileName);

        if (isset($this->backendConfig['baseUrl'])) {
            return Path::combine($this->backendConfig['baseUrl'], Utils::encodeURLParts($path));
        }

        $baseAdapter = $this->getBaseAdapter();

        if (method_exists($baseAdapter, 'getFileUrl')) {
            return $baseAdapter->getFileUrl($path);
        }

        return null;
    }

    /**
     * Returns the base URL used to build the direct URL to files stored
     * in this backend.
     *
     * @return string|null base URL or `null` if the base URL for a backend
     *                     was not defined.
     */
    public function getBaseUrl()
    {
        if (isset($this->backendConfig['baseUrl']) && !$this->usesProxyCommand()) {
            return $this->backendConfig['baseUrl'];
        }

        return null;
    }

    /**
     * Returns the root directory defined for the backend.
     *
     * @return string|null root directory or `null` if the root directory
     *                     was not defined.
     */
    public function getRootDirectory()
    {
        if (isset($this->backendConfig['root'])) {
            return $this->backendConfig['root'];
        }

        return null;
    }

    /**
     * Returns a Boolean value telling if the backend uses the Proxy command.
     *
     * @return bool
     */
    public function usesProxyCommand()
    {
        return isset($this->backendConfig['useProxyCommand']) && $this->backendConfig['useProxyCommand'];
    }

    /**
     * Creates a stream for writing.
     *
     * @param string $path file path
     *
     * @return resource|null a stream to a file or `null` if the backend does not
     *                       support writing streams.
     */
    public function createWriteStream($path)
    {
        $baseAdapter = $this->getBaseAdapter();

        if (method_exists($baseAdapter, 'createWriteStream')) {
            return $baseAdapter->createWriteStream($path);
        }

        return null;
    }

    /**
     * Renames the object for a given path.
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool `true` on success, `false` on failure.
     */
    public function rename($path, $newpath)
    {
        $baseAdapter = $this->getBaseAdapter();

        if (($baseAdapter instanceof EmulateRenameDirectoryInterface) && $this->hasDirectory($path)) {
            return $baseAdapter->renameDirectory($path, $newpath);
        }

        return parent::rename($path, $newpath);
    }

    /**
     * Returns a base adapter used by this backend.
     *
     * The used adapter might be decorated with CachedAdapter. In this
     * case the returned adapter is the internal one used by CachedAdapter.
     *
     * @return AdapterInterface
     */
    public function getBaseAdapter()
    {
        if ($this->adapter instanceof CachedAdapter) {
            return $this->adapter->getAdapter();
        }

        return $this->adapter;
    }
}
