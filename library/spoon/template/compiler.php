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
 * @author 		Tijs Verkoyen <tijs@spoon-library.com>
 * @author		Dave Lens <dave@spoon-library.com>
 * @since		0.1.1
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
	private $forceCompile = false;


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
	 * @param	string $template	The name of the template to compile.
	 * @param	array $variables	The list of possible variables.
	 */
	public function __construct($template, array $variables)
	{
		$this->template = (string) $template;
		$this->variables = $variables;
	}


	/**
	 * Retrieve the compiled name for this template.
	 *
	 * @return	string				The unique filename used to store the compiled template in the compile directory.
	 * @param	string $template	The name of the template.
	 */
	private function getCompileName($template)
	{
		return md5(realpath($template)) .'_'. basename($template) .'.php';
	}


	/**
	 * Retrieve the content.
	 *
	 * @return	string	The php compiled template.
	 */
	public function getContent()
	{
		if(!$this->parsed) $this->parse();
		return $this->content;
	}


	/**
	 * Creates a string of the provided value with the variables encapsulated.
	 *
	 * @return	string			The variable value as php code.
	 * @param	string $value	The value that needs to be compiled to php code.
	 */
	private function getVariableString($value)
	{
		// init var
		$variables = array();

		// regex
		$pattern = '/\{\$([a-z0-9_])+(\.([a-z0-9_])+)?\}/i';

		// find variables
		if(preg_match_all($pattern, $value, $matches))
		{
			// loop variables
			foreach($matches[0] as $match)
			{
				$variables[] = $this->parseVariable($match);
			}
		}

		// replace the variables by %s
		$value = preg_replace($pattern, '%s', $value);

		// encapsulate the vars
		$value = "'". str_replace('%s', "'. %s .'", $value) ."'";

		// fix errors
		if(mb_substr($value, 0, 4, SPOON_CHARSET) == "''. ") $value = mb_substr($value, 4, mb_strlen($value, SPOON_CHARSET), SPOON_CHARSET);
		if(mb_substr($value, -4, mb_strlen($value, SPOON_CHARSET), SPOON_CHARSET) == " .''") $value = mb_substr($value, 0, -4, SPOON_CHARSET);

		// cleanup
		$value = str_replace(".''.", '.', $value);

		// add the variables
		return vsprintf($value, $variables);
	}


	/**
	 * Check the string for syntax errors
	 *
	 * @return	bool
	 * @param	string $string
	 * @param	string $type
	 */
	private function isCorrectSyntax($string, $type)
	{
		// init vars
		$string = (string) $string;
		$type = SpoonFilter::getValue($type, array('cycle', 'iteration', 'option', 'variable'), 'variable', 'string');

		// types
		switch($type)
		{
			// cycle string
			case 'cycle':
				// the number of single qoutes should always be an even number
				if(!SpoonFilter::isEven(substr_count($string, "'"))) return false;
			break;

			// iteration string
			case 'iteration':
				// the number of square opening/closing brackets should be equal
				if(substr_count($string, '[') != substr_count($string, ']')) return false;

				// the number of single qoutes should always be an even number
				if(!SpoonFilter::isEven(substr_count($string, "'"))) return false;

				// first charachter should not be a number
				if(SpoonFilter::isInteger(substr($string, 2, 1))) return false;

				// square bracket followed by a dot is NOT allowed eg {option:variable[0].var}
				if(substr_count($string, '].') != 0) return false;

				// dot followed by a square bracket is NOT allowed eg {option:variable.['test']}
				if(substr_count($string, '.[') != 0) return false;

				// empty brackets are NOT allowed
				if(substr_count($string, '[]') != 0) return false;
			break;

			// option string
			case 'option':
				// the number of square opening/closing brackets should be equal
				if(substr_count($string, '[') != substr_count($string, ']')) return false;

				// the number of single qoutes should always be an even number
				if(!SpoonFilter::isEven(substr_count($string, "'"))) return false;

				// square bracket followed by a dot is NOT allowed eg {option:variable[0].var}
				if(substr_count($string, '].') != 0) return false;

				// dot followed by a square bracket is NOT allowed eg {option:variable.['test']}
				if(substr_count($string, '.[') != 0) return false;

				// empty brackets are NOT allowed
				if(substr_count($string, '[]') != 0) return false;
			break;

			// variable string
			case 'variable':
				// the number of square opening/closing brackets should be equal
				if(substr_count($string, '[') != substr_count($string, ']')) return false;

				// the number of single qoutes should always be an even number
				if(!SpoonFilter::isEven(substr_count($string, "'"))) return false;

				// first charachter should not be a number
				if(SpoonFilter::isInteger(substr($string, 2, 1))) return false;

				// square bracket followed by a dot is NOT allowed eg {$variable[0].var}
				if(substr_count($string, '].') != 0) return false;

				// dot followed by a square bracket is NOT allowed eg {$variable.['test']}
				if(substr_count($string, '.[') != 0) return false;

				// empty brackets are NOT allowed
				if(substr_count($string, '[]') != 0) return false;
			break;
		}

		return true;
	}


	/**
	 * Parse the template.
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

			// while developing, you might want to know about the undefined indexes
			$errorReporting = (SPOON_DEBUG) ? 'E_ALL | E_STRICT' : 'E_WARNING';
			$displayErrors = (SPOON_DEBUG) ? 'On' : 'Off';

			// add error_reporting setting
			$this->content = '<?php error_reporting('. $errorReporting .'); ini_set(\'display_errors\', \''. $displayErrors .'\'); ?>'. "\n". $this->content;

			// parsed
			$this->parsed = true;
		}
	}


	/**
	 * Parse the cache tags.
	 *
	 * @return	string				The updated content, containing the parsed cache tags.
	 * @param	string $content		The content that may contain the parse tags.
	 */
	private function parseCache($content)
	{
		// regex pattern
		$pattern = '/\{cache:([a-z0-9_\.\{\$\}]+)\}.*?\{\/cache:\\1\}/is';

		// find matches
		if(preg_match_all($pattern, $content, $matches))
		{
			// loop matches
			foreach($matches[1] as $match)
			{
				// variable
				$variable = $this->getVariableString($match);

				// init vars
				$search = array();
				$replace = array();

				// search for
				$search[0] = '{cache:'. $match .'}';
				$search[1] = '{/cache:'. $match .'}';

				// replace with
				$replace[0] = "<?php if(!\$this->isCached(". $variable .")): ?>\n<?php ob_start(); ?>";
				$replace[1] = "<?php SpoonFile::setContent(\$this->cacheDirectory .'/'. $variable .'_cache.tpl', ob_get_clean()); ?>\n<?php endif; ?>\n";
				$replace[1] .= "<?php require \$this->cacheDirectory .'/'. $variable .'_cache.tpl'; ?>";

				// execute
				$content = str_replace($search, $replace, $content);
			}
		}

		return $content;
	}


	/**
	 * Parses the cycle tags in the given content.
	 *
	 * @return	string				The updated content, containing the parsed cycle tags.
	 * @param	string $content		The content that may contain the cycle tags.
	 */
	private function parseCycle($content, $iteration)
	{
		// regex pattern
		$pattern = '/\{cycle((:\'[a-z0-9\-_\<\/\>\"\=\;\:\s]+\')+)\}/is';

		// find matches
		if(preg_match_all($pattern, $content, $matches))
		{
			// chunks
			$chunks = explode('.', $iteration);

			// number of chunks
			$numChunks = count($chunks);

			// variable string
			$variable = '$'. SpoonFilter::toCamelCase(str_replace(array('[', ']', "'", '_'), ' ', $chunks[$numChunks - 1]), ' ', true, SPOON_CHARSET);

			// loop matches
			foreach($matches[1] as $i => $match)
			{
				// correct cycle
				if($this->isCorrectSyntax($match, 'cycle'))
				{
					// init vars
					$cycle = '';
					$inParameter = false;
					$parameters = trim($match, ':');

					// loop every character
					for($c = 0; $c < mb_strlen($parameters, SPOON_CHARSET); $c++)
					{
						// fetch character
						$string = mb_substr($parameters, $c, 1, SPOON_CHARSET);

						// single quote in parameter, indicating the end for this parameter
						if($string == "'" && $inParameter) $inParameter = false;

						// single quotes, indicating the start of a new parameter
						elseif($string == "'" && !$inParameter) $inParameter = true;

						// semicolon outside parameter
						elseif($string == ':' && !$inParameter) $string = ', ';

						// add character
						$cycle .= $string;
					}

					// search & replace
					$search = $matches[0][$i];
					$replace = '<?php echo $this->cycle('. $variable .'I, array('. $cycle .')); ?>';

					// init var
					$iterations = array();

					/*
					 * The name of this iteration may contain characters that need to be escaped if you
					 * want to use them as a literal string in a regex match.
					 */
					$iterationPattern = str_replace(array('.', '[', ']', "'"), array('\.', '\[', '\]', "\'"), $iteration);

					// match current iteration
					preg_match_all('/\{iteration:'. $iterationPattern .'\}.*\{\/iteration:'. $iterationPattern .'\}/ismU', $content, $iterations);

					// loop mathes
					foreach($iterations as $block)
					{
						// build new content
						$newContent = str_replace($search, $replace, $block);

						// replace in original content
						$content = str_replace($block, $newContent, $content);
					}
				}
			}
		}

		return $content;
	}


	/**
	 * Parse the forms.
	 *
	 * @return	string				The updated content, containing the parsed form tags.
	 * @param	string $content		The content that may contain the form tags.
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
					// init vars
					$search = array();
					$replace = array();

					// start & close tag
					$search = array('{form:'. $name .'}', '{/form:'. $name .'}');
					$replace[0] = '<form action="<?php echo $this->forms[\''. $name .'\']->getAction(); ?>" method="<?php echo $this->forms[\''. $name .'\']->getMethod(); ?>"<?php echo $this->forms[\''. $name .'\']->getParametersHTML(); ?>>' ."\n<div>\n";
					$replace[0] .= $this->forms[$name]->getField('form')->parse();

					// form tokens were used
					if($this->forms[$name]->getUseToken()) $replace[0] .= "\n". '<input type="hidden" name="form_token" id="<?php echo $this->forms[\''. $name .'\']->getField(\'form_token\')->getAttribute(\'id\'); ?>" value="<?php echo $this->forms[\''. $name .'\']->getField(\'form_token\')->getValue(); ?>" />';

					// close form & replace it
					$replace[1] = "\n</div>\n</form>";
					$content = str_replace($search, $replace, $content);
				}
			}
		}

		return $content;
	}


	/**
	 * Parse the include tags.
	 *
	 * @return	string				The updated content, containing the parsed include tags.
	 * @param	string $content		The content that may contain the include tags.
	 */
	private function parseIncludes($content)
	{
		// regex pattern
		$pattern = '/\{include:file=\'([a-z0-9\-_\.:\{\$\}\/]+)\'\}/is';

		// find matches
		if(preg_match_all($pattern, $content, $matches))
		{
			// loop matches
			foreach($matches[1] as $match)
			{
				// file
				$file = $this->getVariableString($match);

				// search string
				$search = '{include:file=\''. $match .'\'}';

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
	 * Parse the iterations (recursively).
	 *
	 * @return	string				The updated content, containing the parsed iteration tags.
	 * @param	string $content		The content that my contain the iteration tags.
	 */
	private function parseIterations($content)
	{
		// fetch iterations
		$pattern = '/\{iteration:(([a-z09\'\[\]])+(\.([a-z0-9\'\[\]])+)?)\}/is';

		// find matches
		if(preg_match_all($pattern, $content, $matches))
		{
			// init var
			$iterations = array();

			// loop matches
			foreach($matches[1] as $match)
			{
				if(!in_array($match, $iterations)) $iterations[] = $match;
			}

			// has iterations
			if(count($iterations) != 0)
			{
				// loop iterations
				foreach($iterations as $iteration)
				{
					// check iteration syntax
					if($this->isCorrectSyntax($iteration, 'iteration'))
					{
						// parse cycle tag
						$content = $this->parseCycle($content, $iteration);

						// init vars
						$search = array();
						$replace = array();

						// search
						$search[0] = '{iteration:'. $iteration .'}';
						$search[1] = '{/iteration:'. $iteration .'}';

						// convert multiple dots to a single one
						$name = preg_replace('/\.+/', '.', $iteration);
						$name = trim($name, '.');

						// explode using the dots
						$chunks = explode('.', $name);

						// number of chunks
						$numChunks = count($chunks);

						// define variable
						$variable = $this->parseVariable($name);

						// internal variable
						$internalVariable = SpoonFilter::toCamelCase(str_replace(array('[', ']', "'", '_'), ' ', $chunks[$numChunks - 1]), ' ', true, SPOON_CHARSET);

						// replace
						$replace[0] = '<?php $'. $internalVariable ."I = 1; ?>\n";
						$replace[0] .= '<?php $'. $internalVariable .'Count = count('. $variable ."); ?>\n";
						$replace[0] .= '<?php foreach((array) '. $variable .' as $'. $internalVariable ."): ?>\n";
						$replace[0] .= "<?php
						if(!isset(\$". $internalVariable ."['first']) && \$". $internalVariable ."I == 1) \$". $internalVariable ."['first'] = true;
						if(!isset(\$". $internalVariable ."['last']) && \$". $internalVariable ."I == \$". $internalVariable ."Count) \$". $internalVariable ."['last'] = true;
						if(isset(\$". $internalVariable ."['formElements']) && is_array(\$". $internalVariable ."['formElements']))
						{
							foreach(\$". $internalVariable ."['formElements'] as \$name => \$object)
							{
								\$". $internalVariable ."[\$name] = \$object->parse();
								\$". $internalVariable ."[\$name .'Error'] = (method_exists(\$object, 'getErrors') && \$object->getErrors() != '') ? '<span class=\"formError\">'. \$object->getErrors() .'</span>' : '';
							}
						}
						?>";
						$replace[1] = '<?php $'. $internalVariable ."I++; ?>\n";
						$replace[1] .= '<?php endforeach; ?>';

						// replace
						$content = str_replace($search, $replace, $content);
					}
				}
			}
		}

		return $content;
	}


	/**
	 * Parse the options in the given content & scope.
	 *
	 * @return	string				The updated content, containing the parsed option tags.
	 * @param	string $content		The content that may contain the option tags.
	 */
	private function parseOptions($content)
	{
		// regex pattern
		$pattern = '/\{option:((\!)?[a-z0-9\-_\[\]\']+(\.([a-z0-9\-_\[\]\'])+)?)}.*?\{\/option:\\1\}/is';

		// init vars
		$options = array();

		// keep finding those options!
		while(1)
		{
			// find matches
			if(preg_match_all($pattern, $content, $matches))
			{
				// init var
				$correctOptions = false;

				// loop matches
				foreach($matches[1] as $match)
				{
					// correct syntax
					if($this->isCorrectSyntax($match, 'option'))
					{
						// redefine match
						$match = str_replace('!', '', $match);

						// fetch variable
						$variable = $this->parseVariable($match);

						// already matched
						if(in_array($match, $options)) continue;

						// init vars
						$search = array();
						$replace = array();

						// not yet used
						$options[] = $match;

						// search for
						$search[] = '{option:'. $match .'}';
						$search[] = '{/option:'. $match .'}';

						// inverse option
						$search[] = '{option:!'. $match .'}';
						$search[] = '{/option:!'. $match .'}';

						// replace with
						$replace[] = '<?php if(isset('. $variable .') && count('. $variable .') != 0 && '. $variable .' != \'\' && '. $variable .' !== false): ?>';
						$replace[] = '<?php endif; ?>';

						// inverse option
						$replace[] = '<?php if(!isset('. $variable .') || count('. $variable .') == 0 || '. $variable .' == \'\' || '. $variable .' === false): ?>';
						$replace[] = '<?php endif; ?>';

						// go replace
						$content = str_replace($search, $replace, $content);

						// reset vars
						unset($search);
						unset($replace);

						// at least one correct option
						$correctOptions = true;
					}
				}

				// no correct options were found
				if(!$correctOptions) break;
			}

			// no matches
			else break;
		}

		return $content;
	}


	/**
	 * Parse the template to a file.
	 *
	 * @return	void
	 */
	public function parseToFile()
	{
		SpoonFile::setContent($this->compileDirectory .'/'. $this->getCompileName($this->template), $this->getContent());
	}


	/**
	 * Parse a single variable.
	 *
	 * @return	string				The variable as PHP code.
	 * @param	string $variable	The variable that needs to be converted to php code.
	 */
	private function parseVariable($variable)
	{
		// strip '{$' and '}'
		$variable = ltrim($variable, '{$');
		$variable = rtrim($variable, '}');

		// fetch modifiers
		$var = explode('|', $variable);

		// base variable
		$variable = '';

		// explode using the dots
		$varChunks = explode('.', $var[0]);

		// number of chunks
		$numChunks = count($varChunks);

		// more than 2 chunks is NOT allowed
		if($numChunks > 2) return '\'{$'. implode('|', $var) .'}\'';

		// 2 chunks
		elseif($numChunks == 2)
		{
			// contains [
			if(strpos($varChunks[1],'[') !== false)
			{
				// get rid of ]
				$varChunks[1] = str_replace(']', '', $varChunks[1]);

				// create chunks
				$bracketChunks = explode('[', $varChunks[1]);

				// add first part
				$variable = '$'. $varChunks[0];

				// loop all chunks
				for($i = 0; $i < count($bracketChunks); $i++)
				{
					// explicitly add single quotes for the first element
					if($i == 0) $variable .= '[\''. $bracketChunks[$i] .'\']';

					// everything after first as is provided in the template
					else $variable .= '['. $bracketChunks[$i] .']';
				}
			}

			// no square bracketes used
			else $variable = '$'. $varChunks[0] .'[\''. $varChunks[1] .'\']';
		}

		// 1 chunk
		else
		{
			// contains [
			if(strpos($varChunks[0],'[') !== false)
			{
				// get rid of ]
				$varChunks[0] = str_replace(']', '', $varChunks[0]);

				// create chunks
				$bracketChunks = explode('[', $varChunks[0]);

				// add first part
				$variable = '$this->variables[\''. $bracketChunks[0] .'\']';

				// loop all chunks
				for($i = 1; $i < count($bracketChunks); $i++)
				{
					// add this chunk (as provided in the template)
					$variable .= '['. $bracketChunks[$i] .']';
				}
			}

			// no square brackets used
			else $variable = '$this->variables[\''. $var[0] .'\']';
		}

		// has modifiers ?
		if(isset($var[1]))
		{
			// loop modifiers
			foreach($var as $i => $modifier)
			{
				// skip first record
				if($i == 0) continue;

				// modifier + parameters
				$modifierChunks = explode(':', $modifier);

				// modifier doesn't exist
				if(!isset($this->modifiers[$modifierChunks[0]])) throw new SpoonTemplateException('The modifier ('. $modifierChunks[0] .') does not exist.');

				// add call
				else
				{
					// method call
					if(is_array($this->modifiers[$modifierChunks[0]])) $variable = implode('::', $this->modifiers[$modifierChunks[0]]) .'('. $variable;

					// function call
					else $variable = $this->modifiers[$modifierChunks[0]] .'('. $variable;
				}

				// has arguments
				if(count($modifierChunks) > 1)
				{
					// init vars
					$inParameter = false;
					$parameters = mb_substr($modifier, strlen($modifierChunks[0]), mb_strlen($modifier, SPOON_CHARSET), SPOON_CHARSET);

					// loop every character
					for($i = 0; $i < mb_strlen($parameters, SPOON_CHARSET); $i++)
					{
						// fetch character
						$string = mb_substr($parameters, $i, 1, SPOON_CHARSET);

						// single quote in parameter, indicating the end for this parameter
						if($string == "'" && $inParameter) $inParameter = false;

						// single quotes, indicating the start of a new parameter
						elseif($string == "'" && !$inParameter) $inParameter = true;

						// semicolon outside parameter
						elseif($string == ':' && !$inParameter) $string = ', ';

						// add character
						$variable .= $string;
					}
				}

				// add close tag
				$variable .= ')';
			}
		}

		return $variable;
	}


	/**
	 * Parse all the variables in this string.
	 *
	 * @return	string				The updated content, containing the parsed variables.
	 * @param	string $content		The content that may contain variables.
	 */
	private function parseVariables($content)
	{
		// regex pattern
		$pattern = '/\{\$([a-z0-9_\'\[\]])+(\.([a-z0-9_\'\[\]])+)?(\|[a-z0-9\-_]+(:[\']?[a-z0-9\-_\s\$\[\]:]+[\']?)*)*\}/i';

		// temp variables
		$variables = array();

		/*
		 * We willen een lijstje bijhouden van alle variabelen die wel gematched zijn, maar niet correct zijn.
		 * Van zodra dit de enige variabelen zijn die nog overschieten, dang aan we de while loop breken.
		 */

		// we want to keep parsing vars until none can be found.
		while(1)
		{
			// find matches
			if(preg_match_all($pattern, $content, $matches))
			{
				// init var
				$correctVariables = false;

				// loop matches
				foreach($matches[0] as $match)
				{
					// variable doesn't already exist
					if(array_search($match, $variables, true) === false)
					{
						// syntax check this match
						if($this->isCorrectSyntax($match, 'variable'))
						{
							// unique key
							$key = md5($match);

							// add parsed variable
							$variables[$key] = $this->parseVariable($match);

							// replace in content
							$content = str_replace($match, '[$'. $key .']', $content);

							// note that at least 1 good variable was found
							$correctVariables = true;
						}
					}
				}

				if(!$correctVariables) break;
			}

			// break the loop, no matches were found
			else break;
		}

		/**
		 * Every variable needs to be searched & replaced one by one,
		 * since only then the nesting process works as intended.
		 */
		foreach($variables as $key => $value)
		{
			// loop each element except this one
			foreach($variables as $keyIndex => $valueContent)
			{
				// skip myself
				if($key == $keyIndex) continue;

				// replace myself in the other var
				$variables[$keyIndex] = str_replace('[$'. $key .']', $variables[$key], $variables[$keyIndex]);
			}
		}

		/**
		 * Now loop these vars again, but this time parse them in the
		 * content we're actually working with.
		 */
		foreach($variables as $key => $value)
		{
			$content = str_replace('[$'. $key .']', '<?php echo '. $value .'; ?>', $content);
		}

		return $content;
	}


	/**
	 * Set the cache directory.
	 *
	 * @return	void
	 * @param	string $path	The location of the cache directory to store cached template blocks.
	 */
	public function setCacheDirectory($path)
	{
		$this->cacheDirectory = (string) $path;
	}


	/**
	 * Set the compile directory.
	 *
	 * @return	void
	 * @param	string $path	The location of the compile directory to store compiled templates in.
	 */
	public function setCompileDirectory($path)
	{
		$this->compileDirectory = (string) $path;
	}


	/**
	 * If enabled, recompiles a template even if it has already been compiled.
	 *
	 * @return	void
	 * @param	bool[optional] $on	Should this template be recompiled every time it's loaded.
	 */
	public function setForceCompile($on = true)
	{
		$this->forceCompile = (bool) $on;
	}


	/**
	 * Sets the forms.
	 *
	 * @return	void
	 * @param	array $forms	An array of forms that need to be included in this template.
	 */
	public function setForms(array $forms)
	{
		$this->forms = $forms;
	}


	/**
	 * Strips php code from the content.
	 *
	 * @return	string				The updated content, no longer containing php code.
	 * @param	string $content		The content that may contain php code.
	 */
	private function stripCode($content)
	{
		return $content = preg_replace('/\<\?(php)?(.*)\?\>/siU', '', $content);
	}


	/**
	 * Strip comments from the output.
	 *
	 * @return	string				The updated content, no longer containing template comments.
	 * @param	string $content		The content that may contain template comments.
	 */
	private function stripComments($content)
	{
		return $content = preg_replace('/\{\*(.+?)\*\}/s', '', $content);
	}
}

?>