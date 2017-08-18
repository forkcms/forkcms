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
use CKSource\CKFinder\ResizedImage\ResizedImage;

/**
 * The DownloadFileEvent event class.
 */
class ProxyDownloadEvent extends CKFinderEvent
{
    /**
     * @var DownloadedFile|ResizedImage $downloadedFile
     */
    protected $downloadedFile;

    /**
     * Constructor.
     *
     * @param CKFinder                    $app
     * @param DownloadedFile|ResizedImage $downloadedFile
     */
    public function __construct(CKFinder $app, $downloadedFile)
    {
        $this->downloadedFile = $downloadedFile;

        parent::__construct($app);
    }

    /**
     * Returns the downloaded file object.
     *
     * @return DownloadedFile|ResizedImage
     */
    public function getFile()
    {
        return $this->downloadedFile;
    }
}
