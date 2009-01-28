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
	 * List of form objects
	 *
	 * @var	array
	 */
	private $forms = array();


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
	private $variables = array('CRLF' => "\n", 'TAB' => "\t");


	/**
	 * Adds a form to this template
	 *
	 * @return	void
	 * @param	SpoonForm $form
	 */
	public function addForm(SpoonForm $form)
	{
		$this->forms[$form->getName()] = $form;
	}


	/**
	 * Assign values to variables
	 *
	 * @return	void
	 * @param	mixed $variable
	 * @param	mixed[optional] $value
	 */
	public function assign($variable, $value = null)
	{
		// regular function use
		if($value !== null && $variable != '') $this->variables[(string) $variable] = $value;

		// only 1 argument
		else
		{
			// not an array
			if(!is_array($variable)) throw new SpoonTemplateException('If you provide one argument it needs to be an array');

			// loop array
			foreach($variable as $key => $value)
			{
				// key is NOT empty
				if($key !== '') $this->variables[(string) $key] = $value;
			}
		}
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
		$lifetime = (SpoonFilter::isBetween(10, 30758400, $lifetime)) ? (int) $lifetime : 60;

		// set lifetime
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
	 * Clear the entire compiled directory or a specific template
	 *
	 * @return	void
	 * @param	string[optional] $template
	 */
	public function clearCompiled($template = null)
	{
		// specific template
		if($template !== null) SpoonFile::delete($this->compileDirectory .'/'. $this->getCompileName($template));

		// all compiled templates
		else
		{
			// list of *.tpl.php files from compileDirectory
			$aFiles = SpoonFile::getList($this->compileDirectory, '|.*\.tpl\.php|');

			// delete
			foreach($aFiles as $file) SpoonFile::delete($this->compileDirectory .'/'. $file);
		}
	}


	/**
	 * Compile a given template
	 *
	 * @return	void
	 * @param	string $path
	 * @param 	string $template
	 */
	public function compile($path, $template)
	{
		// redefine template
		if(substr($template, 0, 1) != '/') $template = $path .'/'. $template;

		// create object
		$compiler = new SpoonTemplateCompiler($template, $this->variables);

		// set some options
		$compiler->setCacheDirectory($this->cacheDirectory);
		$compiler->setCompileDirectory($this->compileDirectory);
		$compiler->setForceCompile($this->forceCompile);
		$compiler->setStrict($this->strict);
		$compiler->setForms($this->forms);

		// compile & save
		$compiler->parseToFile();
	}


	/**
	 * Returns the correct element from the list, based on the counter
	 *
	 * @return	string
	 * @param	int $counter
	 * @param	array $elements
	 */
	public function cycle($counter, array $elements)
	{
		// update counter
		$counter += 1;

		// number of elements
		$numElements = count($elements);

		// calculate modulus
		$modulus = $counter % $numElements;

		// leftovers?
		if($modulus == 0) return $elements[$numElements - 1];
		else return $elements[$modulus - 1];
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
		if(!SpoonFile::exists($this->compileDirectory .'/'. $compileName, $this->strict) || $this->forceCompile)
		{
			// create compiler
			$compiler = new SpoonTemplateCompiler((string) $template, $this->variables);

			// set some options
			$compiler->setCacheDirectory($this->cacheDirectory);
			$compiler->setCompileDirectory($this->compileDirectory);
			$compiler->setForceCompile($this->forceCompile);
			$compiler->setStrict($this->strict);
			$compiler->setForms($this->forms);

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
	 * @param	string[optional] $path
	 */
	private function getCompileName($template, $path = null)
	{
		// redefine template
		if(substr($template, 0, 1) != '/' && $path !== null) $template = $path .'/'. $template;

		// return the correct full path
		return md5(realpath($template)) .'_'. basename($template) .'.php';
	}


	/**
	 * Get the force compiling directive
	 *
	 * @return	bool
	 */
	public function getForceCompile()
	{
		return $this->forceCompile;
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
	public function isCached($name)
	{
		// doesnt exist
		if(!isset($this->cache[(string) $name])) throw new SpoonTemplateException('No cache with the name '. (string) $name .' is known.');

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
		$this->forceCompile = (bool) $on;
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