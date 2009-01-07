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
		if(substr($value, 0, 4) == "''. ") $value = substr($value, 4);
		if(substr($value, -4) == " .''") $value = substr($value, 0, -4);

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
			$this->content = SpoonFile::getFileContent($this->template, 'string', $this->strict);

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
				$aReplace[1] = "<?php SpoonFile::setFileContent(\$this->cacheDirectory .'/'. $variable .'_cache.tpl', ob_get_clean()); ?>\n<?php endif; ?>\n";
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

				// replace it
				$content = str_replace($search, $replace, $content);
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
					$aReplace[0] = '<form action="'. $this->forms[$name]->getAction() .'" method="'. $this->forms[$name]->getMethod() .'"'. $this->forms[$name]->getParametersAsHTML() .'>';
					$aReplace[0] .= '<fieldset style="display: none;">'. $this->forms[$name]->getField('form')->parse() .'</fieldset>';
					$aReplace[1] = '</form>';
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

				// template doesn't start from the root
				if(substr($template, 0, 1) != '/') $template = dirname(realpath($this->template)) .'/'. $template;

				// search string
				$search = '{include:file="'. $match .'"}';

				// replace string
				$replace = '<?php if($this->getForceCompile()) $this->compile(\''. $template .'\'); ?>' ."\n";
				$replace .= '<?php $return = @include $this->getCompileDirectory() .\'/\'. $this->getCompileName(\''. $template .'\'); ?>' ."\n";
				$replace .= '<?php if($return === false): ?>' ."\n";
				$replace .= '<?php $this->compile(\''. $template .'\'); ?>' ."\n";
				$replace .= '<?php @include $this->getCompileDirectory() .\'/\'. $this->getCompileName(\''. $template .'\'); ?>' ."\n";
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
	private function parseOptions($content, array $scope = null)
	{
		// regex pattern
		$pattern = "/{option:([a-z0-9-_\.\[\]]+)}.*?{\/option:\\1}/is";

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
					$variable = $this->parseVariable($match);

					// search for
					$aSearch[] = '{option:'. $match .'}';
					$aSearch[] = '{/option:'. $match .'}';

					// replace with
					$aReplace[] = '<?php if(isset('. $variable .') && count('. $variable .') != 0 && '. $variable .' != \'\' && '. $variable .' !== false): ?>';
					$aReplace[] = '<?php endif; ?>';

					// go replace
					$content = str_replace($aSearch, $aReplace, $content);
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

?>