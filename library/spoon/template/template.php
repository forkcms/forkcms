<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package		template
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @author 		Tijs Verkoyen <tijs@spoon-library.be>
 * @author		Dave Lens <dave@spoon-library.be>
 * @since		0.1.1
 */


/**
 * This exception is used to handle template related exceptions.
 *
 * @package		template
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @since		0.1.1
 */
class SpoonTemplateException extends SpoonException {}


/** Filesystem package */
require_once 'spoon/filesystem/filesystem.php';

/** SpoonDate class */
require_once 'spoon/date/date.php';


/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package		template
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @author		Tijs Verkoyen <tijs@spoon-library.be>
 * @since		0.1.1
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
	private $strict = true;


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
			$aFiles = SpoonDirectory::getList($this->cacheDirectory, true, array(), '|.*\_cache\.tpl|');

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
			$aFiles = SpoonDirectory::getList($this->compileDirectory, true, array(), '|.*\.tpl\.php|');

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
		if(mb_substr($template, 0, 1, SPOON_CHARSET) != '/') $template = $path .'/'. $template;

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
		// validate name
		if(trim($template) == '') throw new SpoonTemplateException('Please provide a template.');

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
	 * Retrieves the already assigned value
	 *
	 * @return	mixed
	 * @param	string $variable
	 */
	public function getAssignedValue($variable)
	{
		if(isset($this->variables[(string) $variable])) return $this->variables[(string) $variable];
		return null;
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
		if(mb_substr($template, 0, 1, SPOON_CHARSET) != '/' && $path !== null) $template = $path .'/'. $template;

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
		if(!isset($this->cache[(string) $name])) throw new SpoonTemplateException('No cache with the name "'. (string) $name .'" is known.');

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


/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package		template
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @since		1.0.0
 */
class SpoonTemplateCompiler
{
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
	 * Working content
	 *
	 * @var	string
	 */
	private $content;


	/**
	 * Always recompile
	 *
	 * @var	bool
	 */
	private $foreCompile = false;


	/**
	 * List of form objects
	 *
	 * @var	array
	 */
	private $forms = array();


	/**
	 * Cached list of the modifiers
	 *
	 * @var	array
	 */
	private $modifiers = array();


	/**
	 * Is the content already parsed
	 *
	 * @var	bool
	 */
	private $parsed = false;


	/**
	 * Strict setting
	 *
	 * @var	bool
	 */
	private $strict = true;


	/**
	 * Template file
	 *
	 * @var	string
	 */
	private $template;


	/**
	 * List of variables
	 *
	 * @var	array
	 */
	private $variables = array();


	/**
	 * Class constructor.
	 *
	 * @return	void
	 * @param	string $template
	 * @param	array $variables
	 */
	public function __construct($template, array $variables)
	{
		$this->template = (string) $template;
		$this->variables = $variables;
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
	 * Retrieve the content
	 *
	 * @return	string
	 */
	public function getContent()
	{
		if(!$this->parsed) $this->parse();
		return $this->content;
	}


	/**
	 * Retrieve a unique name for an iteration
	 *
	 * @return	string
	 * @param	string $name
	 * @param	array $iterations
	 */
	private function getUniqueIteration($name, &$iterations)
	{
		// redefine name
		$name = md5((string) $name);

		// fetch unique name
		return (isset($iterations[$name])) ? $this->getUniqueIteration($name, $iterations) : $name;
	}


	/**
	 * Creates a string of the provided value with the variables encapsulated
	 *
	 * @return	string
	 * @param	string $value
	 */
	private function getVariableString($value)
	{
		// init var
		$aVariables = array();

		// remove ' and "
		$value = str_replace(array('"', "'"), '', (string) $value);

		// regex pattern
		$pattern = '/\{\$([a-z0-9\.\[\]\_\-\|\:\'\"\$\s])*\}/i';

		// find variables
		if(preg_match_all($pattern, $value, $matches))
		{
			foreach($matches[0] as $match) $aVariables[] = $this->parseVariable($match);
		}

		// replace the variables by %s
		$value = preg_replace($pattern, '%s', $value);

		// encapsulate the vars
		$value = "'". str_replace('%s', "'. %s .'", $value) ."'";

		// fix errors
		if(mb_substr($value, 0, 4, SPOON_CHARSET) == "''. ") $value = mb_substr($value, 4, mb_strlen($value, SPOON_CHARSET), SPOON_CHARSET);
		if(mb_substr($value, -4, mb_strlen($value, SPOON_CHARSET), SPOON_CHARSET) == " .''") $value = mb_substr($value, 0, -4, SPOON_CHARSET);

		// add the variables
		return vsprintf($value, $aVariables);
	}


	/**
	 * Parse the template
	 *
	 * @return	void
	 */
	private function parse()
	{
		// not yet parsed
		if(!$this->parsed)
		{
			// add to the list of parsed files
			$this->files[] = $this->getCompileName($this->template);

			// map modifiers
			$this->modifiers = SpoonTemplateModifiers::getModifiers();

			// set content
			$this->content = SpoonFile::getContent($this->template);

			// strip php code
			$this->content = $this->stripCode($this->content);

			// strip comments
			$this->content = $this->stripComments($this->content);

			// parse iterations
			$this->content = $this->parseIterations($this->content);

			// includes
			$this->content = $this->parseIncludes($this->content);

			// parse options
			$this->content = $this->parseOptions($this->content);

			// parse cache tags
			$this->content = $this->parseCache($this->content);

			// parse variables
			$this->content = $this->parseVariables($this->content);

			// parse forms
			$this->content = $this->parseForms($this->content);

			// error reporting @todo @davy check me
			if(SPOON_DEBUG) $this->content = '<?php error_reporting(E_ALL | E_STRICT); ?>'. "\n". $this->content;
			else $this->content = '<?php error_reporting(E_WARNING); ?>'. "\n". $this->content;

			// parsed
			$this->parsed = true;
		}
	}


	/**
	 * Parse the cache tags
	 *
	 * @return	string
	 * @param	string $content
	 */
	private function parseCache($content)
	{
		// regex pattern
		$pattern = "/{cache:([a-z0-9-_\.\{\$\}]+)}.*?{\/cache:\\1}/is";

		// find matches
		if(preg_match_all($pattern, $content, $matches))
		{
			// loop matches
			foreach($matches[1] as $match)
			{
				// variable
				$variable = $this->getVariableString($match);

				// search for
				$aSearch[0] = '{cache:'. $match .'}';
				$aSearch[1] = '{/cache:'. $match .'}';

				// replace with
				$aReplace[0] = "<?php if(!\$this->isCached(". $variable .")): ?>\n<?php ob_start(); ?>";
				$aReplace[1] = "<?php SpoonFile::setContent(\$this->cacheDirectory .'/'. $variable .'_cache.tpl', ob_get_clean()); ?>\n<?php endif; ?>\n";
				$aReplace[1] .= "<?php require \$this->cacheDirectory .'/'. $variable .'_cache.tpl'; ?>";

				// execute
				$content = str_replace($aSearch, $aReplace, $content);
			}
		}

		return $content;
	}


	/**
	 * Parses the cycle tags in the given content
	 *
	 * @return	string
	 * @param	string $content
	 */
	private function parseCycle($content, $iteration)
	{
		// regex pattern
		$pattern = "|{cycle([a-z0-9-_\.\'\"\:\<\/\>\s]+)+?}|is";

		// find matches
		if(preg_match_all($pattern, $content, $matches))
		{
			// convert multiple dots to a single one
			$name = preg_replace('/\.+/', '.', $iteration);
			$name = trim($name, '.');

			// explode using the dots
			$aChunks = explode('.', $name);

			// number of chunks
			$numChunks = count($aChunks);

			// 1 or 2 chunks ?
			if($numChunks == 1) $variable = '$'. $aChunks[0];
			else $variable = '$'. $aChunks[0] .'[\''. $aChunks[1] .'\']';

			// loop matches
			foreach($matches[1] as $i => $match)
			{
				// create list
				$aCycle = explode(':', trim($match, ':'));

				// search & replace
				$search = $matches[0][$i];
				$replace = '<?php echo $this->cycle('. $variable .'I, array(\''. implode('\',\'', $aCycle) .'\')); ?>';

				// init var
				$aIterations = array();

				// match current iteration
				preg_match_all('|{iteration:'. $iteration .'}.*{/iteration:'. $iteration .'}|ismU', $content, $aIterations);

				// loop mathes
				foreach($aIterations as $block)
				{
					// build new content
					$newContent = str_replace($search, $replace, $block);

					// replace in original content
					$content = str_replace($block, $newContent, $content);
				}
			}
		}

		return $content;
	}


	/**
	 * Parse the forms
	 *
	 * @return	string
	 * @param	string $content
	 */
	private function parseForms($content)
	{
		// regex pattern
		$pattern = '/\{form:([a-z0-9_]+?)\}?/siU';

		// find matches
		if(preg_match_all($pattern, $content, $matches))
		{
			// loop matches
			foreach($matches[1] as $name)
			{
				// form object with that name exists
				if(isset($this->forms[$name]))
				{
					// start & close tag
					$aSearch = array('{form:'. $name .'}', '{/form:'. $name .'}');
					$aReplace[0] = '<form action="<?php echo $this->forms[\''. $name .'\']->getAction(); ?>" method="<?php echo $this->forms[\''. $name .'\']->getMethod(); ?>"<?php echo $this->forms[\''. $name .'\']->getParametersHTML(); ?>>' ."\n<div>\n";
					$aReplace[0] .= $this->forms[$name]->getField('form')->parse();
					$aReplace[1] = "\n</div>\n</form>";
					$content = str_replace($aSearch, $aReplace, $content);
				}
			}
		}

		return $content;
	}


	/**
	 * Parse the include tags
	 *
	 * @return	string
	 * @param	string $content
	 */
	private function parseIncludes($content)
	{
		// regex pattern
		$pattern = "|{include:file=\"([a-z0-9-_\.\'\"\:\.\{\$\}\/]+)\"}|is";

		// find matches
		if(preg_match_all($pattern, $content, $matches))
		{
			// loop matches
			foreach($matches[1] as $match)
			{
				// file
				$file = $this->getVariableString($match);

				// template path
				$template = eval('error_reporting(0); return '. $file .';');

				// search string
				$search = '{include:file="'. $match .'"}';

				// replace string
				$replace = '<?php if($this->getForceCompile()) $this->compile(\''. dirname(realpath($this->template)) .'\', '. $file .'); ?>' ."\n";
				$replace .= '<?php $return = @include $this->getCompileDirectory() .\'/\'. $this->getCompileName('. $file .',\''. dirname(realpath($this->template)) .'\'); ?>' ."\n";
				$replace .= '<?php if($return === false): ?>' ."\n";
				$replace .= '<?php $this->compile(\''. dirname(realpath($this->template)) .'\', '. $file .'); ?>' ."\n";
				$replace .= '<?php @include $this->getCompileDirectory() .\'/\'. $this->getCompileName('. $file .',\''. dirname(realpath($this->template)) .'\'); ?>' ."\n";
				$replace .= '<?php endif; ?>' ."\n";

				// replace it
				$content = str_replace($search, $replace, $content);
			}
		}

		return $content;
	}


	/**
	 * Parse the iterations (recursively)
	 *
	 * @return	string
	 * @param	string $content
	 * @param	array $iterations
	 */
	private function parseIterations($content)
	{
		// fetch iterations
		$pattern = '/\{iteration:([a-z0-9_\.]+?)\}?/siU';

		// find matches
		if(preg_match_all($pattern, $content, $matches))
		{
			// init var
			$aIterations = array();

			// loop matches
			foreach($matches[1] as $match)
			{
				if(!in_array($match, $aIterations)) $aIterations[] = $match;
			}

			// has iterations
			if(count($aIterations) != 0)
			{
				// loop iterations
				foreach($aIterations as $iteration)
				{
					// parse cycle tag
					$content = $this->parseCycle($content, $iteration);

					// search
					$aSearch[0] = '{iteration:'. $iteration .'}';
					$aSearch[1] = '{/iteration:'. $iteration .'}';

					// convert multiple dots to a single one
					$name = preg_replace('/\.+/', '.', $iteration);
					$name = trim($name, '.');

					// explode using the dots
					$aChunks = explode('.', $name);

					// number of chunks
					$numChunks = count($aChunks);

					// 1 or 2 chunks?
					if($numChunks == 2) $variable = '$'. $aChunks[0] .'[\''. $aChunks[1] .'\']';
					else $variable = '$this->variables[\''. $aChunks[0] .'\']';

					// replace
					$aReplace[0] = '<?php foreach((array) '. $variable .' as $'. $aChunks[$numChunks - 1] .'I => $'. $aChunks[$numChunks - 1] .'): ?>';
					$aReplace[0] .= "<?php
					if(isset(\$". $aChunks[$numChunks - 1] ."['formElements']) && is_array(\$". $aChunks[$numChunks - 1] ."['formElements']))
					{
						foreach(\$". $aChunks[$numChunks - 1] ."['formElements'] as \$name => \$object)
						{
							// @todo if we're dealing with radiobuttons or checkboxes, an alternative way of parsing needs to be used!
							\$". $aChunks[$numChunks - 1] ."[\$name] = \$object->parse();
							\$". $aChunks[$numChunks - 1] ."[\$name .'Error'] = (\$object->getErrors() == '') ? '' : '<span class=\"formError\">'. \$object->getErrors() .'</span>';
						}
					}
					?>
					";

					/*
					 * <?php
	if(isset($tabs['formElements']) && is_array($tabs['formElements']))
	{
		foreach($tabs['formElements'] as $name => $object)
		{
			$tabs[$name] = $object->parse();
			$tabs[$name .'Error'] = ($object->getErrors() == '') ? '' : '<span class="formError">'. $object->getErrors() .'</span>';
		}
	}

	?>
					 */


					$aReplace[1] = '<?php endforeach; ?>';

					// replace
					$content = str_replace($aSearch, $aReplace, $content);
				}
			}
		}

		return $content;
	}


	/**
	 * Parse the options in the given content & scope
	 *
	 * @return	string
	 * @param	string $content
	 * @param	array[optional] $scope
	 */
	private function parseOptions($content)
	{
		// regex pattern
		$pattern = "/{option:([a-z0-9-_\.\[\]\!]+)}.*?{\/option:\\1}/is";

		// init var
		$aOptions = array();

		// keep finding those options!
		while(1)
		{
			// find matches
			if(preg_match_all($pattern, $content, $matches))
			{
				// loop matches
				foreach($matches[1] as $match)
				{
					// redefine match
					$match = str_replace('!', '', $match);

					// fetch variable
					$variable = $this->parseVariable($match);

					// already matched
					if(in_array($match, $aOptions)) continue;

					// not yet used
					$aOptions[] = $match;

					// search for
					$aSearch[] = '{option:'. $match .'}';
					$aSearch[] = '{/option:'. $match .'}';
					// inverse option
					$aSearch[] = '{option:!'. $match .'}';
					$aSearch[] = '{/option:!'. $match .'}';

					// replace with
					$aReplace[] = '<?php if(isset('. $variable .') && count('. $variable .') != 0 && '. $variable .' != \'\' && '. $variable .' !== false): ?>';
					$aReplace[] = '<?php endif; ?>';
					// inverse option
					$aReplace[] = '<?php if(!isset('. $variable .') || count('. $variable .') == 0 || '. $variable .' == \'\' || '. $variable .' === false): ?>';
					$aReplace[] = '<?php endif; ?>';

					// go replace
					$content = str_replace($aSearch, $aReplace, $content);

					// reset vars
					unset($aSearch);
					unset($aReplace);
				}
			}

			// no matches
			else break;
		}

		return $content;
	}


	/**
	 * Parse the template to a file
	 *
	 * @return	void
	 */
	public function parseToFile()
	{
		SpoonFile::setContent($this->compileDirectory .'/'. $this->getCompileName($this->template), $this->getContent());
	}


	/**
	 * Parse a single variable within the provided scope or up
	 *
	 * @return	string
	 * @param	string $variable
	 * @param	array[optional] $scope
	 */
	private function parseVariable($variable)
	{
		// strip '{$' and '}'
		$variable = ltrim($variable, '{$');
		$variable = rtrim($variable, '}');

		// replace [ & ]
		$variable = str_replace(array('[', ']'), '.', $variable);

		// fetch modifiers
		$aVar = explode('|', $variable);

		// base variable
		$variable = '';

		// convert multiple dots to a single one
		$aVar[0] = preg_replace('/\.+/', '.', $aVar[0]);
		$aVar[0] = trim($aVar[0], '.');

		// explode using the dots
		$aVarChunks = explode('.', $aVar[0]);

		// number of chunks
		$numChunks = count($aVarChunks);

		// more than 2 chunks is NOT allowed
		if($numChunks > 2) return '\'[$'. implode('|', $aVar) .']\'';

		// 2 chunks
		elseif($numChunks == 2) $variable = '$'. $aVarChunks[0] .'[\''. $aVarChunks[1] .'\']';

		// 1 chunk
		else $variable = '$this->variables[\''. $aVar[0] .'\']';

		// has modifiers ?
		if(isset($aVar[1]))
		{
			// loop modifiers
			foreach($aVar as $i => $modifier)
			{
				// skip first record
				if($i == 0) continue;

				// modifier + parameters
				$aModifier = explode(':', $modifier);

				// modifier doesn't exist
				if(!isset($this->modifiers[$aModifier[0]])) throw new SpoonTemplateException('The modifier ('. $aModifier[0] .') does not exist.');

				// add call
				else
				{
					// method call
					if(is_array($this->modifiers[$aModifier[0]])) $variable = implode('::', $this->modifiers[$aModifier[0]]) .'('. $variable;

					// function call
					else $variable = $this->modifiers[$aModifier[0]] .'('. $variable;
				}

				// has arguments
				if(count($aModifier) > 1)
				{
					// loop arguments
					for($i = 1; $i < count($aModifier); $i++)
					{
						// add modifier
						$variable .= ', '. $aModifier[$i];
					}
				}

				// add close tag
				$variable .= ')';
			}
		}

		return $variable;
	}


	/**
	 * Parse all the variables in this string
	 *
	 * @return	string
	 * @param	string $content
	 * @param	array[optional] $scope
	 */
	private function parseVariables($content, array $scope = null)
	{
		// regex pattern
		$pattern = '/\{\$([a-z0-9\.\[\]\_\-\|\:\'\"\$\s])*\}/i';

		// temp variables
		$aVariables = array();

		// we want to keep parsing vars until none can be found.
		while(1)
		{
			// find matches
			if(preg_match_all($pattern, $content, $matches))
			{
				// loop matches
				foreach($matches[0] as $match)
				{
					// variable doesn't already exist
					if(array_search($match, $aVariables, true) === false)
					{
						// unique key
						$key = md5($match);

						// add parsed variable
						$aVariables[$key] = $this->parseVariable($match, $scope);

						// replace in content
						$content = str_replace($match, '[$'. $key .']', $content);
					}
				}
			}

			// break the loop, no matches were found
			else break;
		}

		/**
		 * Every variable needs to be searched & replaced one by one,
		 * since only then the nesting process works as intended.
		 */
		foreach($aVariables as $key => $value)
		{
			// loop each element except this one
			foreach($aVariables as $keyIndex => $valueContent)
			{
				// skip myself
				if($key == $keyIndex) continue;

				// replace myself in the other var
				$aVariables[$keyIndex] = str_replace('.$'. $key .'.', $aVariables[$key], $aVariables[$keyIndex]);
			}
		}

		/**
		 * Now loop these vars again, but this time parse them in the
		 * content we're actually working with.
		 */
		foreach($aVariables as $key => $value)
		{
			$content = str_replace('[$'. $key .']', '<?php echo '. $value .'; ?>', $content);
		}

		return $content;
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
	 * Sets the forms
	 *
	 * @return	void
	 * @param	array $forms
	 */
	public function setForms(array $forms)
	{
		$this->forms = $forms;
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


	/**
	 * Strips php code from the content
	 *
	 * @return	string
	 * @param	string $content
	 */
	private function stripCode($content)
	{
		return $content = preg_replace("/\<\?(php)?(.*)\?\>/siU", '', $content);
	}


	/**
	 * Strip comments from the output
	 *
	 * @return	void
	 * @param	string $content
	 */
	private function stripComments($content)
	{
		return $content = preg_replace("/\{\*(.+?)\*\}/s", '', $content);
	}
}


/**
 * This class implements modifier mapping for the templat engine
 *
 * @package		template
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @since		1.0.0
 */
class SpoonTemplateModifiers
{
	/**
	 * Default modifiers mapped to their functions
	 *
	 * @var	array
	 */
	private static $modifiers = array(	'addslashes' => 'addslashes',
										'createhtmllinks' => array('SpoonTemplateModifiers', 'createHTMLLinks'),
										'date' => array('SpoonTemplateModifiers', 'date'),
										'htmlentities' => array('SpoonFilter', 'htmlentities'),
										'lowercase' => array('SpoonTemplateModifiers', 'lowercase'),
										'ltrim' => 'ltrim',
										'nl2br' => 'nl2br',
										'repeat' => 'str_repeat',
										'rtrim' => 'rtrim',
										'shuffle' => 'str_shuffle',
										'sprintf' => 'sprintf',
										'stripslashes' => 'stripslashes',
										'substring' => 'substr',
										'trim' => 'trim',
										'ucfirst' => 'ucfirst',
										'ucwords' => 'ucwords',
										'uppercase' => array('SpoonTemplateModifiers', 'uppercase'));


	/**
	 * Clears the entire modifiers list
	 *
	 * @return	void
	 */
	public static function clearModifiers()
	{
		self::$modifiers = array();
	}


	/**
	 * Converts links to HTML links (only to be used with cleartext)
	 *
	 * @return	string
	 * @param	string $text
	 */
	public static function createHTMLLinks($text)
	{
		return SpoonFilter::replaceURLsWithAnchors($text, false);
	}


	/**
	 * Formats a language specific date
	 *
	 * @return	string
	 * @param	int $timestamp
	 * @param	string[optional] $format
	 */
	public function date($timestamp, $format = 'Y-m-d H:i:s', $language = 'en')
	{
		return SpoonDate::getDate($format, $timestamp, $language);
	}


	/**
	 * Retrieves the modifiers
	 *
	 * @return	array
	 */
	public static function getModifiers()
	{
		return self::$modifiers;
	}


	/**
	 * Makes the string lowercase and takes entities into account
	 *
	 * @return	string
	 * @param	string $string
	 */
	public static function lowercase($string)
	{
		return mb_convert_case($string, MB_CASE_LOWER, SPOON_CHARSET);
	}


	/**
	 * Maps a specific modifier to a function/method
	 *
	 * @return	void
	 * @param	string $name
	 * @param	mixed $function
	 */
	public static function mapModifier($name, $function)
	{
		// validate modifier
		if(!SpoonFilter::isValidAgainstRegexp('/[a-zA-Z0-9\_\-]+/', (string) $name)) throw new SpoonTemplateException('Modifier names can only contain a-z, 0-9 and - and _');

		// class method
		if(is_array($function))
		{
			// not enough elements
			if(count($function) != 2) throw new SpoonTemplateException('The array should contain the class and static method.');

			// method doesn't exist
			if(!method_exists($function[0], $function[1])) throw new SpoonTemplateException('The method "'. $function[1] .'" in the class '. $function[0] .' does not exist.');

			// all fine
			self::$modifiers[(string) $name] = $function;
		}

		// regular function
		else
		{
			// function doesn't exist
			if(!function_exists((string) $function)) throw new SpoonTemplateException('The function "'. (string) $function .'" does not exist.');

			// all fine
			self::$modifiers[(string) $name] = $function;
		}
	}


	/**
	 * Makes the string uppercase and takes entities into account
	 *
	 * @return	string
	 * @param	string $string
	 */
	public static function uppercase($string)
	{
		return mb_convert_case($string, MB_CASE_UPPER, SPOON_CHARSET);
	}
}

?>