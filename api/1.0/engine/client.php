<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Response;

/**
 * Client for the Fork CMS API.
 *
 * @author Dave Lens <dave.lens@wijs.be>
 */
class APIClient extends API
{
	/**
	 * @var SpoonTemplate
	 */
	private $tpl;

	/**
	 * @var array
	 */
	private $modules;

	/**
	 * This method exists because the service container needs to be set before
	 * the rest of API functionality gets loaded.
	 */
	public function initialize()
	{
		$this->tpl = new SpoonTemplate();
		$this->tpl->setForceCompile(true);
		$this->tpl->setCompileDirectory(BACKEND_CACHE_PATH . '/compiled_templates');

		$this->loadModules();
		$this->parse();
		$this->display();
	}

	/**
	 * @return Symfony\Component\HttpFoundation\Response
	 */
	public function display()
	{
		$content = $this->tpl->getContent(__DIR__ . '/../client/layout/templates/index.tpl');

		return new Response(
			$content, 200, SpoonHttp::getHeadersList()
		);
	}

	/**
	 * Loops all backend modules, and builds a list of those that have an
	 * api.php file in their engine.
	 */
	protected function loadModules()
	{
		$modules = BackendModel::getModules();

		foreach($modules as &$module)
		{
			/*
			 * check if the api.php file exists for this module, and load it so our methods are
			 * accessible by the Reflection API.
			 */
			$moduleAPIFile = BACKEND_MODULES_PATH . '/' . $module . '/engine/api.php';
			if(!file_exists($moduleAPIFile)) continue;
			require_once $moduleAPIFile;

			// class names of the API file are always based on the name o/t module
			$className = 'Backend' . SpoonFilter::toCamelCase($module) . 'API';
			$methods = get_class_methods($className);

			// we will need the parameters + PHPDoc to generate our textfields
			foreach($methods as $key => $method)
			{
				$methods[$key] = array(
					'name' => $method,
					'parameters' => $this->loadParameters($className, $method)
				);
			}

			// properly format so an iteration can do the work for us
			$this->modules[] = array(
				'name' => $module,
				'methods' => $methods
			);
		}
	}

	/**
	 * This method is used to return an iteration-friendly list of parameters for a given method.
	 *
	 * @param string $className
	 * @param string $method
	 * @return array
	 */
	protected function loadParameters($className, $method)
	{
		// dig for data on the chosen method, in the chosen class
		$parameters = array();
		$reflectionMethod = new ReflectionMethod($className, $method);
		$PHPDoc = $reflectionMethod->getDocComment();

		/*
		 * This regex filters out all parameters, along with their PHPDoc. We use this instead
		 * of $reflectionMethod->getParameters(), since that returns ReflectionParameter objects
		 * that, rather shamefully, do not contain PHPDoc.
		 */
		preg_match_all('/@param[\s\t]+(.*)[\s\t]+\$(.*)[\s\t]+(.*)$/Um', $PHPDoc, $matches);
		if(array_key_exists(0, $matches) && empty($matches[0])) return;
		$phpdoc = array();

		// we have to build up a custom stack of parameters
		foreach($matches[0] as $i => $row)
		{
			$name = $matches[2][$i];

			if($name === 'language') continue;

			$parameters[] = array(
				'name' => $name,
				'label' => $name . '-' . rand(1, 99999),
				'optional' => (substr_count($matches[2][$i], '[optional]') > 0),
				'description' => $matches[3][$i]
			);
		}

		return $parameters;
	}

	/**
	 * Parse the data into the template
	 */
	protected function parse()
	{
		// the URL to call the API
		$url = SITE_URL . str_replace('client/', 'index.php', $_SERVER['REQUEST_URI']);

		$this->tpl->assign('url', $url);
		$this->tpl->assign('modules', $this->modules);
	}
}
