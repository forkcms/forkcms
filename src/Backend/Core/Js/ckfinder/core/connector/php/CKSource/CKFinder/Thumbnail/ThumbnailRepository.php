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

namespace CKSource\CKFinder\Thumbnail;

use CKSource\CKFinder\Backend\Backend;
use CKSource\CKFinder\CKFinder;
use CKSource\CKFinder\Config;
use CKSource\CKFinder\Event\CKFinderEvent;
use CKSource\CKFinder\Event\ResizeImageEvent;
use CKSource\CKFinder\Filesystem\Path;
use CKSource\CKFinder\ResourceType\ResourceType;

/**
 * The ThumbnailRepository class.
 *
 * A class responsible for thumbnail management.
 *
 * @copyright 2016 CKSource - Frederico Knabben
 */
class ThumbnailRepository
{
    /**
     * @var CKFinder
     */
    protected $app;

    /**
     * @var Config
     */
    protected $config;

    /**
     * The Backend where thumbnails are stored.
     *
     * @var Backend $thumbsBackend
     */
    protected $thumbsBackend;

    /**
     * Event dispatcher.
     *
     * @var $dispatcher
     */
    protected $dispatcher;

    /**
     * Constructor.
     *
     * @param CKFinder $app
     */
    public function __construct(CKFinder $app)
    {
        $this->app = $app;
        $this->config = $app['config'];
        $this->thumbsBackend = $app['backend_factory']->getPrivateDirBackend('thumbs');
        $this->dispatcher = $app['dispatcher'];
    }

    /**
     * Returns the Backend object where thumbnails are stored.
     *
     * @return Backend
     */
    public function getThumbnailBackend()
    {
        return $this->thumbsBackend;
    }

    /**
     * @return CKFinder
     */
    public function getContainer()
    {
        return $this->app;
    }

    /**
     * Returns backend-relative directory path where
     * thumbnails are stored.
     *
     * @return string
     */
    public function getThumbnailsPath()
    {
        return $this->config->getPrivateDirPath('thumbs');
    }

    /**
     * Returns an array of allowed sizes for thumbnails.
     *
     * @return array
     */
    public function getAllowedSizes()
    {
        return $this->config->get('thumbnails.sizes');
    }

    /**
     * Returns information about bitmap support for thumbnails. If bitmap
     * support is disabled, thumbnails for bitmaps will not be generated.
     *
     * @return bool `true` if bitmap support is enabled.
     */
    public function isBitmapSupportEnabled()
    {
        return $this->config->get('thumbnails.bmpSupported');
    }

    /**
     * Returns a thumbnail object for a given file defined by the resource type,
     * path and file name.
     * The real size of the thumbnail image will be adjusted to one of the sizes
     * allowed by the thumbnail configuration.
     *
     * @param ResourceType $resourceType    source file resource type
     * @param string       $path            source file directory path
     * @param string       $fileName        source file name
     * @param int          $requestedWidth  requested thumbnail height
     * @param int          $requestedHeight requested thumbnail height
     *
     * @return Thumbnail
     *
     * @throws \Exception
     */
    public function getThumbnail(ResourceType $resourceType, $path, $fileName, $requestedWidth, $requestedHeight)
    {
        $thumbnail = new Thumbnail($this, $resourceType, $path, $fileName, $requestedWidth, $requestedHeight);

        if (!$thumbnail->exists()) {
            $thumbnail->create();

            $createThumbnailEvent = new ResizeImageEvent($this->app, $thumbnail);
            $this->dispatcher->dispatch(CKFinderEvent::CREATE_THUMBNAIL, $createThumbnailEvent);

            if (!$createThumbnailEvent->isPropagationStopped()) {
                $thumbnail = $createThumbnailEvent->getResizedImage();
                $thumbnail->save();
            }
        } else {
            $thumbnail->load();
        }

        return $thumbnail;
    }

    /**
     * Deletes all thumbnails under the given path defined by the resource type,
     * path and file name.
     *
     * @param ResourceType $resourceType
     * @param string       $path
     * @param string       $fileName
     *
     * @return bool `true` if deleted successfully
     */
    public function deleteThumbnails(ResourceType $resourceType, $path, $fileName = null)
    {
        $path = Path::combine($this->getThumbnailsPath(), $resourceType->getName(), $path, $fileName);

        if ($this->thumbsBackend->has($path)) {
            return $this->thumbsBackend->deleteDir($path);
        }

        return false;
    }
}
