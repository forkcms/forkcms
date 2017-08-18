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
use CKSource\CKFinder\CKFinder;
use CKSource\CKFinder\Config;
use CKSource\CKFinder\Filesystem\Path;
use CKSource\CKFinder\ResourceType\ResourceTypeFactory;
use CKSource\CKFinder\Utils;
use Symfony\Component\HttpFoundation\Request;

class Init extends CommandAbstract
{
    public function execute(Request $request, Acl $acl, Config $config, ResourceTypeFactory $resourceTypeFactory)
    {
        $data = new \stdClass();

        /**
         * The connector is always enabled here.
         *
         * @see CKFinder::checkAuth()
         */
        $data->enabled = true;

        $ln = '';
        $lc = str_replace('-', '', ($config->get('licenseKey') ?: $config->get('LicenseKey')) . '                                  ');
        $pos = strpos(CKFinder::CHARS, $lc[2]) % 5;

        if ($pos == 1 || $pos == 2) {
            $ln = $config->get('licenseName') ?: $config->get('LicenseName');
        }

        $data->s = $ln;
        $data->c = trim($lc[1] . $lc[8] . $lc[17] . $lc[22] . $lc[3] . $lc[13] . $lc[11] . $lc[20] . $lc[5] . $lc[24] . $lc[27]);

        // Thumbnails
        $thumbnailsConfig = $config->get('thumbnails');

        $thumbnailsEnabled = (bool) $thumbnailsConfig['enabled'];

        if ($thumbnailsEnabled) {
            $sizes = array();
            foreach ($thumbnailsConfig['sizes'] as $sizeInfo) {
                $sizes[] = sprintf("%dx%d", $sizeInfo['width'], $sizeInfo['height']);
            }

            $data->thumbs = $sizes;
        }

        // Images
        $imagesConfig = $config->get('images');

        $images = array(
            'max' => $imagesConfig['maxWidth'] . 'x' . $imagesConfig['maxHeight']
        );

        if (isset($imagesConfig['sizes'])) {
            $resize = array();

            foreach ($imagesConfig['sizes'] as $name => $sizeInfo) {
                $resize[$name] = $sizeInfo['width'] . 'x' . $sizeInfo['height'];
            }

            $images['sizes'] = $resize;
        }

        $data->images = $images;

        $resourceTypesNames = $config->getDefaultResourceTypes() ? : $config->getResourceTypes();

        $data->resourceTypes = array();

        if (!empty($resourceTypesNames)) {
            $phpMaxSize = 0;

            $maxUpload = Utils::returnBytes(ini_get('upload_max_filesize'));
            if ($maxUpload) {
                $phpMaxSize = $maxUpload;
            }

            $maxPost = Utils::returnBytes(ini_get('post_max_size'));
            if ($maxPost) {
                $phpMaxSize = $phpMaxSize ? min($phpMaxSize, $maxPost) : $maxPost;
            }

            //ini_get('memory_limit') only works if compiled with "--enable-memory-limit"
            $memoryLimit = Utils::returnBytes(@ini_get('memory_limit'));
            if ($memoryLimit && $memoryLimit != -1) {
                $phpMaxSize = $phpMaxSize ? min($phpMaxSize, $memoryLimit) : $memoryLimit;
            }

            $data->uploadMaxSize = $phpMaxSize;
            $data->uploadCheckImages = !$config->get('checkSizeAfterScaling');

            $requestedType = (string) $request->query->get('type');

            foreach ($resourceTypesNames as $resourceTypeName) {
                if ($requestedType && $requestedType !== $resourceTypeName) {
                    continue;
                }

                $aclMask = $acl->getComputedMask($resourceTypeName, '/');

                if (!(Permission::FOLDER_VIEW & $aclMask)) {
                    continue;
                }

                $resourceType = $resourceTypeFactory->getResourceType($resourceTypeName);

                $resourceTypeObject = array(
                    'name'              => $resourceTypeName,
                    'allowedExtensions' => implode(",", $resourceType->getAllowedExtensions()),
                    'deniedExtensions'  => implode(",", $resourceType->getDeniedExtensions()),
                    'hash'              => $resourceType->getHash(),
                    'acl'               => $aclMask,
                    'maxSize'           => $resourceType->getMaxSize() ? min($resourceType->getMaxSize(), $phpMaxSize) : $phpMaxSize
                );

                $resourceTypeBackend = $resourceType->getBackend();

                if ($resourceType->isLazyLoaded()) {
                    $resourceTypeObject['hasChildren'] = false;
                    $resourceTypeObject['lazyLoad'] = true;
                } else {
                    $resourceTypeObject['hasChildren'] = $resourceTypeBackend->containsDirectories($resourceType, $resourceType->getDirectory());
                }

                if ($label = $resourceType->getLabel()) {
                    $resourceTypeObject['label'] = $label;
                }

                $useProxyCommand = $resourceTypeBackend->usesProxyCommand();

                if ($useProxyCommand) {
                    $resourceTypeObject['useProxyCommand'] = true;
                } else {
                    $baseUrl = $resourceTypeBackend->getBaseUrl();

                    if ($baseUrl) {
                        $resourceTypeObject['url'] = rtrim(Path::combine($baseUrl, $resourceType->getDirectory()), '/') . '/';
                    }
                }


                $trackedOperations = $resourceTypeBackend->getTrackedOperations();

                if (!empty($trackedOperations)) {
                    $resourceTypeObject['trackedOperations'] = $trackedOperations;
                }

                $data->resourceTypes[] = $resourceTypeObject;
            }
        }

        $enabledPlugins = $config->get('plugins');

        if (!empty($enabledPlugins)) {
            $data->plugins = $enabledPlugins;
        }

        $proxyCacheLifetime = (int) $config->get('cache.proxyCommand');

        if ($proxyCacheLifetime) {
            $data->proxyCache = $proxyCacheLifetime;
        }

        return $data;
    }
}
