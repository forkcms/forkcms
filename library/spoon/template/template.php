<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.com
 *
 * @package		spoon
 * @subpackage	template
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @author		Matthias Mullie <matthias@spoon-library.com>
 * @since		1.0.0
 */


/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.com
 *
 * @package		spoon
 * @subpackage	template
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @author		Tijs Verkoyen <tijs@spoon-library.com>
 * @since		0.1.1
 */
class SpoonTemplate
{
	/**
	 * Cache names
	 *
	 * @var	array
	 */
	protected $cache = array();


	/**
	 * Cache directory location
	 *
	 * @var	string
	 */
	protected $cacheDirectory = '.';


	/**
	 * Compile directory location
	 *
	 * @var	string
	 */
	protected $compileDirectory = '.';


	/**
	 * Always recompile
	 *
	 * @var	bool
	 */
	protected $forceCompile = false;


	/**
	 * List of form objects
	 *
	 * @var	array
	 */
	protected $forms = array();


	/**
	 * Stack of iterations (used in compiled template)
	 *
	 * @var	array
	 */
	protected $iterations = array();


	/**
	 * Stack of variables & their replace values
	 *
	 * @var	array
	 */
	protected $variables = array();


	/**
 	 * Creates a template instance and assigns a few default variables.
	 */
	public function __construct()
	{
		// carriage line feed
		$this->assign('CRLF', "\n");

		// tab
		$this->assign('TAB', "\t");

		// current date/time
		$this->assign('now', time());
	}


	/**
	 * Adds a form to this template.
	 *
	 * @param	SpoonForm $form		The form-instance to add.
	 */
	public function addForm(SpoonForm $form)
	{
		$this->forms[$form->getName()] = $form;
	}


	/**
	 * Assign values to variables.
	 *
	 * @param	mixed $variable			The key to search for or an array with keys & values.
	 * @param	mixed[optional] $value	The value to replace the key with. If the first element is an array, this argument is not required.
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
	 * Assign an entire array with keys & values.
	 *
	 * @param	array $values				This array with keys and values will be used to search and replace in the template file.
	 * @param	string[optional] $prefix	An optional prefix eg. 'lbl' that can be used.
	 * @param	string[optional] $suffix	An optional suffix eg. 'msg' that can be used.
	 */
	public function assignArray(array $values, $prefix = null, $suffix = null)
	{
		foreach($values as $key => $value)
		{
			$this->variables[(string) $prefix . $key . (string) $suffix] = $value;
		}
	}


	/**
	 * Cache a certain block.
	 *
	 * @param	string $name				The name of the block that you want to cache.
	 * @param	int[optional] $lifetime		The lifetime in seconds.
	 */
	public function cache($name, $lifetime = 60)
	{
		// redefine lifetime
		$lifetime = (SpoonFilter::isBetween(10, 30758400, $lifetime)) ? (int) $lifetime : 60;

		// set lifetime
		$this->cache[(string) $name] = $lifetime;
	}


	/**
	 * Clear the entire cache or a specific item.
	 *
	 * @param	string[optional] $name	The name of the cache block that you want to clear from the directory with cached files.
	 */
	public function clearCache($name = null)
	{
		// specific cache
		if($name !== null) SpoonFile::delete($this->cacheDirectory . '/' . (string) $name . '_cache.tpl');

		// all cache files
		else
		{
			// list of *_cache.tpl files from cacheDirectory
			$files = SpoonFile::getList($this->cacheDirectory, '|.*\_cache\.tpl|');

			// delete
			foreach($files as $file) SpoonFile::delete($this->cacheDirectory . '/' . $file);
		}
	}


	/**
	 * Clear the entire compiled directory or a specific template.
	 *
	 * @param	string[optional] $template	The filename of a specific template to mark for recompiling.
	 */
	public function clearCompiled($template = null)
	{
		// specific template
		if($template !== null) SpoonFile::delete($this->compileDirectory . '/' . $this->getCompileName($template));

		// all compiled templates
		else
		{
			// list of *.tpl.php files from compileDirectory
			$files = SpoonFile::getList($this->compileDirectory, '|.*\.tpl\.php|');

			// delete
			foreach($files as $file) SpoonFile::delete($this->compileDirectory . '/' . $file);
		}
	}


	/**
	 * Compile a given template.
	 *
	 * @param	string $path		The path to the template, excluding the template filename.
	 * @param 	string $template	The filename of the template within the path.
	 */
	public function compile($path, $template)
	{
		// redefine template
		if(realpath($template) === false) $template = $path . '/' . $template;

		// source file does not exist
		if(!SpoonFile::exists($template)) return false;

		// create object
		$compiler = new SpoonTemplateCompiler($template, $this->variables);

		// set some options
		$compiler->setCacheDirectory($this->cacheDirectory);
		$compiler->setCompileDirectory($this->compileDirectory);
		$compiler->setForceCompile($this->forceCompile);
		$compiler->setForms($this->forms);

		// compile & save
		$compiler->parseToFile();

		// status
		return true;
	}


	/**
	 * Returns the correct element from the list, based on the counter.
	 *
	 * @return	string				The item based on the counter from $elements.
	 * @param	int $counter		The index of the item to retrieve from the list $elements.
	 * @param	array $elements		The list of elements to cycle through.
	 */
	public function cycle($counter, array $elements)
	{
		// number of elements
		$numElements = count($elements);

		// calculate modulus
		$modulus = $counter % $numElements;

		// leftovers?
		if($modulus == 0) return $elements[$numElements - 1];
		return $elements[$modulus - 1];
	}


