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

use CKSource\CKFinder\Acl\Permission;
use CKSource\CKFinder\Exception\FileNotFoundException;
use CKSource\CKFinder\Exception\InvalidNameException;
use CKSource\CKFinder\Exception\InvalidRequestException;
use CKSource\CKFinder\Filesystem\File\File;
use CKSource\CKFinder\Filesystem\Folder\WorkingFolder;
use CKSource\CKFinder\Image;
use CKSource\CKFinder\Config;
use CKSource\CKFinder\ResizedImage\ResizedImageRepository;
use Symfony\Component\HttpFoundation\Request;

class ImageResize extends CommandAbstract
{
    protected $requestMethod = Request::METHOD_POST;

    protected $requires = array(Permission::FILE_VIEW, Permission::IMAGE_RESIZE);

    public function execute(Request $request, WorkingFolder $workingFolder, Config $config, ResizedImageRepository $resizedImageRepository)
    {
        $fileName = (string) $request->query->get('fileName');

        if (null === $fileName || !File::isValidName($fileName, $config->get('disallowUnsafeCharacters'))) {
            throw new InvalidRequestException('Invalid file name');
        }

        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!Image::isSupportedExtension($ext)) {
            throw new InvalidNameException('Invalid source file name');
        }

        if (!$workingFolder->containsFile($fileName)) {
            throw new FileNotFoundException();
        }

        list($requestedWidth, $requestedHeight) = Image::parseSize((string) $request->query->get('size'));

        $resizedImage = $resizedImageRepository->getResizedImage(
            $workingFolder->getResourceType(),
            $workingFolder->getClientCurrentFolder(),
            $fileName,
            $requestedWidth,
            $requestedHeight
        );

        return array('url' => $resizedImage->getUrl());
    }
}
