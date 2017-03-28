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

namespace CKSource\CKFinder\ResourceType;

use CKSource\CKFinder\Backend\Backend;
use CKSource\CKFinder\ResizedImage\ResizedImageRepository;
use CKSource\CKFinder\Thumbnail\ThumbnailRepository;

class ResourceType
{
    protected $app;
    protected $name;
    protected $backend;
    protected $configNode;
    protected $thumbnailRepository;
    protected $resizedImageRepository;

    public function __construct($name, array $configNode, Backend $backend, ThumbnailRepository $thumbnailRepository, ResizedImageRepository $resizedImageRepository)
    {
        $this->name = $name;
        $this->configNode = $configNode;
        $this->backend = $backend;
        $this->thumbnailRepository = $thumbnailRepository;
        $this->resizedImageRepository = $resizedImageRepository;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDirectory()
    {
        return $this->configNode['directory'];
    }

    public function getBackend()
    {
        return $this->backend;
    }

    public function getThumbnailRepository()
    {
        return $this->thumbnailRepository;
    }

    public function getResizedImageRepository()
    {
        return $this->resizedImageRepository;
    }

    public function getMaxSize()
    {
        return $this->configNode['maxSize'];
    }

    public function getAllowedExtensions()
    {
        return $this->configNode['allowedExtensions'];
    }

    public function getDeniedExtensions()
    {
        return $this->configNode['deniedExtensions'];
    }

    public function getLabel()
    {
        return isset($this->configNode['label']) ? $this->configNode['label'] : null;
    }

    public function isLazyLoaded()
    {
        return isset($this->configNode['lazyLoad']) && $this->configNode['lazyLoad'];
    }

    public function isAllowedExtension($extension)
    {
        $extension = strtolower(ltrim($extension, '.'));

        $allowed = $this->configNode['allowedExtensions'];
        $denied = $this->configNode['deniedExtensions'];

        if (!empty($allowed) && !in_array($extension, $allowed) ||
            !empty($denied) && in_array($extension, $denied)) {
            return false;
        }

        return true;
    }

    /**
     * Returns the resource type hash.
     *
     * @return string hash string
     */
    public function getHash()
    {
        return substr(md5($this->configNode['name'] . $this->configNode['backend'] . $this->configNode['directory'] . $this->backend->getBaseUrl() . $this->backend->getRootDirectory()), 0, 16);
    }
}
