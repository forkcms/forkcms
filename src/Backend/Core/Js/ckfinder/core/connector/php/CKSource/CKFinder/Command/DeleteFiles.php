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

namespace CKSource\CKFinder\Command;

use CKSource\CKFinder\Acl\Acl;
use CKSource\CKFinder\Acl\Permission;
use CKSource\CKFinder\Error;
use CKSource\CKFinder\Event\CKFinderEvent;
use CKSource\CKFinder\Event\DeleteFileEvent;
use CKSource\CKFinder\Exception\InvalidRequestException;
use CKSource\CKFinder\Exception\UnauthorizedException;
use CKSource\CKFinder\Filesystem\File\DeletedFile;
use CKSource\CKFinder\ResourceType\ResourceTypeFactory;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

class DeleteFiles extends CommandAbstract
{
    protected $requestMethod = Request::METHOD_POST;

    protected $requires = array(
        Permission::FILE_DELETE
    );

    public function execute(Request $request, ResourceTypeFactory $resourceTypeFactory, Acl $acl, EventDispatcher $dispatcher)
    {
        $deletedFiles = (array) $request->request->get('files');

        $deleted = 0;

        $errors = array();

        // Initial validation
        foreach ($deletedFiles as $arr) {
            if (!isset($arr['name'], $arr['type'], $arr['folder'])) {
                throw new InvalidRequestException('Invalid request');
            }

            if (!$acl->isAllowed($arr['type'], $arr['folder'], Permission::FILE_DELETE)) {
                throw new UnauthorizedException();
            }
        }

        foreach ($deletedFiles as $arr) {
            if (empty($arr['name'])) {
                continue;
            }

            $name   = $arr['name'];
            $type   = $arr['type'];
            $folder = $arr['folder'];

            $resourceType = $resourceTypeFactory->getResourceType($type);

            $deletedFile = new DeletedFile($name, $folder, $resourceType, $this->app);

            if ($deletedFile->isValid()) {
                $deleteFileEvent = new DeleteFileEvent($this->app, $deletedFile);
                $dispatcher->dispatch(CKFinderEvent::DELETE_FILE, $deleteFileEvent);

                if (!$deleteFileEvent->isPropagationStopped()) {
                    if ($deletedFile->doDelete()) {
                        $deleted++;
                    }
                }
            }

            $errors = array_merge($errors, $deletedFile->getErrors());
        }

        $data = array('deleted' => $deleted);

        if (!empty($errors)) {
            $data['error'] = array(
                'number' => Error::DELETE_FAILED,
                'errors' => $errors
            );
        }

        return $data;
    }
}
