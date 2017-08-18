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
use CKSource\CKFinder\Filesystem\Folder\WorkingFolder;
use Symfony\Component\HttpFoundation\Request;

class GetFileUrl extends CommandAbstract
{
    protected $requires = array(Permission::FILE_VIEW);

    public function execute(WorkingFolder $workingFolder, Request $request)
    {
        $fileName = (string) $request->get('fileName');
        $thumbnail = (string) $request->get('thumbnail');

        $fileNames = (array) $request->get('fileNames');

        if (!empty($fileNames)) {
            $urls = array();

            foreach ($fileNames as $fileName) {
                $urls[$fileName] = $workingFolder->getFileUrl($fileName);
            }

            return array('urls' => $urls);
        }

        return array(
            'url' => $workingFolder->getFileUrl($fileName, $thumbnail)
        );
    }
}
