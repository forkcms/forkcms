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
use CKSource\CKFinder\Cache\CacheManager;
use CKSource\CKFinder\Config;
use CKSource\CKFinder\Exception\FileNotFoundException;
use CKSource\CKFinder\Exception\InvalidNameException;
use CKSource\CKFinder\Exception\InvalidRequestException;
use CKSource\CKFinder\Filesystem\File\DownloadedFile;
use CKSource\CKFinder\Filesystem\File\File;
use CKSource\CKFinder\Filesystem\Folder\WorkingFolder;
use CKSource\CKFinder\Filesystem\Path;
use CKSource\CKFinder\Image;
use Symfony\Component\HttpFoundation\Request;

class ImageInfo extends CommandAbstract
{
    protected $requires = array(
        Permission::FILE_VIEW
    );

    public function execute(Request $request, WorkingFolder $workingFolder, Config $config, CacheManager $cache)
    {
        $fileName = (string) $request->get('fileName');

        if (null === $fileName || !File::isValidName($fileName, $config->get('disallowUnsafeCharacters'))) {
            throw new InvalidRequestException('Invalid file name');
        }

        if (!Image::isSupportedExtension(pathinfo($fileName, PATHINFO_EXTENSION))) {
            throw new InvalidNameException('Invalid source file name');
        }

        if (!$workingFolder->containsFile($fileName)) {
            throw new FileNotFoundException();
        }

        $cachePath = Path::combine(
            $workingFolder->getResourceType()->getName(),
            $workingFolder->getClientCurrentFolder(),
            $fileName
        );

        $imageInfo = array();

        $cachedInfo = $cache->get($cachePath);

        if ($cachedInfo && isset($cachedInfo['width']) && isset($cachedInfo['height'])) {
            $imageInfo = $cachedInfo;
        } else {
            $file = new DownloadedFile($fileName, $this->app);

            if ($file->isValid()) {
                $image = Image::create($file->getContents());
                $imageInfo = $image->getInfo();
                $cache->set($cachePath, $imageInfo);
            }
        }

        return $imageInfo;
    }
}
