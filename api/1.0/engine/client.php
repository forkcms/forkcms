<?php

class APIClient extends API
{
	const SECRET_KEY = 'HuizenTeKoop';

	/**
	 * @var SpoonTemplate
	 */
	private $tpl;

	public function __construct()
	{
		// the URL to call this API
		$this->url = SITE_URL . str_replace('client/', 'index.php', $_SERVER['REQUEST_URI']);

		require_once 'spoon/form/form.php';
		require_once 'spoon/form/button.php';
		require_once 'spoon/template/template.php';

		$this->tpl = new SpoonTemplate();
		$this->tpl->setForceCompile(true);
		$this->tpl->setCompileDirectory(BACKEND_CACHE_PATH . '/compiled_templates');

		$this->loadConfiguration();
		$this->loadModules();

		$this->tpl->display('layout/templates/index.tpl');
	}

	private function loadConfiguration()
	{
		$this->tpl->assign('url', $this->url);
		$this->tpl->assign('secretKey', self::SECRET_KEY);
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
		if(array_key_exists(0, $matches) && empty($matches[0])) continue;
		$phpdoc = array();

		// we have to build up a custom stack of parameters
		foreach($matches[0] as $i => $row)
		{
			$name = $matches[2][$i];

			if($name === 'language') continue;

			$parameters[] = array(
				'name' => $name,
				'label' => $name  .'-'. rand(1, 99999),
				'optional' => (substr_count($matches[2][$i], '[optional]') > 0),
				'description' => $matches[3][$i]
			);
		}

		return $parameters;
	}

	/**
	 * Loops all backend modules, and builds a list of those that have an
	 * api.php file in their engine.
	 */
	private function loadModules()
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
			$relevantModules[] = array(
				'name' => $module,
				'methods' => $methods
			);
		}

		$this->tpl->assign('modules', $relevantModules);
	}
}
