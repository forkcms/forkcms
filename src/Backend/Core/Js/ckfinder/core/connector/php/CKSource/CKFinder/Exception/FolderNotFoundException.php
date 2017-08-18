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

namespace CKSource\CKFinder\Exception;

use CKSource\CKFinder\Error;
use Symfony\Component\HttpFoundation\Response;

/**
 * The "folder not found" exception class.
 *
 * Thrown when the requested folder cannot be found.
 *
 * @copyright 2016 CKSource - Frederico Knabben
 */
class FolderNotFoundException extends CKFinderException
{
    protected $httpStatusCode = Response::HTTP_NOT_FOUND;

    /**
     * Constructor.
     *
     * @param string     $message    the exception message
     * @param array      $parameters the parameters passed for translation
     * @param \Exception $previous   the previous exception
     */
    public function __construct($message = 'Folder not found', $parameters = array(), \Exception $previous = null)
    {
        parent::__construct($message, Error::FOLDER_NOT_FOUND, $parameters, $previous);
    }
}
