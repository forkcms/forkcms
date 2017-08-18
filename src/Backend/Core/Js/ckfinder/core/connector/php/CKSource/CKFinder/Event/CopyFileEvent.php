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
use CKSource\CKFinder\Filesystem\File\CopiedFile;

/**
 * The CopyFileEvent event class.
 */
class CopyFileEvent extends CKFinderEvent
{
    /**
     * @var CopiedFile $copiedFile
     */
    protected $copiedFile;

    /**
     * Constructor.
     *
     * @param CKFinder     $app
     * @param CopiedFile   $copiedFile
     */
    public function __construct(CKFinder $app, CopiedFile $copiedFile)
    {
        $this->copiedFile = $copiedFile;

        parent::__construct($app);
    }

    /**
     * Returns the copied file object.
     *
     * @return CopiedFile
     *
     * @deprecated Please use getFile() instead.
     */
    public function getCopiedFile()
    {
        return $this->copiedFile;
    }

    /**
     * Returns the copied file object.
     *
     * @return CopiedFile
     */
    public function getFile()
    {
        return $this->copiedFile;
    }
}
