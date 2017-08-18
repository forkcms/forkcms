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

namespace CKSource\CKFinder\Filesystem\Folder;

use CKSource\CKFinder\Backend\Backend;
use CKSource\CKFinder\CKFinder;
use CKSource\CKFinder\Exception\AccessDeniedException;
use CKSource\CKFinder\Exception\AlreadyExistsException;
use CKSource\CKFinder\Exception\FileNotFoundException;
use CKSource\CKFinder\Exception\FolderNotFoundException;
use CKSource\CKFinder\Exception\InvalidExtensionException;
use CKSource\CKFinder\Exception\InvalidNameException;
use CKSource\CKFinder\Exception\InvalidRequestException;
use CKSource\CKFinder\Filesystem\File\File;
use CKSource\CKFinder\Filesystem\Path;
use CKSource\CKFinder\Operation\OperationManager;
use CKSource\CKFinder\ResourceType\ResourceType;
use CKSource\CKFinder\Response\JsonResponse;
use CKSource\CKFinder\ResizedImage\ResizedImageRepository;
use CKSource\CKFinder\Utils;
use League\Flysystem\Util\MimeType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use CKSource\CKFinder\Thumbnail\ThumbnailRepository;

/**
 * The WorkingFolder class.
 *
 * Represents a working folder for the current request defined by
 * a resource type and a relative path.
 */
class WorkingFolder extends Folder implements EventSubscriberInterface
{
    /**
     * @var CKFinder $app
     */
    protected $app;

    /**
     * @var Backend
     */
    protected $backend;

    /**
     * @var ThumbnailRepository
     */
    protected $thumbnailRepository;

    /**
     * @var ResourceType $resourceType
     */
    protected $resourceType;

    /**
     * Current folder path.
     *
     * @var string $clientCurrentFolder
     */
    protected $clientCurrentFolder;

    /**
     * Backend relative path (includes the backend directory prefix).
     *
     * @var string $path
     */
    protected $path;

    /**
     * Directory ACL mask computed for the current user.
     *
     * @var int|null $aclMask
     */
    protected $aclMask = null;

    /**
     * Constructor.
     *
     * @param CKFinder     $app
     *
     * @throws \Exception
     */
    public function __construct(CKFinder $app)
    {
        $this->app = $app;

        /* @var $request \Symfony\Component\HttpFoundation\Request */
        $request = $app['request_stack']->getCurrentRequest();

        $resourceType = $app['resource_type_factory']->getResourceType((string) $request->get('type'));

        $this->clientCurrentFolder = Path::normalize(trim((string) $request->get('currentFolder')));

        if (!Path::isValid($this->clientCurrentFolder)) {
            throw new InvalidNameException('Invalid path');
        }

        $resourceTypeDirectory = $resourceType->getDirectory();

        parent::__construct($resourceType, $this->clientCurrentFolder);

        $this->backend = $this->resourceType->getBackend();
        $this->thumbnailRepository = $app['thumbnail_repository'];

        $backend = $this->getBackend();

        // Check if folder path is not hidden
        if ($backend->isHiddenPath($this->getClientCurrentFolder())) {
            throw new InvalidRequestException('Hidden folder path used');
        }

        // Check if resource type folder exists - if not then create it
        $currentCommand = (string) $request->query->get('command');
        $omitForCommands = array('Thumbnail');

        if (!in_array($currentCommand, $omitForCommands) &&
            !empty($resourceTypeDirectory) &&
            !$backend->hasDirectory($this->path)) {
            if ($this->clientCurrentFolder === '/') {
                @$backend->createDir($resourceTypeDirectory);

                if (!$backend->hasDirectory($resourceTypeDirectory)) {
                    throw new AccessDeniedException("Couldn't create resource type directory. Please check permissions.");
                }
            } else {
                throw new FolderNotFoundException();
            }
        }
    }

    /**
     * Returns the ResourceType object for the current working folder.
     *
     * @return ResourceType
     */
    public function getResourceType()
    {
        return $this->resourceType;
    }

    /**
     * Returns the name of the current resource type.
     *
     * @return string
     */
    public function getResourceTypeName()
    {
        return $this->resourceType->getName();
    }

    /**
     * Returns the client current folder path.
     *
     * @return string
     */
    public function getClientCurrentFolder()
    {
        return $this->clientCurrentFolder;
    }

    /**
     * Returns the backend relative path with the resource type directory prefix.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Returns the backend assigned for the current resource type.
     *
     * @return Backend
     */
    public function getBackend()
    {
        return $this->resourceType->getBackend();
    }

    /**
     * Returns the thumbnails repository object.
     *
     * @return ThumbnailRepository
     */
    public function getThumbnailsRepository()
    {
        return $this->thumbnailRepository;
    }

