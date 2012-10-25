<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Request;

// hardcode this for now, this should be autoloaded
require_once __DIR__ . '/Kernel.php';
require_once __DIR__ . '/routing.php';

/**
 * The AppKernel provides a proper way to handle a request and transform it into a response.
 *
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 * @author Dave Lens <dave.lens@wijs.be>
 */
class AppKernel extends Kernel
{
	/**
	 * @var ApplicationRouting
	 */
	private $router;

	public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
	{
		$this->router = new ApplicationRouting($request, $this);
		return $this->router->handleRequest();
	}
}

