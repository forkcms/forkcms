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
		$exceptions = array();
		$exceptions['frontend'] = PATH_WWW . '/frontend/core/engine/frontend.php';
		$exceptions['frontendtemplatecompiler'] = PATH_WWW . '/frontend/core/engine/template_compiler.php';
		$exceptions['backend'] = PATH_WWW . '/backend/core/engine/backend.php';
		$exceptions['backendajaxaction'] = PATH_WWW . '/backend/core/engine/ajax_action.php';
		$exceptions['fl'] = PATH_WWW . '/frontend/core/engine/language.php';
		$exceptions['bl'] = PATH_WWW . '/backend/core/language.php';
		$exceptions['api'] = PATH_WWW . '/api/1.0/engine/api.php';

		// is it an exception?
		if(isset($exceptions[$className])) $pathToLoad = $exceptions[$className];

		// if it is a Spoon-class we can stop using this autoloader
		elseif(substr($className, 0, 5) == 'spoon') return;

		elseif(substr($className, 0, 12) == 'frontendbase') $pathToLoad = PATH_WWW . '/frontend/core/engine/base.php';
		elseif(substr($className, 0, 13) == 'frontendblock') $pathToLoad = PATH_WWW . '/frontend/core/engine/block.php';
		elseif(substr($className, 0, 8) == 'frontend') $pathToLoad = PATH_WWW . '/frontend/core/engine/' . str_replace('frontend', '', $className) . '.php';
		elseif(substr($className, 0, 11) == 'backendbase') $pathToLoad = PATH_WWW . '/backend/core/engine/base.php';
		elseif(substr($className, 0, 15) == 'backenddatagrid') $pathToLoad = PATH_WWW . '/backend/core/engine/datagrid.php';
		elseif(substr($className, 0, 7) == 'backend') $pathToLoad = PATH_WWW . '/backend/core/engine/' . str_replace('backend', '', $className) . '.php';
		elseif(substr($className, 0, 6) == 'common') $pathToLoad = PATH_LIBRARY . '/base/' . str_replace('common', '', $className) . '.php';

		// file check in core
		if($pathToLoad != '' && SpoonFile::exists($pathToLoad)) require_once $pathToLoad;

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
			$root = PATH_WWW . '/' . strtolower($root);

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
					if($pathToLoad != '' && SpoonFile::exists($pathToLoad))
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