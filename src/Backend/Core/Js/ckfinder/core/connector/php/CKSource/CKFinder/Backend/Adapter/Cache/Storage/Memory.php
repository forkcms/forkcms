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

namespace CKSource\CKFinder\Backend\Adapter\Cache\Storage;

use League\Flysystem\Cached\Storage\Memory as MemoryBase;

/**
 * Cached adapter customization that resolves
 * https://github.com/thephpleague/flysystem-cached-adapter/issues/18
 */
class Memory extends MemoryBase
{
    /**
     * {@inheritdoc}
     */
    public function read($path)
    {
        if (isset($this->cache[$path]['contents']) && $this->cache[$path]['contents'] !== false) {
            return $this->cache[$path];
        }

        return false;
    }
}
