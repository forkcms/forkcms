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
use CKSource\CKFinder\Filesystem\File\DeletedFile;

/**
 * The DeleteFileEvent event class.
 */
class DeleteFileEvent extends CKFinderEvent
{
    /**
     * @var DeletedFile $deletedFile
     */
    protected $deletedFile;

    /**
     * Constructor.
     *
     * @param CKFinder      $app
     * @param DeletedFile   $deletedFile
     */
    public function __construct(CKFinder $app, DeletedFile $deletedFile)
    {
        $this->deletedFile = $deletedFile;

        parent::__construct($app);
    }

    /**
     * Returns the deleted file object.
     *
     * @return DeletedFile
     *
     * @deprecated Please use getFile() instead.
     */
    public function getDeletedFile()
    {
        return $this->deletedFile;
    }

    /**
     * Returns the deleted file object.
     *
     * @return DeletedFile
     */
    public function getFile()
    {
        return $this->deletedFile;
    }
}
