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
use CKSource\CKFinder\Filesystem\File\DownloadedFile;

/**
 * The DownloadFileEvent event class.
 */
class DownloadFileEvent extends CKFinderEvent
{
    /**
     * @var DownloadedFile $downloadedFile
     */
    protected $downloadedFile;

    /**
     * Constructor.
     *
     * @param CKFinder       $app
     * @param DownloadedFile $downloadedFile
     */
    public function __construct(CKFinder $app, DownloadedFile $downloadedFile)
    {
        $this->downloadedFile = $downloadedFile;

        parent::__construct($app);
    }

    /**
     * Returns the downloaded file object.
     *
     * @return DownloadedFile
     *
     * @deprecated Please use getFile() instead.
     */
    public function getDownloadedFile()
    {
        return $this->downloadedFile;
    }

    /**
     * Returns the downloaded file object.
     *
     * @return DownloadedFile
     */
    public function getFile()
    {
        return $this->downloadedFile;
    }
}
