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
use CKSource\CKFinder\Filesystem\File\RenamedFile;

/**
 * The RenameFileEvent event class.
 */
class RenameFileEvent extends CKFinderEvent
{
    /**
     * @var RenamedFile $renamedFile
     */
    protected $renamedFile;

    /**
     * Constructor.
     *
     * @param CKFinder    $app
     * @param RenamedFile $renamedFile
     */
    public function __construct(CKFinder $app, RenamedFile $renamedFile)
    {
        $this->renamedFile = $renamedFile;

        parent::__construct($app);
    }

    /**
     * Returns the renamed file object.
     *
     * @return RenamedFile
     *
     * @deprecated Please use getFile() instead.
     */
    public function getRenamedFile()
    {
        return $this->renamedFile;
    }

    /**
     * Returns the renamed file object.
     *
     * @return RenamedFile
     */
    public function getFile()
    {
        return $this->renamedFile;
    }
}
