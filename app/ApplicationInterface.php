<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * An interface to define the applications. This will be used to so we can
 * program to an interface, not an implementation.
 *
 * It'll define generic methods which every application should contain. These
 * methods can be used by the AppKernel for example.
 *
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 */
interface ApplicationInterface
{
	/**
	 * Return a proper Response object so we can take care of outputting data
	 * on a higher level.
	 *
	 * @return Symfony\Component\HttpFoundation\Response
	 */
	public function getResponse();
}
