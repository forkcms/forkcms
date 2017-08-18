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
 * The MaskBuilder class.
 * 
 * A class used to build access control masks for folder access management.
 * Two masks are used to handle access rule inheritance from parent directories.
 * 
 * @copyright 2016 CKSource - Frederico Knabben
 */
class MaskBuilder
{
    /**
     * A mask for allowed permissions.
     * 
     * @var int $maskAllowed
     */
    protected $maskAllowed = 0;

    /**
     * @brief A mask for disallowed permissions.
     * 
     * @var int $maskDisallowed
     */
    protected $maskDisallowed = 0;

    /**
     * Enables the permission bit in the mask for allowed permissions.
     * 
     * @param int $permission permission numeric value
     *
     * @see Permission
     * 
     * @return MaskBuilder $this
     */
    public function allow($permission)
    {
        $this->maskAllowed |= $permission;

        return $this;
    }

    /**
     * Enables the permission bit in the mask for disallowed permissions.
     * 
     * @param int $permission permission numeric value
     *
     * @see Permission
     * 
     * @return MaskBuilder $this
     */
    public function disallow($permission)
    {
        $this->maskDisallowed |= $permission;

        return $this;
    }

    /**
     * Merges mask permission rules to input mask numeric value.
     * 
     * Modifies input mask numeric value to enable bits set in $maskAllowed
     * and disable bits set in $maskDisallowed.
     * 
     * @param int $inputMask mask numeric value
     * 
     * @return int computed mask value
     * 
     * @see Acl::getComputedMask()
     */
    public function mergeRules($inputMask)
    {
        $inputMask |= $this->maskAllowed;
        $inputMask &= ~$this->maskDisallowed;

        return $inputMask;
    }
}
