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
use CKSource\CKFinder\Filesystem\File\EditedFile;

/**
 * The EditFileEvent event class.
 */
class EditFileEvent extends CKFinderEvent
{
    /**
     * @var EditedFile $uploadedFile
     */
    protected $editedFile;

    /**
     * @var string $newContents
     */
    protected $newContents;

    /**
     * Constructor.
     *
     * @param CKFinder   $app
     * @param EditedFile $editedFile
     */
    public function __construct(CKFinder $app, EditedFile $editedFile)
    {
        $this->editedFile = $editedFile;

        parent::__construct($app);
    }

    /**
     * Returns the edited file object.
     *
     * @return EditedFile
     *
     * @deprecated Please use getFile() instead.
     */
    public function getEditedFile()
    {
        return $this->editedFile;
    }

    /**
     * Returns the edited file object.
     *
     * @return EditedFile
     */
    public function getFile()
    {
        return $this->editedFile;
    }

    /**
     * Returns new contents of the edited file.
     *
     * @return string
     */
    public function getNewContents()
    {
        return $this->editedFile->getNewContents();
    }

    /**
     * Sets new contents for the edited file.
     *
     * @param string $newContents
     */
    public function setNewContents($newContents)
    {
        $this->editedFile->setNewContents($newContents);
    }
}
