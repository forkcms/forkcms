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
use CKSource\CKFinder\Error;
use CKSource\CKFinder\Exception\AlreadyExistsException;
use CKSource\CKFinder\Exception\FileNotFoundException;
use CKSource\CKFinder\Exception\InvalidExtensionException;
use CKSource\CKFinder\Exception\InvalidNameException;
use CKSource\CKFinder\Exception\InvalidRequestException;
use CKSource\CKFinder\Exception\InvalidUploadException;
use CKSource\CKFinder\Filesystem\Folder\WorkingFolder;
use CKSource\CKFinder\Filesystem\Path;
use CKSource\CKFinder\Utils;

/**
 * The EditedFile class.
 *
 * Represents an existing file being edited, i.e. content
 * of the file is going to be replaced with new content.
 */
class EditedFile extends ExistingFile
{
    /**
     * @var WorkingFolder
     */
    protected $workingFolder;

    /**
     * @var string
     */
    protected $newFileName;

    /**
     * @var bool
     */
    protected $saveAsNew = false;

    /**
     * New file content to be saved.
     *
     * @var string
     */
    protected $newContents;

    /**
     * @param string   $fileName
     * @param CKFinder $app
     * @param null     $newFileName
     */
    public function __construct($fileName, CKFinder $app, $newFileName = null)
    {
        $this->workingFolder = $app['working_folder'];
        $this->newFileName = $newFileName;

        parent::__construct($fileName, $this->workingFolder->getClientCurrentFolder(), $this->workingFolder->getResourceType(), $app);
    }

    /**
     * Validates the file.
     *
     * @return bool `true` if the file passed validation.
     *
     * @throws AlreadyExistsException
     * @throws FileNotFoundException
     * @throws InvalidExtensionException
     * @throws InvalidNameException
     * @throws InvalidRequestException
     * @throws InvalidUploadException
     */
    public function isValid()
    {
        if ($this->newFileName) {
            if (!File::isValidName($this->newFileName, $this->config->get('disallowUnsafeCharacters'))) {
                throw new InvalidNameException('Invalid file name');
            }

            if ($this->resourceType->getBackend()->isHiddenFile($this->newFileName)) {
                throw new InvalidRequestException('New provided file name is hidden');
            }

            if (!$this->resourceType->isAllowedExtension($this->getNewExtension())) {
                throw new InvalidExtensionException();
            }

            if ($this->config->get('checkDoubleExtension') && !$this->areValidDoubleExtensions($this->newFileName)) {
                throw new InvalidExtensionException();
            }

            if ($this->workingFolder->containsFile($this->newFileName)) {
                throw new AlreadyExistsException('File already exists');
            }
        }

        if (!$this->hasValidFilename() || !$this->hasValidPath()) {
            throw new InvalidRequestException('Invalid filename or path');
        }

        if ($this->isHidden() || $this->hasHiddenPath()) {
            throw new InvalidRequestException('Edited file is hidden');
        }

        if ($this->config->get('checkDoubleExtension') && !$this->areValidDoubleExtensions()) {
            throw new InvalidExtensionException();
        }

        if (!$this->resourceType->isAllowedExtension($this->getExtension())) {
            throw new InvalidExtensionException();
        }

        if (!$this->saveAsNew && !$this->exists()) {
            throw new FileNotFoundException();
        }

        if ($this->newContents) {
            if (Utils::containsHtml(substr($this->newContents, 0, 1024)) &&
                !in_array(strtolower($this->newFileName ? $this->getNewExtension() : $this->getExtension()), $this->config->get('htmlExtensions'))) {
                throw new InvalidUploadException('HTML detected in disallowed file type', Error::UPLOADED_WRONG_HTML_FILE);
            }

            $maxFileSize = $this->resourceType->getMaxSize();

            if ($maxFileSize && strlen($this->newContents) > $maxFileSize) {
                throw new InvalidUploadException('Uploaded file is too big', Error::UPLOADED_TOO_BIG);
            }
        }

        return true;
    }

    /**
     * Checks double extensions in a given file name.
     *
     * @param null|string $fileName file name or null if the current file name is checked.
     *
     * @return bool `true` if extensions are allowed for the current resource type.
     */
    protected function areValidDoubleExtensions($fileName = null)
    {
        $extensions = $this->getExtensions($fileName);

        foreach ($extensions as $ext) {
            if (!$this->resourceType->isAllowedExtension($ext)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns the new file name of the edited file.
     *
     * @return null|string the new file name of the edited file.
     */
    public function getNewFilename()
    {
        return $this->newFileName;
    }

    /**
     * Returns the new file extension.
     */
    public function getNewExtension()
    {
        return $this->newFileName ? pathinfo($this->newFileName, PATHINFO_EXTENSION) : null;
    }

    /**
     * Sets the flag if the edited file is saved as new and does not exist in the file system yet.
     *
     * @param bool $saveAsNew
     */
    public function saveAsNew($saveAsNew)
    {
        $this->saveAsNew = $saveAsNew;
    }

    /**
     * Sets new file contents.
     *
     * @param string      $contents new file contents
     * @param string|null $filePath optional path if new contents should be saved in a new file.
     *
     * @return bool
     */
    public function save($contents, $filePath = null)
    {
        return parent::save($contents, $this->newFileName ? Path::combine($this->getPath(), $this->newFileName) : null);
    }

    /**
     * Sets new contents for the edited file.
     *
     * @param string $newContents
     */
    public function setNewContents($newContents)
    {
        $this->newContents = $newContents;
    }

    /**
     * Returns new contents set for the edited file.
     *
     * @return string
     */
    public function getNewContents()
    {
        return $this->newContents;
    }

    /**
     * Returns the folder of the edited file.
     *
     * @return WorkingFolder
     */
    public function getWorkingFolder()
    {
        return $this->workingFolder;
    }
}
