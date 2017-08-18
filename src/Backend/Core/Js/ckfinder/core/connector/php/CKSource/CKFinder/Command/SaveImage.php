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

namespace CKSource\CKFinder\Command;

use CKSource\CKFinder\Acl\Acl;
use CKSource\CKFinder\Acl\Permission;
use CKSource\CKFinder\Cache\CacheManager;
use CKSource\CKFinder\Config;
use CKSource\CKFinder\Error;
use CKSource\CKFinder\Event\CKFinderEvent;
use CKSource\CKFinder\Event\EditFileEvent;
use CKSource\CKFinder\Exception\AccessDeniedException;
use CKSource\CKFinder\Exception\InvalidExtensionException;
use CKSource\CKFinder\Exception\InvalidUploadException;
use CKSource\CKFinder\Exception\UnauthorizedException;
use CKSource\CKFinder\Filesystem\File\EditedImage;
use CKSource\CKFinder\Filesystem\Path;
use CKSource\CKFinder\Image;
use CKSource\CKFinder\Filesystem\Folder\WorkingFolder;
use CKSource\CKFinder\ResizedImage\ResizedImageRepository;
use CKSource\CKFinder\Thumbnail\ThumbnailRepository;
use CKSource\CKFinder\Utils;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

class SaveImage extends CommandAbstract
{
    protected $requestMethod = Request::METHOD_POST;

    protected $requires = array(Permission::FILE_CREATE);

    public function execute(Request $request, WorkingFolder $workingFolder, EventDispatcher $dispatcher, CacheManager $cache, ResizedImageRepository $resizedImageRepository, ThumbnailRepository $thumbnailRepository, Acl $acl, Config $config)
    {
        $fileName = (string) $request->query->get('fileName');

        $editedImage = new EditedImage($fileName, $this->app);

        $saveAsNew = false;

        if (!$editedImage->exists()) {
            $saveAsNew = true;
            $editedImage->saveAsNew(true);
        } else {
            // If file exists check for FILE_DELETE permission
            $resourceTypeName = $workingFolder->getResourceType()->getName();
            $path = $workingFolder->getClientCurrentFolder();

            if (!$acl->isAllowed($resourceTypeName, $path, Permission::FILE_DELETE)) {
                throw new UnauthorizedException(sprintf('Unauthorized: no FILE_DELETE permission in %s:%s', $resourceTypeName, $path));
            }
        }

        if (!Image::isSupportedExtension($editedImage->getExtension())) {
            throw new InvalidExtensionException('Unsupported image type or not image file');
        }

        $imageFormat = Image::mimeTypeFromExtension($editedImage->getExtension());

        $uploadedData = (string) $request->request->get('content');

        if (null === $uploadedData || strpos($uploadedData, 'data:image/png;base64,') !== 0) {
            throw new InvalidUploadException('Invalid upload. Expected base64 encoded PNG image.');
        }

        $data = explode(',', $uploadedData);
        $data = isset($data[1]) ? base64_decode($data[1]) : false;

        if (!$data) {
            throw new InvalidUploadException();
        }

        try {
            $uploadedImage = Image::create($data);
        } catch (\Exception $e) {
            // No need to check if secureImageUploads is enabled - image must be valid here
            throw new InvalidUploadException('Invalid upload: corrupted image', Error::UPLOADED_CORRUPT, array(), $e);
        }

        $imagesConfig = $config->get('images');

        if ($imagesConfig['maxWidth'] && $uploadedImage->getWidth() > $imagesConfig['maxWidth'] ||
            $imagesConfig['maxHeight'] && $uploadedImage->getHeight() > $imagesConfig['maxHeight']) {
            $uploadedImage->resize($imagesConfig['maxWidth'], $imagesConfig['maxHeight'], $imagesConfig['quality']);
        }

        $editedImage->setNewContents($uploadedImage->getData($imageFormat));
        $editedImage->setNewDimensions($uploadedImage->getWidth(), $uploadedImage->getHeight());

        if (!$editedImage->isValid()) {
            throw new InvalidUploadException('Invalid file provided');
        }

        $editFileEvent = new EditFileEvent($this->app, $editedImage);

        $imageInfo = $uploadedImage->getInfo();

        $cache->set(
            Path::combine(
                $workingFolder->getResourceType()->getName(),
                $workingFolder->getClientCurrentFolder(),
                $fileName),
            $uploadedImage->getInfo()
        );

        $dispatcher->dispatch(CKFinderEvent::SAVE_IMAGE, $editFileEvent);

        $saved = false;

        if (!$editFileEvent->isPropagationStopped()) {
            $saved = $editedImage->save($editFileEvent->getNewContents());

            if (!$saved) {
                throw new AccessDeniedException("Couldn't save image file");
            }

            //Remove thumbnails and resized images in case if file is overwritten
            if (!$saveAsNew && $saved) {
                $resourceType = $workingFolder->getResourceType();
                $thumbnailRepository->deleteThumbnails($resourceType, $workingFolder->getClientCurrentFolder(), $fileName);
                $resizedImageRepository->deleteResizedImages($resourceType, $workingFolder->getClientCurrentFolder(), $fileName);
            }
        }

        return array(
            'saved' => (int) $saved,
            'date'  => Utils::formatDate(time()),
            'size'  => Utils::formatSize($imageInfo['size'])
        );
    }
}
