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
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

/**
 * The CKFinderEvent class.
 *
 * The base class for all CKFinder events.
 */
class CKFinderEvent extends Event
{
    /**
     * The beforeCommand events.
     *
     * These events occur before a command is executed, after a particular
     * command is resolved, i.e. it is decided which command class should be used
     * to handle the current request.
     */
    const BEFORE_COMMAND_PREFIX             = 'ckfinder.beforeCommand.';

    const BEFORE_COMMAND_INIT               = 'ckfinder.beforeCommand.init';
    const BEFORE_COMMAND_COPY_FILES         = 'ckfinder.beforeCommand.copyFiles';
    const BEFORE_COMMAND_CREATE_FOLDER      = 'ckfinder.beforeCommand.createFolder';
    const BEFORE_COMMAND_DELETE_FILES       = 'ckfinder.beforeCommand.deleteFiles';
    const BEFORE_COMMAND_DELETE_FOLDER      = 'ckfinder.beforeCommand.deleteFolder';
    const BEFORE_COMMAND_DOWNLOAD_FILE      = 'ckfinder.beforeCommand.downloadFile';
    const BEFORE_COMMAND_FILE_UPLOAD        = 'ckfinder.beforeCommand.fileUpload';
    const BEFORE_COMMAND_GET_FILES          = 'ckfinder.beforeCommand.getFiles';
    const BEFORE_COMMAND_GET_FILE_URL       = 'ckfinder.beforeCommand.getFileUrl';
    const BEFORE_COMMAND_GET_FOLDERS        = 'ckfinder.beforeCommand.getFolders';
    const BEFORE_COMMAND_GET_RESIZED_IMAGES = 'ckfinder.beforeCommand.getResizedImages';
    const BEFORE_COMMAND_IMAGE_EDIT         = 'ckfinder.beforeCommand.imageEdit';
    const BEFORE_COMMAND_IMAGE_INFO         = 'ckfinder.beforeCommand.imageInfo';
    const BEFORE_COMMAND_IMAGE_PREVIEW      = 'ckfinder.beforeCommand.imagePreview';
    const BEFORE_COMMAND_IMAGE_RESIZE       = 'ckfinder.beforeCommand.imageResize';
    const BEFORE_COMMAND_MOVE_FILES         = 'ckfinder.beforeCommand.moveFiles';
    const BEFORE_COMMAND_QUICK_UPLOAD       = 'ckfinder.beforeCommand.quickUpload';
    const BEFORE_COMMAND_RENAME_FILE        = 'ckfinder.beforeCommand.renameFile';
    const BEFORE_COMMAND_RENAME_FOLDER      = 'ckfinder.beforeCommand.renameFolder';
    const BEFORE_COMMAND_SAVE_IMAGE         = 'ckfinder.beforeCommand.saveImage';
    const BEFORE_COMMAND_THUMBNAIL          = 'ckfinder.beforeCommand.thumbnail';

    /**
     * Intermediate events.
     */
    const COPY_FILE              = 'ckfinder.copyFiles.copy';
    const CREATE_FOLDER          = 'ckfinder.createFolder.create';
    const DELETE_FILE            = 'ckfinder.deleteFiles.delete';
    const DELETE_FOLDER          = 'ckfinder.deleteFolder.delete';
    const DOWNLOAD_FILE          = 'ckfinder.downloadFile.download';
    const PROXY_DOWNLOAD         = 'ckfinder.proxy.download';
    const FILE_UPLOAD            = 'ckfinder.uploadFile.upload';
    const MOVE_FILE              = 'ckfinder.moveFiles.move';
    const RENAME_FILE            = 'ckfinder.renameFile.rename';
    const RENAME_FOLDER          = 'ckfinder.renameFolder.rename';
    const SAVE_IMAGE             = 'ckfinder.saveImage.save';
    const EDIT_IMAGE             = 'ckfinder.imageEdit.save';
    const CREATE_THUMBNAIL       = 'ckfinder.thumbnail.createThumbnail';
    const CREATE_RESIZED_IMAGE   = 'ckfinder.imageResize.createResizedImage';

    const CREATE_RESPONSE_PREFIX = 'ckfinder.createResponse.';

    /**
     * The afterCommand events.
     *
     * These events occur after a command execution, when a response for
     * a command was created.
     */
    const AFTER_COMMAND_PREFIX             = 'ckfinder.afterCommand.';

    const AFTER_COMMAND_INIT               = 'ckfinder.afterCommand.init';
    const AFTER_COMMAND_COPY_FILES         = 'ckfinder.afterCommand.copyFiles';
    const AFTER_COMMAND_CREATE_FOLDER      = 'ckfinder.afterCommand.createFolder';
    const AFTER_COMMAND_DELETE_FILES       = 'ckfinder.afterCommand.deleteFiles';
    const AFTER_COMMAND_DELETE_FOLDER      = 'ckfinder.afterCommand.deleteFolder';
    const AFTER_COMMAND_DOWNLOAD_FILE      = 'ckfinder.afterCommand.downloadFile';
    const AFTER_COMMAND_FILE_UPLOAD        = 'ckfinder.afterCommand.fileUpload';
    const AFTER_COMMAND_GET_FILES          = 'ckfinder.afterCommand.getFiles';
    const AFTER_COMMAND_GET_FILE_URL       = 'ckfinder.afterCommand.getFileUrl';
    const AFTER_COMMAND_GET_FOLDERS        = 'ckfinder.afterCommand.getFolders';
    const AFTER_COMMAND_GET_RESIZED_IMAGES = 'ckfinder.afterCommand.getResizedImages';
    const AFTER_COMMAND_IMAGE_EDIT         = 'ckfinder.afterCommand.imageEdit';
    const AFTER_COMMAND_IMAGE_INFO         = 'ckfinder.afterCommand.imageInfo';
    const AFTER_COMMAND_IMAGE_PREVIEW      = 'ckfinder.afterCommand.imagePreview';
    const AFTER_COMMAND_IMAGE_RESIZE       = 'ckfinder.afterCommand.imageResize';
    const AFTER_COMMAND_MOVE_FILES         = 'ckfinder.afterCommand.moveFiles';
    const AFTER_COMMAND_QUICK_UPLOAD       = 'ckfinder.afterCommand.quickUpload';
    const AFTER_COMMAND_RENAME_FILE        = 'ckfinder.afterCommand.renameFile';
    const AFTER_COMMAND_RENAME_FOLDER      = 'ckfinder.afterCommand.renameFolder';
    const AFTER_COMMAND_SAVE_IMAGE         = 'ckfinder.afterCommand.saveImage';
    const AFTER_COMMAND_THUMBNAIL          = 'ckfinder.afterCommand.thumbnail';

    /**
     * The CKFinder instance.
     *
     * @var CKFinder $app
     */
    protected $app;

    /**
     * Constructor.
     *
     * @param CKFinder $app
     */
    public function __construct(CKFinder $app)
    {
        $this->app = $app;
    }

    /**
     * Returns the application dependency injection container.
     *
     * @return CKFinder
     */
    public function getContainer()
    {
        return $this->app;
    }

    /**
     * Returns the current request object.
     *
     * @return Request|null
     */
    public function getRequest()
    {
        return $this->app['request_stack']->getCurrentRequest();
    }
}
