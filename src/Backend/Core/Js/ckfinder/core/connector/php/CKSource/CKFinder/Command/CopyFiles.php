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
use CKSource\CKFinder\Event\CopyFileEvent;
use CKSource\CKFinder\Exception\InvalidRequestException;
use CKSource\CKFinder\Exception\UnauthorizedException;
use CKSource\CKFinder\Filesystem\File\CopiedFile;
use CKSource\CKFinder\ResourceType\ResourceTypeFactory;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

class CopyFiles extends CommandAbstract
{
    protected $requestMethod = Request::METHOD_POST;

    protected $requires = array(
        Permission::FILE_RENAME,
        Permission::FILE_CREATE,
        Permission::FILE_DELETE
    );

    public function execute(Request $request, ResourceTypeFactory $resourceTypeFactory, Acl $acl, EventDispatcher $dispatcher)
    {
        $copiedFiles = (array) $request->request->get('files');

        $copied = 0;

        $errors = array();

        // Initial validation
        foreach ($copiedFiles as $arr) {
            if (!isset($arr['name'], $arr['type'], $arr['folder'])) {
                throw new InvalidRequestException();
            }

            if (!$acl->isAllowed($arr['type'], $arr['folder'], Permission::FILE_VIEW)) {
                throw new UnauthorizedException();
            }
        }

        foreach ($copiedFiles as $arr) {
            if (empty($arr['name'])) {
                continue;
            }

            $name   = $arr['name'];
            $type   = $arr['type'];
            $folder = $arr['folder'];

            $resourceType = $resourceTypeFactory->getResourceType($type);

            $copiedFile = new CopiedFile($name, $folder, $resourceType, $this->app);

            $options = isset($arr['options']) ? $arr['options'] : '';

            $copiedFile->setCopyOptions($options);


            if ($copiedFile->isValid()) {
                $copyFileEvent = new CopyFileEvent($this->app, $copiedFile);
                $dispatcher->dispatch(CKFinderEvent::COPY_FILE, $copyFileEvent);

                if (!$copyFileEvent->isPropagationStopped()) {
                    if ($copiedFile->doCopy()) {
                        $copied++;
                    }
                }
            }

            $errors = array_merge($errors, $copiedFile->getErrors());
        }

        $data = array('copied' => $copied);

        if (!empty($errors)) {
            $data['error'] = array(
                'number' => Error::COPY_FAILED,
                'errors' => $errors
            );
        }

        return $data;
    }
}
