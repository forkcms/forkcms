<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Autoloader for Fork CMS.
 *
 * @author Dieter Vanden Eynde <dieter@dieterve.be>
 */
class Autoloader
{
	/**
	 * @param string $className
	 */
	public function load($className)
	{
		$unifiedClassName = strtolower((string) $className);
		$pathToLoad = '';

		// exceptions
		$exceptions = array();
		$exceptions['frontend'] = __DIR__ . '/frontend/core/engine/frontend.php';
		$exceptions['frontendtemplatecompiler'] = __DIR__ . '/frontend/core/engine/template_compiler.php';
		$exceptions['backend'] = __DIR__ . '/backend/core/engine/backend.php';
		$exceptions['backendajaxaction'] = __DIR__ . '/backend/core/engine/ajax_action.php';
		$exceptions['fl'] = __DIR__ . '/frontend/core/engine/language.php';
		$exceptions['bl'] = __DIR__ . '/backend/core/engine/language.php';
		$exceptions['api'] = __DIR__ . '/api/1.0/engine/api.php';

		// is it an exception?
		if(isset($exceptions[$unifiedClassName])) $pathToLoad = $exceptions[$unifiedClassName];

		// if it is a Spoon-class we can stop using this autoloader
		elseif(substr($unifiedClassName, 0, 5) == 'spoon') return;

		elseif(substr($unifiedClassName, 0, 12) == 'frontendbase') $pathToLoad = __DIR__ . '/frontend/core/engine/base.php';
		elseif(substr($unifiedClassName, 0, 13) == 'frontendblock') $pathToLoad = __DIR__ . '/frontend/core/engine/block.php';
		elseif(substr($unifiedClassName, 0, 8) == 'frontend') $pathToLoad = __DIR__ . '/frontend/core/engine/' . str_replace('frontend', '', $unifiedClassName) . '.php';
		elseif(substr($unifiedClassName, 0, 11) == 'backendbase') $pathToLoad = __DIR__ . '/backend/core/engine/base.php';
		elseif(substr($unifiedClassName, 0, 15) == 'backenddatagrid') $pathToLoad = __DIR__ . '/backend/core/engine/datagrid.php';
		elseif(substr($unifiedClassName, 0, 7) == 'backend') $pathToLoad = __DIR__ . '/backend/core/engine/' . str_replace('backend', '', $unifiedClassName) . '.php';
		elseif(substr($unifiedClassName, 0, 6) == 'common') $pathToLoad = __DIR__ . '/library/base/' . str_replace('common', '', $unifiedClassName) . '.php';

		// file check in core
		if($pathToLoad != '' && file_exists($pathToLoad)) require_once $pathToLoad;

		// check if module file exists
		else
		{
			// split in parts, if nothing is found we stop processing
			if(!preg_match_all('/[A-Z][a-z0-9]*/', $className, $parts)) return;

			// the real matches
			$parts = $parts[0];

			// root path based on the application we are trying to load
			$root = array_shift($parts);
			$root = __DIR__ . '/' . strtolower($root);

			foreach($parts as $i => $part)
			{
				// skip the first
				if($i == 0) continue;

				// action
				$action = strtolower(implode('_', $parts));

				// module
				$module = '';
				for($j = 0; $j < $i; $j++) $module .= strtolower($parts[$j]) . '_';

				// fix action & module
				$action = substr($action, strlen($module));
				$module = substr($module, 0, -1);

				// check the actions, engine & widgets directories
				foreach(array('actions', 'engine', 'widgets') as $dir)
				{
					// file to be loaded
					$pathToLoad = $root . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $action . '.php';

					// if it exists, load it!
					if($pathToLoad != '' && file_exists($pathToLoad))
					{
						require_once $pathToLoad;
						break;
					}
				}
			}
		}
	}
}

// register the autoloader
spl_autoload_register(array(new Autoloader(), 'load'));

// use vender generated autoloader
require_once 'vendor/autoload.php';

// Spoon is not autoloaded via Composer but uses its own oldskool autoloader
set_include_path(__DIR__ . '/vendor/spoon/library' . PATH_SEPARATOR . get_include_path());
require_once 'spoon/spoon.php';
