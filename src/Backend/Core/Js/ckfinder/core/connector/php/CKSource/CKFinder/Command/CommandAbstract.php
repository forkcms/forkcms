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

use CKSource\CKFinder\CKFinder;
use CKSource\CKFinder\Exception\UnauthorizedException;
use Symfony\Component\HttpFoundation\Request;

/**
 * The base class for all Command classes.
 *
 * @copyright 2016 CKSource - Frederico Knabben
 */
abstract class CommandAbstract
{
    /**
     * The CKFinder instance.
     *
     * @var CKFinder $app
     */
    protected $app;

    /**
     * The request method - by default GET.
     *
     * @var string
     */
    protected $requestMethod = Request::METHOD_GET;

    /**
     * An array of permissions required by the command.
     *
     * @var array $requires
     */
    protected $requires = array();

    /**
     * Constructor.
     *
     * @param CKFinder $app
     */
    public function __construct(CKFinder $app)
    {
        $this->setContainer($app);
    }

    /**
     * Injects dependency injection container to the command scope.
     *
     * @param CKFinder $app
     */
    public function setContainer(CKFinder $app)
    {
        $this->app = $app;
    }

    /**
     * Checks permissions required by the command before it is executed.
     *
     * @throws \Exception if access is restricted.
     */
    public function checkPermissions()
    {
        if (!empty($this->requires)) {
            $workingFolder = $this->app->getWorkingFolder();

            $aclMask = $workingFolder->getAclMask();

            $requiredPermissionsMask = array_sum($this->requires);

            if (($aclMask & $requiredPermissionsMask) !== $requiredPermissionsMask) {
                throw new UnauthorizedException();
            }
        }
    }

    /**
     * Returns the name of the request method required by the command.
     *
     * @return string
     */
    public function getRequestMethod()
    {
        return $this->requestMethod;
    }

    /**
     * This method is not defined as abstract to allow for parameter injection.
     * @see CKSource\CKFinder\CommandResolver::getArguments()
     */
    // public abstract function execute();
}