	/**
	 * Deassign a variable.
	 *
	 * @param	string $name	The name of the key that you want to remove from the list of already assigned variables.
	 */
	public function deAssign($name)
	{
		if(isset($this->variables[(string) $name])) unset($this->variables[(string) $name]);
	}


	/**
	 * Display the output.
	 *
	 * @param	string $template	The filename of the template that you want to display.
	 */
	public function display($template)
	{
		// redefine
		$template = (string) $template;

		// validate name
		if(trim($template) == '' || !SpoonFile::exists($template)) throw new SpoonTemplateException('Please provide an existing template.');

		// compiled name
		$compileName = $this->getCompileName((string) $template);

		// compiled if needed
		if($this->forceCompile || !SpoonFile::exists($this->compileDirectory . '/' . $compileName))
		{
			// create compiler
			$compiler = new SpoonTemplateCompiler((string) $template, $this->variables);

			// set some options
			$compiler->setCacheDirectory($this->cacheDirectory);
			$compiler->setCompileDirectory($this->compileDirectory);
			$compiler->setForceCompile($this->forceCompile);
			$compiler->setForms($this->forms);

			// compile & save
			$compiler->parseToFile();
		}

		// load template
		require $this->compileDirectory . '/' . $compileName;
	}


	/**
	 * Retrieves the already assigned value.
	 *
	 * @return	mixed				Returns an array, string, int or null
	 * @param	string $variable	The name of the variable that you want to retrieve the already assigned value from.
	 */
	public function getAssignedValue($variable)
	{
		if(isset($this->variables[(string) $variable])) return $this->variables[(string) $variable];
		return null;
	}


	/**
	 * Get the cache directory path.
	 *
	 * @return	string	The location of the cache directory.
	 */
	public function getCacheDirectory()
	{
		return $this->cacheDirectory;
	}


	/**
	 * Get the compile directory path.
	 *
	 * @return	string	The location of the compile directory.
	 */
	public function getCompileDirectory()
	{
		return $this->compileDirectory;
	}


	/**
	 * Retrieve the compiled name for this template.
	 *
	 * @return	string					The special unique name, used for storing this file once compiled in the compile directory.
	 * @param	string $template		The filename of the template.
	 * @param	string[optional] $path	The optional path to this template.
	 */
	protected function getCompileName($template, $path = null)
	{
		// redefine template
		if($path !== null && realpath($template) === false) $template = $path . '/' . $template;

		// return the correct full path
		return md5(realpath($template)) . '_' . basename($template) . '.php';
	}


	/**
	 * Fetch the parsed content from this template.
	 *
	 * @return	string	 			The actual parsed content after executing this template.
	 * @param	string $template	The location of the template file, used to display this template.
	 */
	public function getContent($template)
	{
		// cache tags can not be combined with this method
		if(!empty($this->cache)) throw new SpoonTemplateException('You can not use this method when the template uses cache tags.');

		// turn on output buffering
		ob_start();

		// show output
		$this->display($template);

		// return template content
		return ob_get_clean();
	}


	/**
	 * Get the force compiling directive.
	 *
	 * @return	bool	Do we need to recompile this template every time it's loaded.
	 */
	public function getForceCompile()
	{
		return $this->forceCompile;
	}


	/**
	 * Is the cache for this item still valid.
	 *
	 * @return	bool			Is this template block cached?
	 * @param	string $name	The name of the cached block.
	 */
	public function isCached($name)
	{
		// doesnt exist
		if(!isset($this->cache[(string) $name])) throw new SpoonTemplateException('No cache with the name "' . (string) $name . '" is known.');

		// last modification date
		$time = @filemtime($this->cacheDirectory . '/' . (string) $name . '_cache.tpl');

		// doesn't exist
		if($time === false) return false;

		// not valid
		if((time() - (int) $time) > $this->cache[(string) $name]) return false;

		// still valid
		return true;
	}


	/**
	 * Map a modifier to a given function/method.
	 *
	 * @param	string $name		The name that you wish to use in the templates as a modifier.
	 * @param	mixed $function		The function or method to map this name to. In case it's a method, provide this as an array containing class and method name.
	 */
	public function mapModifier($name, $function)
	{
		SpoonTemplateModifiers::mapModifier($name, $function);
	}


	/**
	 * Set the cache directory.
	 *
	 * @param	string $path	The location of the directory where you want to store your cached template blocks.
	 */
	public function setCacheDirectory($path)
	{
		$this->cacheDirectory = (string) $path;
	}


	/**
	 * Set the compile directory.
	 *
	 * @param	string $path	The location of the directory where you want to store your compiled templates.
	 */
	public function setCompileDirectory($path)
	{
		$this->compileDirectory = (string) $path;
	}


	/**
	 * If enabled, recompiles a template even if it has already been compiled.
	 *
	 * @param	bool[optional] $on	Do we need to recompile the template every time it loads.
	 */
	public function setForceCompile($on = true)
	{
		$this->forceCompile = (bool) $on;
	}
}


/**
 * This exception is used to handle template related exceptions.
 *
 * @package		spoon
 * @subpackage	template
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		0.1.1
 */
class SpoonTemplateException extends SpoonException {}
