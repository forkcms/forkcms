<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

// load substantial steps
require_once 'step.php';
require_once 'step_1.php';
require_once 'step_2.php';
require_once 'step_3.php';
require_once 'step_4.php';
require_once 'step_5.php';
require_once 'step_6.php';
require_once 'step_7.php';

/**
 * Fork Installer
 *
 * @author Davy Hellemans <davy@netlash.com>
 * @author Matthias Mullie <forkcms@mullie.eu>
 */
class Installer extends KernelLoader implements ApplicationInterface
{
	/**
	 * The current step number
	 *
	 * @var	int
	 */
	private $step;

	/**
	 * Only checks if this Fork is already installed
	 *
	 * @param Kernel $kernel
	 */
	public function __construct($kernel)
	{
		if(file_exists(__DIR__ .  '/../cache/installed.txt'))
		{
			exit('This Fork has already been installed. To reinstall, delete installed.txt from the install/cache directory. To log in, <a href="/private">click here</a>.');
		}

		parent::__construct($kernel);
	}

	/**
	 * Initializes the installation process
	 */
	public function initialize()
	{
		if(!defined('SPOON_DEBUG'))
		{
			define('SPOON_DEBUG', false);
			define('SPOON_CHARSET', 'utf-8');
		}

		$this->setStep();
	}

	/**
	 * Executes the proper step
	 */
	public function display()
	{
		// step class name
		$class = 'InstallerStep' . $this->step;

		// create & execute instance
		$instance = new $class($this->step);
		$instance->setKernel($this->getKernel());
		$instance->initialize();
		$instance->execute();
		return $instance->display();
	}

	/**
	 * Sets the step based on a few checks
	 */
	private function setStep()
	{
		// fetch step
		$step = (isset($_GET['step'])) ? (int) $_GET['step'] : 1;

		// installer step class exists
		if(class_exists('InstallerStep' . $step))
		{
			// isAllowed exists
			if(is_callable(array('InstallerStep' . $step, 'isAllowed')))
			{
				// step is actually allowed
				if(call_user_func(array('InstallerStep' . $step, 'isAllowed')))
				{
					// step has been validated
					$this->step = $step;

					// step out
					return;
				}
			}
		}

		// step not ok? redirect to previous step!
		header('Location: index.php?step=' . ($step - 1));
		exit;
	}
}