    /**
     * Lists directories in the current working folder.
     *
     * @return array list of directories
     */
    public function listDirectories()
    {
        return $this->getBackend()->directories($this->getResourceType(), $this->getClientCurrentFolder());
    }

    /**
     * Lists files in the current working folder.
     *
     * @return array list of files
     */
    public function listFiles()
    {
        return $this->getBackend()->files($this->getResourceType(), $this->getClientCurrentFolder());
    }

    /**
     * Returns ACL mask computed for the current user and the current working folder.
     *
     * @return int
     */
    public function getAclMask()
    {
        if (null === $this->aclMask) {
            $this->aclMask = $this->app->getAcl()->getComputedMask($this->getResourceTypeName(), $this->getClientCurrentFolder());
        }

        return $this->aclMask;
    }

    /**
     * Creates a directory with a given name in the working folder.
     *
     * @param string $dirname directory name
     *
     * @return bool `true` if the folder was created successfully.
     *
     * @throws AccessDeniedException
     * @throws AlreadyExistsException
     * @throws InvalidNameException
     */
    public function createDir($dirname)
    {
        $backend = $this->getBackend();

        if (!Folder::isValidName($dirname, $this->app['config']->get('disallowUnsafeCharacters')) || $backend->isHiddenFolder($dirname)) {
            throw new InvalidNameException('Invalid folder name');
        }

        $dirPath = Path::combine($this->getPath(), $dirname);

        if ($backend->hasDirectory($dirPath)) {
            throw new AlreadyExistsException('Folder already exists');
        }

        $result = $backend->createDir($dirPath);

        if (!$result) {
            throw new AccessDeniedException("Couldn't create new folder. Please check permissions.");
        }

        return $result;
    }

    /**
     * Creates a file inside the current working folder.
     *
     * @param string $fileName file name
     * @param string $data     file data
     *
     * @return bool `true` if created successfully.
     */
    public function write($fileName, $data)
    {
        $backend = $this->getBackend();
        $filePath = Path::combine($this->getPath(), $fileName);

        return $backend->write($filePath, $data);
    }

    /**
     * Creates a file inside the current working folder using the stream.
     *
     * @param string   $fileName file name
     * @param resource $resource file data stream
     *
     * @return bool `true` if created successfully.
     */
    public function writeStream($fileName, $resource)
    {
        $backend = $this->getBackend();
        $filePath = Path::combine($this->getPath(), $fileName);

        return $backend->writeStream($filePath, $resource);
    }

    /**
     * Creates or updates a file inside the current working folder using the stream.
     *
     * @param string   $fileName file name
     * @param resource $resource file data stream
     * @param string   $mimeType file MIME type
     *
     * @return bool `true` if updated successfully.
     */
    public function putStream($fileName, $resource, $mimeType = null)
    {
        $backend = $this->getBackend();
        $filePath = Path::combine($this->getPath(), $fileName);

        if (!$mimeType) {
            $ext =  strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $mimeType = MimeType::detectByFileExtension($ext);
        }

        $options = $mimeType ? array('mimetype' => $mimeType) : array();

        return $backend->putStream($filePath, $resource, $options);
    }

    /**
     * Checks if the current working folder contains a file with a given name.
     *
     * @param string $fileName
     *
     * @return bool
     */
    public function containsFile($fileName)
    {
        $backend = $this->getBackend();

        if (!File::isValidName($fileName, $this->app['config']->get('disallowUnsafeCharacters')) ||
            $backend->isHiddenFolder($this->getClientCurrentFolder()) ||
            $backend->isHiddenFile($fileName) ||
            !$this->resourceType->isAllowedExtension(pathinfo($fileName, PATHINFO_EXTENSION))) {
            return false;
        }

        $filePath = Path::combine($this->getPath(), $fileName);

        return $backend->has($filePath);
    }

    /**
     * Returns contents of the file with a given name.
     *
     * @param string $fileName
     *
     * @return string
     */
    public function read($fileName)
    {
        $backend = $this->getBackend();
        $filePath = Path::combine($this->getPath(), $fileName);

        return $backend->read($filePath);
    }

    /**
     * Returns contents stream of the file with a given name.
     *
     * @param string $fileName
     *
     * @return resource
     */
    public function readStream($fileName)
    {
        $backend = $this->getBackend();
        $filePath = Path::combine($this->getPath(), $fileName);

        return $backend->readStream($filePath);
    }

    /**
     * Deletes the current working folder.
     *
     * @return bool `true` if the deletion was successful
     */
    public function delete()
    {
        // Delete related thumbs path
        $this->thumbnailRepository->deleteThumbnails($this->resourceType, $this->getClientCurrentFolder());

        $this->app['cache']->deleteByPrefix(Path::combine($this->resourceType->getName(), $this->getClientCurrentFolder()));

        return $this->getBackend()->deleteDir($this->getPath());
    }

