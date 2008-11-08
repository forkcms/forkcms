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
 * @since			1.1.0
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

			// parse variables
			$this->content = $this->parseVariables($this->content, $this->scope);

			// parse iterations
			$this->content = $this->parseIterations($this->content, $this->iterations);

			// parse cache tags
			$this->content = $this->parseCache($this->content);

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
	// @todo proper schrijven gij se kleine vetzak!
	private function parseCache($content)
	{
		// regex pattern
		$pattern = "/{cache:([a-z0-9-_\.]+)}.*?{\/cache:\\1}/is";

		// find matches
		if(preg_match_all($pattern, $content, $matches))
		{
			// loop matches
			foreach($matches[1] as $match)
			{
				// search for
				$aSearch[] = '{cache:'. $match .'}';
				$aSearch[] = '{/cache:'. $match .'}';

				// replace with
				$aReplace[0] = '<?php if(!$this->isCached(\''. $match .'\')): ?>'. "\n";
				$aReplace[0] .= '<?php ob_start(); ?>'. "\n";
				$aReplace[1] = '<?php SpoonFile::setFileContent($this->cacheDirectory';
				$aReplace[1] .= " .'/". $match ."_cache.tpl', ob_get_clean()); ?>\n";
				$aReplace[1] .= '<?php endif; ?>' ."\n";
				$aReplace[1] .= '<?php require $this->cacheDirectory';
				$aReplace[1] .= " .'/";
				$aReplace[1] .= $match;
				$aReplace[1] .= "_cache.tpl'; ?>";
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
		$pattern = "|{include:file=([a-z0-9-_\.\'\"\:\.\{\$\}\/]+)}|is";

		// find matches
		if(preg_match_all($pattern, $content, $matches))
		{
			// loop matches
			foreach($matches[1] as $match)
			{
				$file = $match;

				$file = str_replace(array('"', "'"), '', $file);

				$array = array();

				$pattern = '/\{\$([a-z0-9\.\[\]\_\-\|\:\'\"\$\s])*\}/i';
				if(preg_match_all($pattern, $file, $matches2))
				{
					foreach($matches2[0] as $smatch)
					{
						$array[] = $this->parseVariable($smatch, $scope);
					}
				}


				$file = preg_replace($pattern, '%s', $file);
				$file = str_replace('%s', "'. %s .'", $file);

				$file = "'" . $file ."'";
				if(substr($file, 0, 4) == "''. ") $file = substr($file, 4);
				if(substr($file, -4) == " .''") $file = substr($file, 0, -4);
				$file = vsprintf($file, $array);

				// replace den include tag
				$aSearch[0] = '{include:file='. $match .'}';

				$template = eval('return '. $file .';');

				if(substr($template, 0, 1) != '/')
				{
					$template = dirname(realpath($this->template)) .'/'. $template;
				}

//				Spoon::dump($template);

				$scopeString = (is_array($scope)) ? implode("', '", $scope) : '';
				if(is_array($scope) && count($scope) == 1) $scopeString = '\''. $scope[0] .'\'';
				$aReplace[0] = '<?php if($this->getForceCompile()) $this->compile(\''. $template .'\', array('. $scopeString .')); ?>' ."\n";
				$aReplace[0] .= '<?php $return = @include $this->getCompileDirectory() .\'/\'. \''. $this->getCompileName($template) .'\'; ?>' ."\n";
				$aReplace[0] .= '<?php if($return === false): ?>' ."\n";

				$aReplace[0] .= '<?php $this->compile(\''. $template .'\', array('. $scopeString .')); ?>' ."\n";
				$aReplace[0] .= '<?php @include $this->getCompileDirectory() .\'/'. $this->getCompileName($template) .'\'; ?>' ."\n";
				$aReplace[0] .= '<?php endif; ?>' ."\n";

				$content = str_replace($aSearch, $aReplace, $content);
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
	// @todo proper schrijven
	// @todo implementeren van includes parsing. Deze includes MOETEN statisch zijn, wie zegt da nu weer?
	private function parseIterations($content, $iterations, array $scope = null)
	{
		foreach($iterations as $aIteration)
		{
			// update scope
			$scope[] = $aIteration['name'];

			// parse variables
			$aIteration['content'] = $this->parseVariables($aIteration['content'], $scope);

			// parse options
			$aIteration['content'] = $this->parseOptions($aIteration['content'], $scope);

			// parse subiterations
			if($aIteration['children'] !== null) $aIteration['content'] = $this->parseIterations($aIteration['content'], $aIteration['children'], $scope);

			// search & replace shit
			$search = '[iteration:'. $aIteration['code'] .']';

			// scope shitters
			if($scope !== null && count($scope) != 1)
			{
//				Spoon::dump($scope, false);
				while(1)
				{
					$variableTemp = '$this->variables';
//					Spoon::dump($scope);
					foreach($scope as $scopy) $variableTemp .= '[\''. $scopy .'\'][0]';
//					$variableTemp .= '['. $aIteration['name'] .']';
					$variableTemp = substr($variableTemp, 0, strlen($variableTemp) - 3);

//					Spoon::dump($variableTemp);
					$code = 'return (isset('. $variableTemp .'));';
//					Spoon::dump($code);
					$exists = eval($code);

					if($exists)
					{
						$variable = '$'. $scope[(count($scope) -2)] . '[\''. $aIteration['name'] .'\']';
						$replace = '<?php foreach('. $variable .' as $'. $aIteration['name'] ."): ?>\n";
						break;
					}

					elseif(count($scope) == 1)
					{
						$variable = '$this->variables' . '['. $aIteration['name'] .']';
						$replace = '<?php foreach('. $variable .' as $'. $aIteration['name'] ."): ?>\n";
						break;
					}

					else array_pop($scope);

					// securitas or elsas fucktas!
					break;
				}

			}

			else $replace = '<?php foreach($this->variables[\''. $aIteration['name'] .'\'] as $'. $aIteration['name'] ."): ?>\n";

			/*$replace = '<?php foreach($this->variables[\''. $aIteration['name'] .'\'] as $'. $aIteration['name'] ."): ?>\n";*/
//			$replace .= $aIteration['content'] ."\n"; // de oude content
			$replace .= $this->parseIncludes($aIteration['content'], $scope) ."\n";
			$replace .= "<?php endforeach; ?>\n";
			$content = str_replace($search, $replace, $content);

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


	// @todo de scope werkt nu nog maar op het diepste niveau, hij moet telkens 1tje omhoog gaan!
	private function parseVariable($variable, array $scope = null)
	{
		// strip '{$' and '}'
		$variable = ltrim($variable, '{$');
		$variable = rtrim($variable, '}');

		// fetch modifiers
		$aVar = explode('|', $variable);

		// base variable
		//$variable = '$this->variables';
		$variable = '';

		// convert multiple dots to a single one
		$aVar[0] = preg_replace('/\.+/', '.', $aVar[0]);
		$aVar[0] = trim($aVar[0], '.');

		// glue using the dots
		$aVarChunks = explode('.', $aVar[0]);
		foreach($aVarChunks as $chunk) $variable .= "['$chunk']";


		if($scope !== null && is_array($scope) && count($scope))
		{
			while(1)
			{
				$variableTemp = '$this->variables';
				foreach($scope as $scopy) $variableTemp .= '[\''. $scopy .'\'][0]';
				$variableTemp .= $variable;
				$code = 'return (isset('. $variableTemp .'));';
				$exists = eval($code);

				if($exists)
				{
					$variable = '$'. $scope[(count($scope) -1)] . $variable;
					break;
				}

				else array_pop($scope);

				if(count($scope) == 0) break;
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
		 * Every variable needs to be searched & replace one by one,
		 * since then the nesting process works as intended.
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
		return $content = preg_replace("/\<\?php(.*)\?\>/siU", '', $content);
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