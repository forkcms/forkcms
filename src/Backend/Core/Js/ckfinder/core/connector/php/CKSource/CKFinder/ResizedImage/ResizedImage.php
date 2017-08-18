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

use CKSource\CKFinder\Exception\FileNotFoundException;
use CKSource\CKFinder\Filesystem\Path;
use CKSource\CKFinder\Image;
use CKSource\CKFinder\ResourceType\ResourceType;

/**
 * The resized image class.
 *
 * A class representing an image that was resized to given dimensions.
 *
 * @copyright 2016 CKSource - Frederico Knabben
 */
class ResizedImage extends ResizedImageAbstract
{
    const DIR = '__thumbs';

    /**
     * @var ResizedImageRepository
     */
    protected $resizedImageRepository;

    /**
     * @var Image $image
     */
    protected $image;

    /**
     * @var int $width
     */
    protected $width;

    /**
     * @var int $height
     */
    protected $height;

    /**
     * @var bool $requestedSizeIsValid
     */
    protected $requestedSizeIsValid = true;

    /**
     * A full source file path.
     *
     * @var string $sourceFileDir
     */
    protected $sourceFileDir;

    /**
     * @param ResizedImageRepository $resizedImageRepository Resized image repository object
     * @param ResourceType           $sourceFileResourceType Source image file resource type
     * @param string                 $sourceFileDir          Resource type relative directory path
     * @param string                 $sourceFileName         Source image filename
     * @param int                    $requestedWidth         Requested width
     * @param int                    $requestedHeight        Requested height
     * @param bool                   $forceRequestedSize     A flag telling if the requested size should be used, without calculating the aspect ratio
     *
     * @throws \Exception if the source image is invalid
     */
    public function __construct(ResizedImageRepository $resizedImageRepository, ResourceType $sourceFileResourceType, $sourceFileDir, $sourceFileName, $requestedWidth, $requestedHeight, $forceRequestedSize = false)
    {
        parent::__construct($sourceFileResourceType, $sourceFileDir, $sourceFileName, $requestedWidth, $requestedHeight);

        $this->resizedImageRepository = $resizedImageRepository;

        $backend = $this->backend = $sourceFileResourceType->getBackend();

        // Check if there's info about source image in cache
        $app = $this->resizedImageRepository->getContainer();


        if (!$forceRequestedSize) {
            $cacheKey = Path::combine($sourceFileResourceType->getName(), $sourceFileDir, $sourceFileName);

            $cachedInfo = $app['cache']->get($cacheKey);

            // No info cached, get original image
            if (null === $cachedInfo || !isset($cachedInfo['width']) || !isset($cachedInfo['height'])) {
                $sourceFilePath = Path::combine($sourceFileResourceType->getDirectory(), $sourceFileDir, $sourceFileName);

                if ($backend->isHiddenFile($sourceFileName) || !$backend->has($sourceFilePath)) {
                    throw new FileNotFoundException('ResizedImage::create(): Source file not found');
                }

                $originalImage = $this->image = Image::create($backend->read($sourceFilePath));

                $app['cache']->set($cacheKey, $originalImage->getInfo());

                $originalImageWidth = $originalImage->getWidth();
                $originalImageHeight = $originalImage->getHeight();
            } else {
                $originalImageWidth = $cachedInfo['width'];
                $originalImageHeight = $cachedInfo['height'];
            }

            $targetSize = Image::calculateAspectRatio($requestedWidth, $requestedHeight, $originalImageWidth, $originalImageHeight);

            if ($targetSize['width'] >= $originalImageWidth || $targetSize['height'] >= $originalImageHeight) {
                $this->width = $originalImageWidth;
                $this->height = $originalImageHeight;
                $this->requestedSizeIsValid = false;
            } else {
                $this->width = $targetSize['width'];
                $this->height = $targetSize['height'];
            }
        } else {
            $this->width = $requestedWidth;
            $this->height = $requestedHeight;
        }

        $this->resizedImageFileName = static::createFilename($sourceFileName, $this->width, $this->height);
    }

    public static function createFilename($fileName, $width, $height)
    {
        $pathInfo = pathinfo($fileName);

        return sprintf("%s__%dx%d%s", $pathInfo['filename'], $width, $height, isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '');
    }

    public static function getSizeFromFilename($resizedImageFileName)
    {
        $pathInfo = pathinfo($resizedImageFileName);

        preg_match('/^.*__(\d+)x(\d+)$/', $pathInfo['filename'], $matches);

        if (count($matches) === 3) {
            return array(
                'width'  => (int) $matches[1],
                'height' => (int) $matches[2]
            );
        }

        return null;
    }

    /**
     * Returns the directory of the resized image.
     *
     * @return string
     */
    public function getDirectory()
    {
        return Path::combine($this->sourceFileResourceType->getDirectory(),
            $this->sourceFileDir,
            ResizedImage::DIR,
            $this->sourceFileName
        );
    }

    /**
     * Creates a resized image.
     */
    public function create()
    {
        if (null === $this->image) {
            $sourceFilePath = Path::combine($this->sourceFileResourceType->getDirectory(), $this->sourceFileDir, $this->sourceFileName);

            if ($this->backend->isHiddenFile($this->sourceFileName) || !$this->backend->has($sourceFilePath)) {
                throw new FileNotFoundException('ResizedImage::create(): Source file not found');
            }

            $this->image = Image::create($this->backend->read($sourceFilePath));
        }

        $this->image->resize($this->width, $this->height);
        $this->resizedImageData = $this->image->getData();
        $this->resizedImageSize = $this->image->getDataSize();
        $this->resizedImageMimeType = $this->image->getMimeType();
    }

    /**
     * Returns the direct URL to the resized image.
     *
     * @return string
     */
    public function getUrl()
    {
        $backend = $this->sourceFileResourceType->getBackend();

        /**
         * In case the requested size is bigger than the size of the original image,
         * the resized version was not created.
         * This is a fallback that returns the URL to the original image.
         */
        if (!$this->requestedSizeIsValid()) {
            return $backend->getFileUrl($this->getResourceType(), $this->sourceFileDir, $this->sourceFileName);
        }

        return $backend->getFileUrl($this->sourceFileResourceType, $this->sourceFileDir, $this->sourceFileName, $this->getFileName());
    }

    /**
     * Checks if the size requested for the resized image is valid.
     *
     * @return bool `true` if the requested size is valid.
     */
    public function requestedSizeIsValid()
    {
        return $this->requestedSizeIsValid;
    }
}
