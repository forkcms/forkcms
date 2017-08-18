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

namespace CKSource\CKFinder\Plugin;

use CKSource\CKFinder\CKFinder;

/**
 * The Plugin interface.
 *
 * @copyright 2016 CKSource - Frederico Knabben
 */
interface PluginInterface
{
    /**
     * Injects the DI container to the plugin.
     *
     * @param CKFinder $app
     */
    public function setContainer(CKFinder $app);

    /**
     * Returns an array with the default configuration for this plugin. Any of
     * the plugin configuration options can be overwritten in the CKFinder configuration file.
     *
     * @return array default plugin configuration
     */
    public function getDefaultConfig();
}
