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

use CKSource\CKFinder\Exception\InvalidUploadException;

/**
 * The EditedImage class.
 *
 * Represents an image file that is edited.
 */
class EditedImage extends EditedFile
{
    /**
     * @var int
     */
    protected $newWidth;

    /**
     * @var int
     */
    protected $newHeight;

    /**
     * Sets new image dimensions.
     *
     * @param int $newWidth
     * @param int $newHeight
     */
    public function setNewDimensions($newWidth, $newHeight)
    {
        $this->newWidth = $newWidth;
        $this->newHeight = $newHeight;
    }

    /**
     * @copydoc EditedFile::isValid()
     */
    public function isValid()
    {
        $imagesConfig = $this->config->get('images');

        if ($imagesConfig['maxWidth'] && $this->newWidth > $imagesConfig['maxWidth'] ||
            $imagesConfig['maxHeight'] && $this->newHeight > $imagesConfig['maxHeight']) {
            throw new InvalidUploadException('The image dimensions exceeds images.maxWidth or images.maxHeight');
        }

        return parent::isValid();
    }
}
