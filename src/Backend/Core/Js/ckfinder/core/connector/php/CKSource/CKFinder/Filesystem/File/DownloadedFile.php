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
use CKSource\CKFinder\Exception\FileNotFoundException;
use CKSource\CKFinder\Exception\InvalidExtensionException;
use CKSource\CKFinder\Exception\InvalidNameException;
use CKSource\CKFinder\Filesystem\Folder\WorkingFolder;

/**
 * The DownloadedFile class.
 *
 * Represents downloaded file
 */
class DownloadedFile extends ExistingFile
{
    /**
     * @var WorkingFolder $workingFolder
     */
    protected $workingFolder;

    /**
     * Constructor.
     *
     * @param string        $fileName
     * @param CKFinder      $app
     */
    public function __construct($fileName, CKFinder $app)
    {
        $this->workingFolder = $app['working_folder'];

        parent::__construct($fileName, $this->workingFolder->getClientCurrentFolder(), $this->workingFolder->getResourceType(), $app);
    }

    /**
     * Returns the folder of the downloaded file.
     *
     * @return WorkingFolder
     */
    public function getWorkingFolder()
    {
        return $this->workingFolder;
    }

    /**
     * Validates the downloaded file.
     *
     * @throws \Exception
     *
     * @return boolean `true` if the file passed validation.
     */
    public function isValid()
    {
        if (!$this->hasValidFilename()) {
            throw new InvalidNameException('Invalid file name');
        }

        if (!$this->hasAllowedExtension()) {
            throw new InvalidExtensionException();
        }

        if ($this->isHidden() || !$this->exists()) {
            throw new FileNotFoundException();
        }

        return true;
    }

    /**
     * Checks if the file extension is allowed.
     *
     * @return bool `true` if an extension is allowed.
     */
    public function hasAllowedExtension()
    {
        if (strpos($this->fileName, '.') === false) {
            return true;
        }

        $extension = $this->getExtension();

        return $this->workingFolder->getResourceType()->isAllowedExtension($extension);
    }

    /**
     * Checks if the file is hidden.
     *
     * @return bool `true` if the file is hidden.
     */
    public function isHidden()
    {
        return $this->workingFolder->getBackend()->isHiddenFile($this->fileName);
    }

    /**
     * Checks if the file exists.
     *
     * @return bool `true` if the file exists.
     */
    public function exists()
    {
        return $this->workingFolder->containsFile($this->fileName);
    }
}
