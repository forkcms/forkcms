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

namespace CKSource\CKFinder\ResizedImage;

use CKSource\CKFinder\Backend\Backend;
use CKSource\CKFinder\Exception\CKFinderException;
use CKSource\CKFinder\Filesystem\Path;
use CKSource\CKFinder\Image;
use CKSource\CKFinder\ResourceType\ResourceType;

abstract class ResizedImageAbstract
{
    /**
     * The source file resource type object.
     *
     * @var ResourceType $sourceFileResourceType
     */
    protected $sourceFileResourceType;

    /**
     * The Backend where resized images are stored. By default
     * it points to the `dir.thumbs` local file system directory.
     *
     * @var Backend $backend
     */
    protected $backend;

    /**
     * The source file directory path.
     *
     * @var string $sourceFileDir
     */
    protected $sourceFileDir;

    /**
     * The source file name.
     *
     * @var string $sourceFileName
     */
    protected $sourceFileName;

    /**
     * The width requested for this resized image.
     *
     * @var int $requestedWidth
     */
    protected $requestedWidth;

    /**
     * The height requested for this resized image.
     *
     * @var int $requestedHeight
     */
    protected $requestedHeight;

    /**
     * Thumbnail file name. For example the name of the resized image generated
     * for a file `example.jpg` may look like `example__300x300.jpg`.
     *
     * @var string $resizedImageFileName
     */
    protected $resizedImageFileName;

    /**
     * Thumbnail image binary data.
     *
     * @var string $resizedImageData
     */
    protected $resizedImageData;

    /**
     * Thumbnail image size in bytes.
     *
     * @var int $resizedImageSize
     */
    protected $resizedImageSize;

    /**
     * Thumbnail image MIME type.
     * @var string $resizedImageMimeType
     */
    protected $resizedImageMimeType;

    /**
     * Timestamp with the last modification time of the resized image.
     *
     * @var int $timestamp
     */
    protected $timestamp;

    /**
     * @param ResourceType $sourceFileResourceType
     * @param string       $sourceFileDir
     * @param string       $sourceFileName
     * @param int          $requestedWidth
     * @param int          $requestedHeight
     */
    public function __construct(ResourceType $sourceFileResourceType, $sourceFileDir, $sourceFileName, $requestedWidth, $requestedHeight)
    {
        $this->sourceFileResourceType = $sourceFileResourceType;
        $this->sourceFileDir = $sourceFileDir;
        $this->sourceFileName = $sourceFileName;
        $this->requestedWidth = $requestedWidth;
        $this->requestedHeight = $requestedHeight;

        $this->backend = $sourceFileResourceType->getBackend();
    }

    /**
     * Returns the resized image resource type.
     *
     * @return ResourceType
     */
    public function getResourceType()
    {
        return $this->sourceFileResourceType;
    }

    /**
     * Returns the resized image file name.
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->resizedImageFileName;
    }

    /**
     * Returns the backend-relative resized image file path.
     *
     * @return string
     */
    public function getFilePath()
    {
        return Path::combine($this->getDirectory(), $this->getFileName());
    }

    /**
     * Returns the resized image MIME type.
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->resizedImageMimeType;
    }

    /**
     * Returns a timestamp of the last modification of this resized image.
     *
     * @return int timestamp
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Returns the resized image size in bytes.
     *
     * @return int
     */
    public function getSize()
    {
        return $this->resizedImageSize;
    }

    /**
     * Returns the resized image binary data.
     *
     * @return string binary image date
     */
    public function getImageData()
    {
        return $this->resizedImageData;
    }

    /**
     * Sets the image data.
     *
     * @param string $imageData binary image data
     */
    public function setImageData($imageData)
    {
        $image = Image::create($imageData);

        $this->resizedImageSize = strlen($imageData);
        $this->resizedImageMimeType = $image->getMimeType();
        $this->resizedImageData = $imageData;

        unset($image);
    }

    /**
     * Checks if the resized image already exists.
     *
     * @return bool
     */
    public function exists()
    {
        return $this->backend->has($this->getFilePath());
    }

    /**
     * Saves the resized image in the backend.
     *
     * @return bool `true` if saved successfully.
     */
    public function save()
    {
        if (!$this->backend->hasDirectory($this->getDirectory())) {
            $this->backend->createDir($this->getDirectory());
        }

        $saved = $this->backend->put($this->getFilePath(), $this->resizedImageData, array('mimetype' => $this->getMimeType()));

        if ($saved) {
            $this->timestamp = time();
        }

        return $saved;
    }

    /**
     * Loads an existing resized image from a backend.
     */
    public function load()
    {
        $thumbnailMetadata = $this->backend->getWithMetadata($this->getFilePath(), array('mimetype', 'timestamp'));
        $this->timestamp = $thumbnailMetadata['timestamp'];
        $this->resizedImageSize = $thumbnailMetadata['size'];
        $this->resizedImageMimeType = $thumbnailMetadata['mimetype'];

        $this->resizedImageData = $this->backend->read($this->getFilePath());
    }

    /**
     * Returns image data stream.
     *
     * @return bool|false|resource
     *
     * @throws CKFinderException
     */
    public function readStream()
    {
        if (null === $this->resizedImageData) {
            throw new CKFinderException('The resized image was not loaded from a backend yet. Please use ResizedImage::load() first.');
        }

        // The image should be already loaded the memory, no need to read stream from backend
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $this->resizedImageData);
        rewind($stream);

        return $stream;
    }

    /**
     * Creates the resized image.
     */
    abstract public function create();

    /**
     * Returns a directory path for the resized image.
     */
    abstract public function getDirectory();
}
