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

namespace CKSource\CKFinder\Event;

use CKSource\CKFinder\CKFinder;
use CKSource\CKFinder\Filesystem\Folder\WorkingFolder;

/**
 * The CreateFolderEvent event class.
 */
class CreateFolderEvent extends CKFinderEvent
{
    /**
     * The working folder where the new folder is going to be created.
     *
     * @var WorkingFolder $workingFolder
     */
    protected $workingFolder;

    /**
     * The new folder name.
     *
     * @var string
     */
    protected $newFolderName;

    /**
     * Constructor.
     *
     * @param CKFinder      $app
     * @param WorkingFolder $workingFolder
     * @param string        $newFolderName
     */
    public function __construct(CKFinder $app, WorkingFolder $workingFolder, $newFolderName)
    {
        $this->workingFolder = $workingFolder;
        $this->newFolderName = $newFolderName;

        parent::__construct($app);
    }

    /**
     * Returns the working folder where the new folder is going to be created.
     *
     * @return WorkingFolder
     */
    public function getWorkingFolder()
    {
        return $this->workingFolder;
    }

    /**
     * Returns the name of the new folder.
     *
     * @return string
     */
    public function getNewFolderName()
    {
        return $this->newFolderName;
    }

    /**
     * Sets the name for the new folder.
     *
     * @param string $newFolderName
     */
    public function setNewFolderName($newFolderName)
    {
        $this->newFolderName = $newFolderName;
    }
}
