<?php

/**
 * Step 1 of the Fork installer
 *
 * @package		install
 * @subpackage	installer
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.0
 */
class InstallerStep1 extends InstallerStep
{
	/**
	 * Execute this step
	 *
	 * @return	void
	 */
	public function execute()
	{
		// init vars
		$possiblePaths = array();
		$variables = array();

		// head
		$variables['head'] = file_get_contents('layout/templates/head.tpl');
		$variables['foot'] = file_get_contents('layout/templates/foot.tpl');

		// get the possible library paths
		self::guessLibraryPath(dirname(dirname(dirname(realpath($_SERVER['SCRIPT_FILENAME'])))), $possiblePaths);

		// was the form submitted?
		if(isset($_GET['spoon_location']) && in_array($_GET['spoon_location'], $possiblePaths))
		{
			// store in session
			$_SESSION['path_library'] = $_GET['spoon_location'];

			// redirect to step 2
			header('Location: index.php?step=2');
			exit;
		}

		// count
		$count = count($possiblePaths);

		// just one found? add it into the session
		if($count == 1)
		{
			$_SESSION['path_library'] = $possiblePaths[0];

			// redirect to step 2
			header('Location: index.php?step=2');
			exit;
		}

		// nothing found
		elseif($count > 0)
		{
			$variables['content'] = '<h3>Location of Spoon</h3>
									<div class="horizontal">
										<p>We detected multiple folders containing Spoon. Below you can select the correct folder.</p>
										<p>
											<label for="spoonLocation">Paths<abbr title="Required field">*</abbr></label>
											<select id="spoonLocation" name="spoon_location" class="input-select" style="width: 350px;">';

			// loop locations
			foreach($possiblePaths as $path)
			{
				$variables['content'] .= '<option value="' . $path . '">' . $path . '</option>';
			}


			$variables['content'] .= 	'</select>
										</p>
										<p class="buttonHolder">
											<input id="installerButton" class="button inputButton mainButton" type="submit" name="installer" value="Next" />
										</p>';
		}

		// nothing found
		else
		{
			$variables['content'] = '<div class="formMessage errorMessage">
										<p>We couldn\'t locate Spoon Library. Make sure you uploaded the <code>library</code>-folder.</p>
									</div>';
		}

		// template contents
		$tpl = file_get_contents('layout/templates/1.tpl');

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
	 * Try to guess the location of the library based on spoon library
	 *
	 * @return	void
	 * @param	string $directory			The directory to start from.
	 * @param	array[optional] $library	An array to hold the paths that were guesed.
	 */
	private static function guessLibraryPath($directory, array &$library = null)
	{
		// init var
		$location = '';

		// loop directories
		foreach((array) glob($directory . '/*') as $filename)
		{
			// not a directory and equals 'spoon.php'
			if(!is_dir($filename) && substr($filename, -9) == 'spoon.php')
			{
				// get real path
				$path = realpath(str_replace('spoon.php', '..', $filename));

				// only unique values should be added
				if(is_array($library))
				{
					// add
					if(!in_array($path, $library)) $library[] = $path;
				}

				// not an array
				else $library = array($path);
			}

			// directory
			elseif(is_dir($filename) && substr($filename, -4) != '.svn')
			{
				// new location
				self::guessLibraryPath($filename, $library);
			}
		}
	}
}

?>