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
		$className = strtolower((string) $className);
		$pathToLoad = '';

		// exceptions
		// @todo - exceptions should be reduced to a minimum
		$exceptions = array();
		$exceptions['frontend'] = __DIR__ . '/frontend/core/engine/frontend.php';
		$exceptions['frontendbaseajaxaction'] = __DIR__ . '/frontend/core/engine/base.php';
		$exceptions['frontendbaseconfig'] = __DIR__ . '/frontend/core/engine/base.php';
		$exceptions['frontendbaseobject'] = __DIR__ . '/frontend/core/engine/base.php';
		$exceptions['frontendblockextra'] = __DIR__ . '/frontend/core/engine/block.php';
		$exceptions['frontendblockwidget'] = __DIR__ . '/frontend/core/engine/block.php';
		$exceptions['frontendtemplatecompiler'] = __DIR__ . '/frontend/core/engine/template_compiler.php';
		$exceptions['backend'] = __DIR__ . '/backend/core/engine/backend.php';
		$exceptions['backendbaseobject'] = __DIR__ . '/backend/core/engine/base.php';
		$exceptions['backendajaxaction'] = __DIR__ . '/backend/core/engine/ajax_action.php';
		$exceptions['backendbaseajaxaction'] = __DIR__ . '/backend/core/engine/base.php';
		$exceptions['backenddatagriddb'] = __DIR__ . '/backend/core/engine/datagrid.php';
		$exceptions['backenddatagridarray'] = __DIR__ . '/backend/core/engine/datagrid.php';
		$exceptions['backenddatagridfunctions'] = __DIR__ . '/backend/core/engine/datagrid.php';
		$exceptions['backendbaseconfig'] = __DIR__ . '/backend/core/engine/base.php';
		$exceptions['backendbasecronjob'] = __DIR__ . '/backend/core/engine/base.php';
		$exceptions['fl'] = __DIR__ . '/frontend/core/engine/language.php';
		$exceptions['bl'] = __DIR__ . '/backend/core/language.php';
		$exceptions['api'] = __DIR__ . '/api/1.0/engine/api.php';

		// is it an exception
		if(isset($exceptions[$className])) $pathToLoad = $exceptions[$className];

		// frontend
		elseif(substr($className, 0, 8) == 'frontend')
		{
			$filename = str_replace('frontend', '', $className) . '.php';
			$pathToLoad = __DIR__ . '/frontend/core/engine/' . $filename;
		}

		// backend
		elseif(substr($className, 0, 7) == 'backend')
		{
			$filename = str_replace('backend', '', $className) . '.php';
			$pathToLoad = __DIR__ . '/backend/core/engine/' . $filename;
		}

		// common
		elseif(substr($className, 0, 6) == 'common')
		{
			$filename = str_replace('common', '', $className) . '.php';
			$pathToLoad = __DIR__ . '/library/base/' . $filename;
		}

		// file check in core
		if($pathToLoad != '' && file_exists($pathToLoad)) require_once $pathToLoad;

		// check if module file exists
		else
		{
			// we'll need the original class name again, with the uppercases
			$className = func_get_arg(0);

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
require 'vendor/autoload.php';

// @todo we also need the autoloader of spoon before we start our application (so we can define services)
set_include_path(__DIR__ . '/library' . PATH_SEPARATOR . get_include_path());
require_once 'spoon/spoon.php';

require_once __DIR__ . '/app/AppKernel.php';
require_once __DIR__ . '/app/bootstrap.php';
