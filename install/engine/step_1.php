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
		$variables['head'] = file_get_contents('layout/templates/head.tpl');
		$variables['foot'] = file_get_contents('layout/templates/foot.tpl');
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

		// just one found? add it into the session
		if(file_exists($path . '/spoon/spoon.php'))
		{
			$_SESSION['path_library'] = $path;

			// redirect to step 2
			header('Location: index.php?step=2');
			exit;
		}

		// nothing found
		else
		{
			$variables['content'] = '<h3>Location of Spoon</h3>
									 <div>
										<p>We couldn\'t locate Spoon Library, give us a hand and enter the path to the library-folder below.</p>
										<p>
											<label for="spoonLocation">Path<abbr title="Required field">*</abbr></label>
											<input type="text" name="spoon_location" id="spoonLocation" class="inputText" style="width: 350px;">';

			if($hasError) $variables['content'] .= '<span style="padding-left: 0;" class="formError">The path you entered doesn\'t contain Spoon Library.</span>';

			$variables['content'] .= '	</p>
										<p class="buttonHolder">
											<input id="installerButton" class="button inputButton mainButton" type="submit" name="installer" value="Next" />
										</p>';
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
