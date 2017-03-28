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

namespace CKSource\CKFinder\Acl;

/**
 * The Acl interface.
 * 
 * @copyright 2016 CKSource - Frederico Knabben
 */
interface AclInterface
{
    /**
     * Allows a permission in the chosen folder.
     * 
     * @param string $resourceType the resource type identifier (also `*` for all resource types)
     * @param string $folderPath   the folder path
     * @param int    $permission   the permission numeric value
     * @param string $role         the user role name (also `*` for all roles)
     * 
     * @return Acl $this
     * 
     * @see Permission
     */
    public function allow($resourceType, $folderPath, $permission, $role);

    /**
     * Disallows a permission in the chosen folder.
     * 
     * @param string $resourceType the resource type identifier (also `*` for all resource types)
     * @param string $folderPath   the folder path
     * @param int    $permission   the permission numeric value
     * @param string $role         the user role name (also `*` for all roles)
     * 
     * @return Acl $this
     * 
     * @see Permission
     */
    public function disallow($resourceType, $folderPath, $permission, $role);

    /**
     * Checks if a role has the required permission for a folder.
     * 
     * @param string $resourceType the resource type identifier (also `*` for all resource types)
     * @param string $folderPath   the folder path
     * @param int    $permission   the permission numeric value
     * @param string $role         the user role name (also `*` for all roles)
     * 
     * @return bool true if role has required permission
     * 
     * @see Permission
     */
    public function isAllowed($resourceType, $folderPath, $permission, $role = null);

    /**
     * Computes a mask based on the current user role and ACL rules.
     * 
     * @param string $resourceType the resource type identifier (also `*` for all resource types)
     * @param string $folderPath   the folder path
     * @param string $role         the user role name (also `*` for all roles)
     * 
     * @return int computed mask value
     * 
     * @see MaskBuilder
     */
    public function getComputedMask($resourceType, $folderPath, $role = null);
}
