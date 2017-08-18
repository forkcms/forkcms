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

use CKSource\CKFinder\Acl\Permission;
use CKSource\CKFinder\Event\CKFinderEvent;
use CKSource\CKFinder\Event\CreateFolderEvent;
use CKSource\CKFinder\Filesystem\Folder\WorkingFolder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

class CreateFolder extends CommandAbstract
{
    protected $requestMethod = Request::METHOD_POST;

    protected $requires = array(Permission::FOLDER_CREATE);

    public function execute(Request $request, WorkingFolder $workingFolder, EventDispatcher $dispatcher)
    {
        $newFolderName = (string) $request->query->get('newFolderName', '');

        $createFolderEvent = new CreateFolderEvent($this->app, $workingFolder, $newFolderName);

        $dispatcher->dispatch(CKFinderEvent::CREATE_FOLDER, $createFolderEvent);

        $created = false;

        if (!$createFolderEvent->isPropagationStopped()) {
            $newFolderName = $createFolderEvent->getNewFolderName();
            $created = $workingFolder->createDir($newFolderName);
        }

        return array('newFolder' => $newFolderName, 'created' => (int) $created);
    }
}
