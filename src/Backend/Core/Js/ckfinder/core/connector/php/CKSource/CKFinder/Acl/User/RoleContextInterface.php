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

namespace CKSource\CKFinder\Acl\User;

/**
 * The role context interface.
 * 
 * You can implement this interface to get the current user role in your application.
 * By default Access Control Lists use SessionRoleContext to get the user role from the
 * defined $_SESSION field.
 * 
 * @copyright 2016 CKSource - Frederico Knabben
 */
interface RoleContextInterface
{
    /**
     * Returns the name of the current user role.
     * 
     * @return string|null the current user role name or `null`
     */
    public function getRole();
}
