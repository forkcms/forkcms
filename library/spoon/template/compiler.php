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
 * @since			1.0.0
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
	 * List of replaced iterations
	 *
	 * @var	array
	 */
	private $iterations = array();


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
	 * Variable scope
	 *
	 * @var	array
	 */
	private $scope = array();


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
	 * @param	array[optional] $scope
	 */
	public function __construct($template, array $variables, array $scope = null)
	{
		$this->template = (string) $template;
		$this->variables = $variables;
		if($scope !== null) $this->scope = $scope;
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
	 * @param	array[optional] $scope
	 */
	private function getVariableString($value, array $scope = null)
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
			// loop matches
			foreach($matches[0] as $match) $aVariables[] = $this->parseVariable($match, $scope);
		}

		// replace the variables by %s
		$value = preg_replace($pattern, '%s', $value);

		// encapsulate the vars
		$value = "'". str_replace('%s', "'. %s .'", $value) ."'";

		// fix errors
		if(substr($value, 0, 4) == "''. ") $value = substr($value, 4);
		if(substr($value, -4) == " .''") $value = substr($value, 0, -4);

		// add the variables
		return vsprintf($value, $aVariables);
	}


	/**
	 * Obfuscate all the iterations
	 *
	 * @return	string
	 * @param	string $content
	 * @param	array $iterations
	 * @param 	string[optional] $parent
	 */
	private function obfuscateIterations($content, &$iterations, $parent = null)
	{
		// regex pattern
		$pattern = '/\{iteration:([a-z0-9_]+?)\}(.*)\{\/iteration:\\1(\})??/siU';

		// find matches
		if(preg_match_all($pattern, $content, $matches))
		{
			// number of iterations found
			$numIterations = count($matches[0]);

			// loop iterations
			for($i = 0; $i < $numIterations; $i++)
			{
				// unique name
				$iterationName = $this->getUniqueIteration($matches[1][$i], $iterations);

				// set options
				$iterations[$iterationName]['name'] = $matches[1][$i];
				$iterations[$iterationName]['code'] = $iterationName;
				$iterations[$iterationName]['parent'] = $parent;
				$iterations[$iterationName]['content'] = $matches[2][$i];

				// replace by temp tags
				$content = str_replace($matches[0][$i], '[iteration:'. $iterationName .']', $content);

				// recursive
				$iterations[$iterationName]['content'] = $this->obfuscateIterations($iterations[$iterationName]['content'], $iterations[$iterationName]['children'], $matches[1][$i]);
			}
		}

		return $content;
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
			$this->content = SpoonFile::getFileContent($this->template, 'string', $this->strict);

			// strip php code
			$this->content = $this->stripCode($this->content);

			// strip comments
			$this->content = $this->stripComments($this->content);

			// obfuscate iterations
			$this->content = $this->obfuscateIterations($this->content, $this->iterations);

			// includes
			$this->content = $this->parseIncludes($this->content);

			// parse options
			$this->content = $this->parseOptions($this->content, $this->scope);

			// parse cache tags
			$this->content = $this->parseCache($this->content, $this->scope);

			// parse variables
			$this->content = $this->parseVariables($this->content, $this->scope);

			// parse iterations
			$this->content = $this->parseIterations($this->content, $this->iterations);

			// error reporting
			$this->content = "<?php error_reporting(E_WARNING); ?>\n". $this->content;

			// parsed
			$this->parsed = true;
		}
	}


	/**
	 * Parse the cache tags
	 *
	 * @return	string
	 * @param	string $content
	 * @param	array[optional] $scope
	 */
	private function parseCache($content, array $scope = null)
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
				$variable = $this->getVariableString($match, $scope);

				// search for
				$aSearch[] = '{cache:'. $match .'}';
				$aSearch[] = '{/cache:'. $match .'}';

				// replace with
				$aReplace[0] = "<?php if(!\$this->isCached(". $variable .")): ?>\n<?php ob_start(); ?>";
				$aReplace[1] = "<?php SpoonFile::setFileContent(\$this->cacheDirectory .'/'. $variable .'_cache.tpl', ob_get_clean()); ?>\n<?php endif; ?>\n";
				$aReplace[1] .= "<?php require \$this->cacheDirectory .'/'. $variable .'_cache.tpl'; ?>";

				// execute
				$content = str_replace($aSearch, $aReplace, $content);
			}
		}

		return $content;
	}


	/**
	 * Parse the include tags
	 *
	 * @return	string
	 * @param	string $content
	 * @param	array[optional] $scope
	 */
	private function parseIncludes($content, array $scope = null)
	{
		// regex pattern
		$pattern = "|{include:file=\"([a-z0-9-_\.\'\"\:\.\{\$\}\/]+)\"}|is";

		// find matches
		if(preg_match_all($pattern, $content, $matches))
		{
			Spoon::dump($matches);
			// loop matches
			foreach($matches[1] as $match)
			{
				// file
				$file = $this->getVariableString($match, $scope);

				// template name
				$template = eval('return '. $file .';');

				// template doesn't start from the root
				if(substr($template, 0, 1) != '/') $template = dirname(realpath($this->template)) .'/'. $template;

				// define the scope as a string
				$sScope = (is_array($scope)) ? implode("', '", $scope) : '';
				if(is_array($scope) && count($scope) == 1) $sScope = '\''. $scope[0] .'\'';

				// search string
				$search = '{include:file='. $match .'}';

				// replace string
				$replace = '<?php if($this->getForceCompile()) $this->compile(\''. $template .'\', array('. $sScope .')); ?>' ."\n";
				$replace .= '<?php $return = @include $this->getCompileDirectory() .\'/\'. \''. $this->getCompileName($template) .'\'; ?>' ."\n";
				$replace .= '<?php if($return === false): ?>' ."\n";
				$replace .= '<?php $this->compile(\''. $template .'\', array('. $sScope .')); ?>' ."\n";
				$replace .= '<?php @include $this->getCompileDirectory() .\'/'. $this->getCompileName($template) .'\'; ?>' ."\n";
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
	 * @param	array[optional] $scope
	 */
	private function parseIterations($content, $iterations, array $scope = null)
	{
		// loop iterations
		foreach($iterations as $aIteration)
		{
			// update scope
			$scope[] = $aIteration['name'];

			// parse cache
			$aIteration['content'] = $this->parseCache($aIteration['content'], $scope);

			// parse variables
			$aIteration['content'] = $this->parseVariables($aIteration['content'], $scope);

			// parse options
			$aIteration['content'] = $this->parseOptions($aIteration['content'], $scope);

			// parse subiterations
			if($aIteration['children'] !== null) $aIteration['content'] = $this->parseIterations($aIteration['content'], $aIteration['children'], $scope);

			// search string
			$search = '[iteration:'. $aIteration['code'] .']';

			// has a scope
			if(is_array($scope) && count($scope) != 1)
			{
				// loop the scope
				while(1)
				{
					// base variable
					$variableTemp = '$this->variables';

					// add the scope to the variable
					foreach($scope as $item) $variableTemp .= '[\''. $item .'\'][0]';

					// snoop off the last [0]
					$variableTemp = substr($variableTemp, 0, strlen($variableTemp) - 3);

					// check if this var exists
					$exists = eval('return (isset('. $variableTemp .'));');

					// the variable exists
					if($exists)
					{
						// rework the final variable
						$variable = '$'. $scope[(count($scope) -2)] . '[\''. $aIteration['name'] .'\']';

						// replace string
						$replace = '<?php foreach('. $variable .' as $'. $aIteration['name'] ."): ?>\n";

						// stop the while
						break;
					}

					// only one item in the scope
					elseif(count($scope) == 1)
					{
						// rework the final variable
						$variable = '$this->variables' . '['. $aIteration['name'] .']';

						// replace string
						$replace = '<?php foreach('. $variable .' as $'. $aIteration['name'] ."): ?>\n";

						// stop the while
						break;
					}

					// nothing found, remove the last item
					else array_pop($scope);
				}

			}

			// no scope defined
			else $replace = '<?php foreach($this->variables[\''. $aIteration['name'] .'\'] as $'. $aIteration['name'] ."): ?>\n";

			// add to the replace string
			$replace .= $this->parseIncludes($aIteration['content'], $scope) ."\n<?php endforeach; ?>";

			// replace it
			$content = str_replace($search, $replace, $content);

			// reset the scope
			$scope = null;
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
	private function parseOptions($content, array $scope = null)
	{
		// regex pattern
		$pattern = "/{option:([a-z0-9-_\.]+)}.*?{\/option:\\1}/is";

		// keep finding those options!
		while(1)
		{
			// find matches
			if(preg_match_all($pattern, $content, $matches))
			{
				// loop matches
				foreach($matches[1] as $match)
				{
					// fetch variable
					$variable = $this->parseVariable($match, $scope);

					// search for
					$aSearch[] = '{option:'. $match .'}';
					$aSearch[] = '{/option:'. $match .'}';

					// replace with
					$aReplace[] = '<?php if(isset('. $variable .') && '. $variable .' !== null && '. $variable .' !== false): ?>';
					$aReplace[] = '<?php endif; ?>';

					// go replace
					$content = str_replace($aSearch, $aReplace, $content);
				}
			}

			// no matchese
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
		SpoonFile::setFileContent($this->compileDirectory .'/'. $this->getCompileName($this->template), $this->getContent());
	}


	/**
	 * Parse a single variable within the provided scope or up
	 *
	 * @return	string
	 * @param	string $variable
	 * @param	array[optional] $scope
	 */
	private function parseVariable($variable, array $scope = null)
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

		// glue using the dots
		$aVarChunks = explode('.', $aVar[0]);
		foreach($aVarChunks as $chunk) $variable .= "['$chunk']";

		// @todo is da wel nodig hieronder?
		// temp variable
		$variableTemp = '$this->variables';

		// scope exists
		if(is_array($scope) && count($scope) != 0)
		{
			// loop until we find each variable
			while(1)
			{
				// temp variable
				$variableTemp = '$this->variables';

				// add the scope to the temp variable
				foreach($scope as $item) $variableTemp .= '[\''. $item .'\'][0]';

				// add the variable to the end
				$variableTemp .= $variable;

				// does this variable exists in this scope?
				$exists = eval('return (isset('. $variableTemp .'));');

				// variable exists
				if($exists)
				{
					// define final variable
					$variable = '$'. $scope[(count($scope) -1)] . $variable;

					// break while
					break;
				}

				// variable not found
				else array_pop($scope);

				// stop looping if there's no scope left
				if(count($scope) == 0)
				{
					// define final variable
					$variable = '$this->variables' . $variable;

					// break while
					break;
				}
			}
		}

		// no scope
		else $variable = '$this->variables' . $variable;

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
				$aVariables[$keyIndex] = str_replace('[$'. $key .']', $aVariables[$key], $aVariables[$keyIndex]);
			}
		}

		/**
		 * Now loop these vars again, but this time parse them in the
		 * content we're working with.
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

?>