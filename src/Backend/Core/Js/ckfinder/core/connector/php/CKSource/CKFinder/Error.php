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

namespace CKSource\CKFinder;

/**
 * The Error class.
 * 
 * @copyright 2016 CKSource - Frederico Knabben
 */
class Error
{
    const NONE                          = 0;
    const CUSTOM_ERROR                  = 1;
    const INVALID_COMMAND               = 10;
    const TYPE_NOT_SPECIFIED            = 11;
    const INVALID_TYPE                  = 12;
    const INVALID_CONFIG                = 13;
    const INVALID_PLUGIN                = 14;
    const INVALID_NAME                  = 102;
    const UNAUTHORIZED                  = 103;
    const ACCESS_DENIED                 = 104;
    const INVALID_EXTENSION             = 105;
    const INVALID_REQUEST               = 109;
    const UNKNOWN                       = 110;
    const CREATED_FILE_TOO_BIG          = 111;
    const ALREADY_EXIST                 = 115;
    const FOLDER_NOT_FOUND              = 116;
    const FILE_NOT_FOUND                = 117;
    const SOURCE_AND_TARGET_PATH_EQUAL  = 118;
    const UPLOADED_FILE_RENAMED         = 201;
    const UPLOADED_INVALID              = 202;
    const UPLOADED_TOO_BIG              = 203;
    const UPLOADED_CORRUPT              = 204;
    const UPLOADED_NO_TMP_DIR           = 205;
    const UPLOADED_WRONG_HTML_FILE      = 206;
    const UPLOADED_INVALID_NAME_RENAMED = 207;
    const MOVE_FAILED                   = 300;
    const COPY_FAILED                   = 301;
    const DELETE_FAILED                 = 302;
    const ZIP_FAILED                    = 303;
    const CONNECTOR_DISABLED            = 500;
    const THUMBNAILS_DISABLED           = 501;
}