    /**
     * Renames the current working folder.
     *
     * @param string $newName new folder name
     *
     * @return array containing newName and newPath
     *
     * @throws AccessDeniedException
     * @throws AlreadyExistsException
     * @throws InvalidNameException
     */
    public function rename($newName)
    {
        $disallowUnsafeCharacters  = $this->app['config']->get('disallowUnsafeCharacters');

        if (!Folder::isValidName($newName, $disallowUnsafeCharacters) || $this->backend->isHiddenFolder($newName)) {
            throw new InvalidNameException('Invalid folder name');
        }

        $newBackendPath = dirname($this->getPath()) . '/' . $newName;

        if ($this->backend->has($newBackendPath)) {
            throw new AlreadyExistsException('File already exists');
        }

        $newClientPath = Path::normalize(dirname($this->getClientCurrentFolder()) . '/' . $newName);

        if (!$this->getBackend()->rename($this->getPath(), $newBackendPath)) {
            throw new AccessDeniedException();
        }

        /* @var OperationManager $currentRequestOperation */
        $currentRequestOperation = $this->app['operation'];

        if ($currentRequestOperation->isAborted()) {
            // Don't continue in this case, no need to touch thumbs and cache entries
            return array('aborted' => true);
        }

        // Delete related thumbs path
        $this->thumbnailRepository->deleteThumbnails($this->resourceType, $this->getClientCurrentFolder());

        $this->app['cache']->changePrefix(
            Path::combine($this->resourceType->getName(), $this->getClientCurrentFolder()),
            Path::combine($this->resourceType->getName(), $newClientPath));

        return array(
            'newName' => $newName,
            'newPath' => $newClientPath,
            'renamed' => 1
        );
    }

    /**
     * Returns the URL to a given file.
     *
     * @param string      $fileName
     * @param string|null $thumbnailFileName
     *
     * @throws FileNotFoundException
     * @throws InvalidExtensionException
     * @throws InvalidRequestException
     *
     * @return null|string
     */
    public function getFileUrl($fileName, $thumbnailFileName = null)
    {
        $config = $this->app['config'];

        if (!File::isValidName($fileName, $config->get('disallowUnsafeCharacters'))) {
            throw new InvalidRequestException('Invalid file name');
        }

        if ($thumbnailFileName) {
            if (!File::isValidName($thumbnailFileName, $config->get('disallowUnsafeCharacters'))) {
                throw new InvalidRequestException('Invalid thumbnail file name');
            }

            if (!$this->resourceType->isAllowedExtension(pathinfo($thumbnailFileName, PATHINFO_EXTENSION))) {
                throw new InvalidExtensionException('Invalid thumbnail file name');
            }
        }

        if (!$this->containsFile($fileName)) {
            throw new FileNotFoundException();
        }

        return $this->backend->getFileUrl($this->resourceType, $this->getClientCurrentFolder(), $fileName, $thumbnailFileName);
    }

    /**
     * @return ResizedImageRepository
     */
    public function getResizedImageRepository()
    {
        return $this->app['resized_image_repository'];
    }

    /**
     * Tells the current WorkingFolder object to not add the current folder
     * to the response.
     *
     * By default the WorkingFolder object acts as an event subscriber and
     * listens for the `KernelEvents::RESPONSE` event. The response given is
     * then modified by adding information about the current folder.
     *
     * @see WorkingFolder::addCurrentFolderInfo()
     */
    public function omitResponseInfo()
    {
        $this->app['dispatcher']->removeSubscriber($this);
    }

    /**
     * Adds the current folder information to the response.
     *
     * @param FilterResponseEvent $event
     */
    public function addCurrentFolderInfo(FilterResponseEvent $event)
    {
        /* @var JsonResponse $response */
        $response = $event->getResponse();

        if ($response instanceof JsonResponse) {
            $responseData = (array) $response->getData();

            $responseData = array(
                    'resourceType' => $this->getResourceTypeName(),
                    'currentFolder' => array(
                        'path' => $this->getClientCurrentFolder(),
                        'acl' => $this->getAclMask()
                    )
                ) + $responseData;

            $baseUrl = $this->backend->getBaseUrl();

            if (null !== $baseUrl) {
                $folderUrl = Path::combine($baseUrl, Utils::encodeURLParts(Path::combine($this->resourceType->getDirectory(), $this->getClientCurrentFolder())));
                $responseData['currentFolder']['url'] = rtrim($folderUrl, '/') . '/';
            }

            $response->setData($responseData);
        }
    }

    /**
     * Returns listeners for the event dispatcher.
     *
     * @return array subscribed events
     */
    public static function getSubscribedEvents()
    {
        return array(KernelEvents::RESPONSE => array('addCurrentFolderInfo', 512));
    }
}
