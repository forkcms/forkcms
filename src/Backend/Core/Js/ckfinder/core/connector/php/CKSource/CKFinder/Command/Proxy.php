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
use CKSource\CKFinder\Config;
use CKSource\CKFinder\Event\CKFinderEvent;
use CKSource\CKFinder\Event\DownloadFileEvent;
use CKSource\CKFinder\Event\ProxyDownloadEvent;
use CKSource\CKFinder\Exception\AccessDeniedException;
use CKSource\CKFinder\Exception\FileNotFoundException;
use CKSource\CKFinder\Exception\InvalidExtensionException;
use CKSource\CKFinder\Exception\InvalidRequestException;
use CKSource\CKFinder\Filesystem\File\DownloadedFile;
use CKSource\CKFinder\Filesystem\File\File;
use CKSource\CKFinder\Filesystem\Folder\WorkingFolder;
use CKSource\CKFinder\Utils;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Proxy extends CommandAbstract
{
    protected $requires = array(Permission::FILE_VIEW);

    public function execute(Request $request, WorkingFolder $workingFolder, EventDispatcher $dispatcher, Config $config)
    {
        $fileName = (string) $request->query->get('fileName');
        $thumbnailFileName = (string) $request->query->get('thumbnail');

        if (!File::isValidName($fileName, $config->get('disallowUnsafeCharacters'))) {
            throw new InvalidRequestException(sprintf('Invalid file name: %s', $fileName));
        }

        $cacheLifetime = (int) $request->query->get('cache');

        if (!$workingFolder->containsFile($fileName)) {
            throw new FileNotFoundException();
        }

        if ($thumbnailFileName) {
            if (!File::isValidName($thumbnailFileName, $config->get('disallowUnsafeCharacters'))) {
                throw new InvalidRequestException(sprintf('Invalid resized image file name: %s', $fileName));
            }

            if (!$workingFolder->getResourceType()->isAllowedExtension(pathinfo($thumbnailFileName, PATHINFO_EXTENSION))) {
                throw new InvalidExtensionException();
            }

            $resizedImageRespository = $this->app->getResizedImageRepository();
            $file = $resizedImageRespository->getExistingResizedImage(
                $workingFolder->getResourceType(),
                $workingFolder->getClientCurrentFolder(),
                $fileName,
                $thumbnailFileName
            );
            $dataStream = $file->readStream();
        } else {
            $file = new DownloadedFile($fileName, $this->app);
            $file->isValid();
            $dataStream = $workingFolder->readStream($file->getFilename());
        }

        $proxyDownload = new ProxyDownloadEvent($this->app, $file);

        $dispatcher->dispatch(CKFinderEvent::PROXY_DOWNLOAD, $proxyDownload);

        if ($proxyDownload->isPropagationStopped()) {
            throw new AccessDeniedException();
        }

        $response = new StreamedResponse();
        $response->headers->set('Content-Type', $file->getMimeType());
        $response->headers->set('Content-Length', $file->getSize());
        $response->headers->set('Content-Disposition', 'inline; filename="' . $fileName. '"');

        if ($cacheLifetime > 0) {
            Utils::removeSessionCacheHeaders();

            $response->setPublic();
            $response->setEtag(dechex($file->getTimestamp()) . "-" . dechex($file->getSize()));

            $lastModificationDate = new \DateTime();
            $lastModificationDate->setTimestamp($file->getTimestamp());

            $response->setLastModified($lastModificationDate);

            if ($response->isNotModified($request)) {
                return $response;
            }

            $response->setMaxAge($cacheLifetime);

            $expireTime = new \DateTime();
            $expireTime->modify('+' . $cacheLifetime . 'seconds');
            $response->setExpires($expireTime);
        }

        $chunkSize = 1024 * 100;

        $response->setCallback(function () use ($dataStream, $chunkSize) {
            if ($dataStream === false) {
                return false;
            }
            while (!feof($dataStream)) {
                echo fread($dataStream, $chunkSize);
                flush();
                @set_time_limit(8);
            }

            return true;
        });

        return $response;
    }
}
