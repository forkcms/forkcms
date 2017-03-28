<?php
/*
 * CKFinder
 * ========
 * http://cksource.com/ckfinder
 * Copyright (c) 2007-2016, CKSource - Frederico Knabben. All rights reserved.
 *
 * The software, this file and its contents are subject to the CKFinder
 * License. Please read the license.txt file before using, installing, copying,
 * modifying or distribute this file or part of its contents. The contents of
 * this file is part of the Source Code of CKFinder.
 */

require_once __DIR__ . '/vendor/autoload.php';

use CKSource\CKFinder\CKFinder;

$ckfinder = new CKFinder(__DIR__ . '/../../../config.php');

$ckfinder->run();
