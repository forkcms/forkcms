<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package			template
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @author			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			0.1.1
 */


/** Spoon class */
require_once 'spoon/spoon.php';

/** SpoonTemplateException class */
require_once 'spoon/template/exception.php';

/** SpoonTemplateModifiers class */
require_once 'spoon/template/modifiers.php';

/** SpoonTemplateCompiler class */
require_once 'spoon/template/compiler.php';

/** SpoonFile class */
require_once 'spoon/filesystem/file.php';

/** SpoonDate class */
require_once 'spoon/date/date.php';


/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package			template
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @author			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			0.1.1
 */
class SpoonTemplate
{
	/**
	 * Cache names
	 *
	 * @var	array
	 */
	private $cache = array();


	/**
	 * Cache directory location
	 *
	 * @var	string
	 */
	private $cacheDirectory = '.';


	/**
	 * Compile directory location
	 *
	 * @var	string
	 */
	private $compileDirectory = '.';


	/**
	 * Always recompile
	 *
	 * @var	bool
	 */
	private $forceCompile = false;


	/**
	 * Strict option
	 *
	 * @var	bool
	 */
	private $strict = SPOON_STRICT;


	/**
	 * Stack of variables & their replace values
	 *
	 * @var	array
	 */
	private $variables = array();


	/**
	 * Assign values to variables
	 *
	 * @return	void
	 * @param	mixed $name
	 * @param	mixed $value
	 */
	public function assign($name, $value)
	{
		$this->variables[(string) $name] = $value;
	}


	/**
	 * Assign an entire array with keys & values
	 *
	 * @return	void
	 * @param	array $values
	 * @param	string[optional] $prefix
	 * @param	string[optional] $suffix
	 */
	public function assignArray(array $values, $prefix = null, $suffix = null)
	{
		foreach($values as $key => $value)
		{
			$this->variables[(string) $prefix . $key . (string) $suffix] = $value;
		}
	}


	/**
	 * Cache a certain block
	 *
	 * @return	void
	 * @param	string $name
	 * @param	int[optional] $lifetime
	 */
	public function cache($name, $lifetime = 60)
	{
		// redefine lifetime
		$lifetime = (SpoonFilter::isBetween(60, 30758400, $lifetime)) ? (int) $lifetime : 60;

		// set
		$this->cache[(string) $name] = $lifetime;
	}


	/**
	 * Clear the entire cache or a certain item
	 *
	 * @return	void
	 * @param	string[optional] $name
	 */
	public function clearCache($name = null)
	{
		// specific cache
		if($name !== null) SpoonFile::delete($this->cacheDirectory .'/'. (string) $name .'_cache.tpl');

		// all cache files
		else
		{
			// list of *_cache.tpl files from cacheDirectory
			$aFiles = SpoonFile::getList($this->cacheDirectory, '|.*\_cache\.tpl|');

			// delete
			foreach($aFiles as $file) SpoonFile::delete($this->cacheDirectory .'/'. $file);
		}
	}


	/**
	 * Compile a given template
	 *
	 * @return	void
	 * @param 	string $template
	 * @param	array[optional] $scope
	 */
	public function compile($template, array $scope = null)
	{
		// create object
		$compiler = new SpoonTemplateCompiler($template, $this->variables, $scope);

		// set some options
		$compiler->setCacheDirectory($this->cacheDirectory);
		$compiler->setCompileDirectory($this->compileDirectory);
		$compiler->setForceCompile($this->foreCompile);
		$compiler->setStrict($this->strict);

		// compile & save
		$compiler->parseToFile();
	}


	/**
	 * Deassign a variable
	 *
	 * @return	void
	 * @param	string $name
	 */
	public function deAssign($name)
	{
		if(isset($this->variables[(string) $name])) unset($this->variables[(string) $name]);
	}


	/**
	 * Display the output
	 *
	 * @return	void
	 * @param	string $template
	 */
	public function display($template)
	{
		// compiled name
		$compileName = $this->getCompileName((string) $template);

		// compiled if needed
		if(!SpoonFile::exists($this->compileDirectory .'/'. $compileName, $this->strict) || $this->foreCompile)
		{
			// create compiler
			$compiler = new SpoonTemplateCompiler((string) $template, $this->variables);

			// set some options
			$compiler->setCacheDirectory($this->cacheDirectory);
			$compiler->setCompileDirectory($this->compileDirectory);
			$compiler->setForceCompile($this->foreCompile);
			$compiler->setStrict($this->strict);

			// compile & save
			$compiler->parseToFile();
		}

		// load template
		require $this->compileDirectory .'/'. $compileName;
	}


	/**
	 * Get the cache directory path
	 *
	 * @return	string
	 */
	public function getCacheDirectory()
	{
		return $this->cacheDirectory;
	}


	/**
	 * get the compile directory path
	 *
	 * @return	string
	 */
	public function getCompileDirectory()
	{
		return $this->compileDirectory;
	}


	/**
	 * Retrieve the compiled name for this template
	 *
	 * @return	string
	 * @param	string $template
	 */
	private function getCompileName($template)
	{
		return md5(realpath($template)) .'_'. basename($template) .'.php';
	}


	/**
	 * Get the force compiling directive
	 *
	 * @return	bool
	 */
	public function getForceCompile()
	{
		return $this->foreCompile;
	}


	/**
	 * Get the template language
	 *
	 * @return	string
	 */
	public function getLanguage()
	{
		return $this->language;
	}


	/**
	 * Fetch the strict option
	 *
	 * @return	bool
	 */
	public function getStrict()
	{
		return $this->strict;
	}


	/**
	 * Is the cache for this item still valid
	 *
	 * @return	bool
	 * @param	string $name
	 */
	// @todo exceptions goe doen
	public function isCached($name)
	{
		// doesnt exist
		if(!isset($this->cache[(string) $name])) throw new SpoonTemplateException('No cache with the name '. (string) $name .') is cached of azoiets.');

		// last modification date
		$time = @filemtime($this->cacheDirectory .'/'. (string) $name .'_cache.tpl');

		// doesn't exist
		if($time === false) return false;

		// not valid
		if((time() - (int) $time) > $this->cache[(string) $name]) return false;

		// still valid
		return true;
	}


	/**
	 * Map a modifier to a given function/method
	 *
	 * @return	void
	 * @param	string $name
	 * @param	mixed $function
	 */
	public function mapModifier($name, $function)
	{
		SpoonTemplateModifiers::mapModifier($name, $function);
	}


	/**
	 * Set the cache directory
	 *
	 * @return	void
	 * @param	string $path
	 */
	public function setCacheDirectory($path)
	{
		$this->cacheDirectory = (string) $path;
	}


	/**
	 * Set the compile directory
	 *
	 * @return	void
	 * @param	string $path
	 */
	public function setCompileDirectory($path)
	{
		$this->compileDirectory = (string) $path;
	}


	/**
	 * If enabled, recompiles a template even if it has already been compiled
	 *
	 * @return	void
	 * @param	bool[optional] $on
	 */
	public function setForceCompile($on = true)
	{
		$this->foreCompile = (bool) $on;
	}


	/**
	 * Set the strict option
	 *
	 * @return	void
	 * @param	bool[optional] $on
	 */
	public function setStrict($on = true)
	{
		$this->strict = (bool) $on;
	}
}

?>