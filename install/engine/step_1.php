<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Step 1 of the Fork installer
 *
 * @author Davy Hellemans <davy@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Matthias Mullie <forkcms@mullie.eu>
 */
class InstallerStep1 extends InstallerStep
{
	/**
	 * Execute this step
	 */
	public function execute()
	{
		// init vars
		$possiblePaths = array();
		$variables = array();

		// head
		$variables['head'] = file_get_contents(dirname(__FILE__) . '/../layout/templates/head.tpl');
		$variables['foot'] = file_get_contents(dirname(__FILE__) . '/../layout/templates/foot.tpl');
		$hasError = false;

		// was the form submitted?
		if(isset($_GET['spoon_location']))
		{
			// empty path
			if($_GET['spoon_location'] == '') $hasError = true;

			else
			{
				// cleanup the path
				$temporaryPath = realpath(rtrim($_GET['spoon_location'], '/'));

				if(file_exists($temporaryPath . '/spoon/spoon.php'))
				{
					// store in session
					$_SESSION['path_library'] = $temporaryPath;

					// redirect to step 2
					header('Location: index.php?step=2');
					exit;
				}

				// add error
				else $hasError = true;
			}
		}

		// this should be the path
		$path = realpath(dirname(__FILE__) . '/../../library');
		$spoonFolder = realpath($path . '/../vendor/spoon/library');

		// just one found? add it into the session
		if(file_exists($spoonFolder . '/spoon/spoon.php'))
		{
			$_SESSION['path_library'] = $path;

			// redirect to step 2
			header('Location: index.php?step=2');
			exit;
		}

		// template contents
		$tpl = file_get_contents('layout/templates/step_1.tpl');

		// build the search & replace array
		$search = array_keys($variables);
		$replace = array_values($variables);

		// loop search values
		foreach($search as $key => $value) $search[$key] = '{$' . $value . '}';

		// build output
		$output = str_replace($search, $replace, $tpl);

		// show output
		echo $output;

		// stop the script
		exit;
	}

	/**
	 * This step is always allowed.
	 *
	 * @return bool
	 */
	public static function isAllowed()
	{
		return true;
	}
}
