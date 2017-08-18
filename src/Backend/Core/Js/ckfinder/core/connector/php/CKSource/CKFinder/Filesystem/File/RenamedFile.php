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

namespace CKSource\CKFinder\Filesystem\File;

use CKSource\CKFinder\CKFinder;
use CKSource\CKFinder\Exception\AlreadyExistsException;
use CKSource\CKFinder\Exception\FileNotFoundException;
use CKSource\CKFinder\Exception\InvalidExtensionException;
use CKSource\CKFinder\Exception\InvalidNameException;
use CKSource\CKFinder\Exception\InvalidRequestException;
use CKSource\CKFinder\Filesystem\Path;
use CKSource\CKFinder\ResourceType\ResourceType;
use CKSource\CKFinder\Utils;

/**
 * The RenamedFile class.
 *
 * Represents the file being renamed.
 */
class RenamedFile extends ExistingFile
{
    /**
     * New file name.
     *
     * @var string $newFileName
     */
    protected $newFileName;

    /**
     * Constructor.
     *
     * @param string       $newFileName  new file name
     * @param string       $fileName     current file name
     * @param string       $folder       current file folder
     * @param ResourceType $resourceType current file resource type
     * @param CKFinder     $app          CKFinder app
     */
    public function __construct($newFileName, $fileName, $folder, ResourceType $resourceType, CKFinder $app)
    {
        parent::__construct($fileName, $folder, $resourceType, $app);

        $this->newFileName = static::secureName($newFileName, $this->config->get('disallowUnsafeCharacters'));

        if ($this->config->get('checkDoubleExtension')) {
            $this->newFileName = Utils::replaceDisallowedExtensions($this->newFileName, $resourceType);
        }
    }

    /**
     * Returns the new file name of the renamed file.
     *
     * @return string
     */
    public function getNewFileName()
    {
        return $this->newFileName;
    }

    /**
     * Returns the new path of the renamed file.
     *
     * @return string
     */
    public function getNewFilePath()
    {
        return Path::combine($this->getPath(), $this->getNewFileName());
    }

    /**
     * Sets the new file name of the renamed file.
     *
     * @param string $newFileName
     */
    public function setNewFileName($newFileName)
    {
        $this->newFileName = $newFileName;
    }

    /**
     * Renames the current file.
     *
     * @return bool `true` if the file was renamed successfully.
     *
     * @throws \Exception
     */
    public function doRename()
    {
        $oldPath = Path::combine($this->getPath(), $this->getFilename());
        $newPath = Path::combine($this->getPath(), $this->newFileName);

        $backend = $this->resourceType->getBackend();

        if ($backend->has($newPath)) {
            throw new AlreadyExistsException('Target file already exists');
        }

        $this->deleteThumbnails();
        $this->resourceType->getResizedImageRepository()->renameResizedImages(
            $this->resourceType,
            $this->folder,
            $this->getFilename(),
            $this->newFileName
        );

        $this->getCache()->move(
            Path::combine($this->resourceType->getName(), $this->folder, $this->getFilename()),
            Path::combine($this->resourceType->getName(), $this->folder, $this->newFileName));

        return $backend->rename($oldPath, $newPath);
    }

    /**
     * Validates the renamed file.
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function isValid()
    {
        $newExtension = pathinfo($this->newFileName, PATHINFO_EXTENSION);

        if (!$this->hasAllowedExtension()) {
            throw new InvalidRequestException('Invalid source file extension');
        }

        if (!$this->resourceType->isAllowedExtension($newExtension)) {
            throw new InvalidExtensionException('Invalid target file extension');
        }

        if (!$this->hasValidFilename() || $this->isHidden()) {
            throw new InvalidRequestException('Invalid source file name');
        }

        if (!File::isValidName($this->newFileName, $this->config->get('disallowUnsafeCharacters')) ||
            $this->resourceType->getBackend()->isHiddenFile($this->newFileName)) {
            throw new InvalidNameException('Invalid target file name');
        }

        if (!$this->exists()) {
            throw new FileNotFoundException();
        }

        return true;
    }
}
