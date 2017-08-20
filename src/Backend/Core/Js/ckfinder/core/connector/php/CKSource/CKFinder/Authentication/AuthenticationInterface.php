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

namespace CKSource\CKFinder\Authentication;

/**
 * The AuthenticationInterface interface.
 *
 * An interface for authentication methods.
 */
interface AuthenticationInterface
{
    /**
     * @return bool `true` if the current user was successfully authenticated within CKFinder.
     */
    public function authenticate();
}
