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
 * @author		Matthias Mullie <matthias@spoon-library.com>
 * @author		Tijs Verkoyen <tijs@spoon-library.com>
 * @since		1.3.0
 */
class SpoonTemplateCompiler
{
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
	 * Working content
	 *
	 * @var	string
	 */
	protected $content;


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
	 * List of used iterations
	 *
	 * @var	array
	 */
	protected $iterations = array();


	/**
	 * Counter of used iterations (each iteration will get a unique number)
	 *
	 * @var	int
	 */
	protected $iterationsCounter;


	/**
	 * Cached list of the modifiers
	 *
	 * @var	array
	 */
	protected $modifiers = array();


	/**
	 * Is the content already parsed
	 *
	 * @var	bool
	 */
	protected $parsed = false;


	/**
	 * Template file
	 *
	 * @var	string
	 */
	protected $template;


	/**
	 * List of compiler-interpreted variables
	 *
	 * @var	array
	 */
	protected $templateVariables = array();


	/**
	 * List of variables
	 *
	 * @var	array
	 */
	protected $variables = array();


	/**
	 * Class constructor.
	 *
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
	protected function getCompileName($template)
	{
		return md5(realpath($template)) . '_' . basename($template) . '.php';
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
	 * Parse the template.
	 */
	protected function parse()
	{
		// not yet parsed
		if(!$this->parsed)
		{
			// while developing, you might want to know about the undefined indexes
			$errorReporting = (SPOON_DEBUG) ? 'E_ALL | E_STRICT' : 'E_WARNING';
			$displayErrors = (SPOON_DEBUG) ? 'On' : 'Off';

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

			// prepare iterations
			$this->content = $this->prepareIterations($this->content);

			// parse iterations
			$this->content = $this->parseIterations($this->content);

			// parse variables
			$this->content = $this->parseVariables($this->content);

			// parse options
			$this->content = $this->parseOptions($this->content);

			// includes
			$this->content = $this->parseIncludes($this->content);

			// parse cache tags
			$this->content = $this->parseCache($this->content);

			// parse forms
			$this->content = $this->parseForms($this->content);

			// replace variables
			$this->content = $this->replaceVariables($this->content);

			// add error_reporting setting
			$this->content = '<?php error_reporting(' . $errorReporting . '); ini_set(\'display_errors\', \'' . $displayErrors . '\'); ?>' . "\n" . $this->content;

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
	protected function parseCache($content)
	{
		// regex pattern
		$pattern = '/\{cache:(.*?)}.*?\{\/cache:\\1\}/is';

		// find matches
		if(preg_match_all($pattern, $content, $matches))
		{
			// loop matches
			foreach($matches[1] as $match)
			{
				// search
				$search[0] = '{cache:' . $match . '}';
				$search[1] = '{/cache:' . $match . '}';

				// replace
				$replace[0] = '<?php
				ob_start();
				?>' . $match . '<?php
				$cache = eval(\'return \\\'\' . str_replace(\'\\\'\', \'\\\\\\\'\', ob_get_clean()) .\'\\\';\');
				if(!$this->isCached($cache))
				{
					ob_start();
				?>';
				$replace[1] = '<?php
					SpoonFile::setContent($this->cacheDirectory .\'/\' . $cache .\'_cache.tpl\', ob_get_clean());
				}
				require $this->cacheDirectory .\'/\' . $cache .\'_cache.tpl\';
				?>';

				// replace it
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
	 * @param	string $iteration	The iteration.
	 */
	protected function parseCycle($content, $iteration)
	{
		// regex pattern
		$pattern = '/\{cycle((:(.*?))+)\}/is';

		// find matches
		if(preg_match_all($pattern, $content, $matches, PREG_SET_ORDER))
		{
			// loop matches
			foreach($matches as $i => $match)
			{
				// cycles pattern
				$pattern = '/:("[^"]*?"|\'[^\']*?\'|[^:]*)/';

				// has cycles
				if(preg_match_all($pattern, $match[1], $arguments))
				{
					// init search & replace: reset arguments array
					$search = $match[0];
					$replace = '<?php
					$arguments = array();';

					// loop arguments
					foreach($arguments[1] as &$argument)
					{
						// inside a string
						if(in_array(substr($argument, 0, 1), array('\'', '"')))
						{
							// strip quotes
							$argument = substr($argument, 1, -1);
						}

						// let's do this argument per argument: we need our eval to take care of the esacping (parsed variables result may contain single quotes)
						$replace .= '
						ob_start();
						?>' . $argument . '<?php
						$arguments[] = eval(\'return \\\'\' . str_replace(\'\\\'\', \'\\\\\\\'\', ob_get_clean()) .\'\\\';\');';
					}

					// finish replace: create cycle
					$replace .= '
					echo $this->cycle(' . $iteration . '[\'i\'], $arguments);
					?>' . "\n";

					// replace it
					$content = str_replace($search, $replace, $content);
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
	protected function parseForms($content)
	{
		// regex pattern
		$pattern = '/\{form:([a-z0-9_]+?)\}?/siU';

		// find matches
		if(preg_match_all($pattern, $content, $matches))
		{
			// loop matches
			foreach($matches[1] as $name)
			{
				// init vars
				$search = array();
				$replace = array();

				// start & close tag
				$search = array('{form:' . $name . '}', '{/form:' . $name . '}');

				// using UTF-8 as charset
				if(SPOON_CHARSET == 'utf-8')
				{
					$replace[0] = '<?php
					if(isset($this->forms[\'' . $name . '\']))
					{
						?><form accept-charset="UTF-8" action="<?php echo $this->forms[\'' . $name . '\']->getAction(); ?>" method="<?php echo $this->forms[\'' . $name . '\']->getMethod(); ?>"<?php echo $this->forms[\'' . $name . '\']->getParametersHTML(); ?>>
						<?php echo $this->forms[\'' . $name . '\']->getField(\'form\')->parse();
						if($this->forms[\'' . $name . '\']->getUseToken())
						{
							?><input type="hidden" name="form_token" id="<?php echo $this->forms[\'' . $name . '\']->getField(\'form_token\')->getAttribute(\'id\'); ?>" value="<?php echo $this->forms[\'' . $name . '\']->getField(\'form_token\')->getValue(); ?>" />
						<?php } ?>';
				}

				// no UTF-8
				else
				{
					$replace[0] = '<?php
					if(isset($this->forms[\'' . $name . '\']))
					{
						?><form action="<?php echo $this->forms[\'' . $name . '\']->getAction(); ?>" method="<?php echo $this->forms[\'' . $name . '\']->getMethod(); ?>"<?php echo $this->forms[\'' . $name . '\']->getParametersHTML(); ?>>
						<?php echo $this->forms[\'' . $name . '\']->getField(\'form\')->parse();
						if($this->forms[\'' . $name . '\']->getUseToken())
						{
							?><input type="hidden" name="form_token" id="<?php echo $this->forms[\'' . $name . '\']->getField(\'form_token\')->getAttribute(\'id\'); ?>" value="<?php echo $this->forms[\'' . $name . '\']->getField(\'form_token\')->getValue(); ?>" />
						<?php } ?>';
				}

				$replace[1] = '</form>
				<?php } ?>';

				$content = str_replace($search, $replace, $content);
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
	protected function parseIncludes($content)
	{
		// regex pattern
		// no unified restriction can be done on the allowed characters, that differs from one OS to another (see http://www.comentum.com/File-Systems-HFS-FAT-UFS.html)
		$pattern = '/\{include:(("[^"]*?"|\'[^\']*?\')|[^:]*?)\}/i';

		// find matches
		if(preg_match_all($pattern, $content, $matches, PREG_SET_ORDER))
		{
			// loop matches
			foreach($matches as $match)
			{
				// search string
				$search = $match[0];

				// inside a string
				if(in_array(substr($match[1], 0, 1), array('\'', '"')))
				{
					// strip quotes
					$match[1] = substr($match[1], 1, -1);
				}

				// replace string
				$replace = '<?php
				ob_start();
				?>' . $match[1] . '<?php
				$include = eval(\'return \\\'\' . str_replace(\'\\\'\', \'\\\\\\\'\', ob_get_clean()) .\'\\\';\');
				if($this->getForceCompile()) $this->compile(\'' . dirname(realpath($this->template)) . '\', $include);
				$return = @include $this->getCompileDirectory() .\'/\' . $this->getCompileName($include, \'' . dirname(realpath($this->template)) . '\');
				if($return === false && $this->compile(\'' . dirname(realpath($this->template)) . '\', $include))
				{
					$return = @include $this->getCompileDirectory() .\'/\' . $this->getCompileName($include, \'' . dirname(realpath($this->template)) . '\');
				}' . "\n";
				if(SPOON_DEBUG) $replace .= 'if($return === false)
				{
					?>' . $match[0] . '<?php
				}' . "\n";
				$replace .= '?>';

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
	 * @param	string $content		The content that may contain the iteration tags.
	 */
	protected function parseIterations($content)
	{
		// fetch iterations
		$pattern = '/(\{iteration_([0-9]+):([a-z0-9_]*)((\.[a-z0-9_]*)*)((-\>[a-z0-9_]*((\.[a-z0-9_]*)*))?)\})(.*?)(\{\/iteration_\\2:\\3\\4\\6\})/is';

		// find matches
		if(preg_match_all($pattern, $content, $matches, PREG_SET_ORDER))
		{
			// loop iterations
			foreach($matches as $match)
			{
				// base variable names
				$iteration = '$this->iterations[\'' . $this->getCompileName($this->template) . '_' . $match[2] . '\']';
				$internalVariable = '${\'' . $match[3] . '\'}';

				// variable within iteration
				if($match[6] != '')
				{
					// base
					$variable = '${\'' . $match[3] . '\'}';

					// add separate chunks
					foreach(explode('.', ltrim($match[4] . str_replace('->', '.', $match[6]), '.')) as $chunk)
					{
						// make sure it's a valid chunk
						if(!$chunk) continue;

						// append pieces
						$variable .= "['" . $chunk . "']";
						$iteration .= "['" . $chunk . "']";
						$internalVariable .= "['" . $chunk . "']";
					}
				}

				// regular variable
				else
				{
					// base
					$variable = '$this->variables[\'' . $match[3] . '\']';

					// add separate chunks
					foreach(explode('.', ltrim($match[4], '.')) as $chunk)
					{
						// make sure it's a valid chunk
						if(!$chunk) continue;

						// append pieces
						$variable .= "['" . $chunk . "']";
						$iteration .= "['" . $chunk . "']";
						$internalVariable .= "['" . $chunk . "']";
					}
				}

				// iteration content
				$innerContent = $match[10];

				// alter inner iterations, options & variables to indicate where the iteration starting point is
				$search = $match[3] . $match[4] . str_replace('->', '.', $match[6]) . '.';
				$replace = $match[3] . $match[4] . str_replace('->', '.', $match[6]) . '->';
				$innerContent = preg_replace('/\{(.*?)' . preg_quote($search, '/') . '(.*?)\}/is', '{\\1' . $replace . '\\2}', $innerContent);

				// parse inner iterations (recursively)
				$innerContent = $this->parseIterations($innerContent);

				// parse inner variables (they have to be parsed first in case a variable exists inside a cycle)
				$innerContent = $this->parseVariables($innerContent);

				// parse cycle tags
				$innerContent = $this->parseCycle($innerContent, $iteration);

				// start iteration
				$templateContent = '<?php';
				if(SPOON_DEBUG)
				{
					$templateContent .= '
					if(!isset(' . $variable . '))
					{
						?>{iteration:' . $match[3] . $match[4] . $match[6] . '}<?php
						' . $variable . ' = array();
						' . $iteration . '[\'fail\'] = true;
					}';
				}
				$templateContent .= '
				if(isset(' . $internalVariable . ')) ' . $iteration . '[\'old\'] = ' . $internalVariable . ';
				' . $iteration . '[\'iteration\'] = ' . $variable . ';
				' . $iteration . '[\'i\'] = 1;
				' . $iteration . '[\'count\'] = count(' . $iteration . '[\'iteration\']);
				foreach((array) ' . $iteration . '[\'iteration\'] as ' . $internalVariable . ')
				{
					if(!isset(' . $internalVariable . '[\'first\']) && ' . $iteration . '[\'i\'] == 1) ' . $internalVariable . '[\'first\'] = true;
					if(!isset(' . $internalVariable . '[\'last\']) && ' . $iteration . '[\'i\'] == ' . $iteration . '[\'count\']) ' . $internalVariable . '[\'last\'] = true;
					if(isset(' . $internalVariable . '[\'formElements\']) && is_array(' . $internalVariable . '[\'formElements\']))
					{
						foreach(' . $internalVariable . '[\'formElements\'] as $name => $object)
						{
							' . $internalVariable . '[$name] = $object->parse();
							' . $internalVariable . '[$name .\'Error\'] = (is_callable(array($object, \'getErrors\')) && $object->getErrors() != \'\') ? \'<span class="formError">\' . $object->getErrors() .\'</span>\' : \'\';
						}
					} ?>';

				// append inner content
				$templateContent .= $innerContent;

				// close iteration
				$templateContent .= '<?php
					' . $iteration . '[\'i\']++;
				}';
				if(SPOON_DEBUG)
				{
					$templateContent .= '
					if(isset(' . $iteration . '[\'fail\']) && ' . $iteration . '[\'fail\'] == true)
					{
						?>{/iteration:' . $match[3] . $match[4] . $match[6] . '}<?php
					}';
				}
				$templateContent .= '
				if(isset(' . $iteration . '[\'old\'])) ' . $internalVariable . ' = ' . $iteration . '[\'old\'];
				else unset(' . $iteration . ');
				?>';

				// replace!
				$content = str_replace($match[0], $templateContent, $content);
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
	protected function parseOptions($content)
	{
		// regex pattern
		$pattern = '/\{option:(\!?)([a-z0-9_]*)((\.[a-z0-9_]*)*)(-\>[a-z0-9_]*((\.[a-z0-9_]*)*))?}.*?\{\/option:\\1\\2\\3\\5?\}/is';

		// init vars
		$options = array();

		// keep finding those options!
		while(1)
		{
			// find matches
			if(preg_match_all($pattern, $content, $matches, PREG_SET_ORDER))
			{
				// loop matches
				foreach($matches as $match)
				{
					// variable within iteration
					if(isset($match[5]) && $match[5] != '')
					{
						// base
						$variable = '${\'' . $match[2] . '\'}';

						// add separate chunks
						foreach(explode('.', ltrim($match[3] . str_replace('->', '.', $match[5]), '.')) as $chunk)
						{
							$variable .= "['" . $chunk . "']";
						}
					}

					// regular variable
					else
					{
						// base
						$variable = '$this->variables';

						// add separate chunks
						foreach(explode('.', $match[2] . $match[3]) as $chunk)
						{
							$variable .= "['" . $chunk . "']";
						}
					}

					// init vars
					$search = array();
					$replace = array();

					// not yet used
					$options[] = $match;

					// set option
					$option = $match[2] . $match[3] . (isset($match[5]) ? $match[5] : '');

					// search for
					$search[] = '{option:' . $option . '}';
					$search[] = '{/option:' . $option . '}';

					// inverse option
					$search[] = '{option:!' . $option . '}';
					$search[] = '{/option:!' . $option . '}';

					// replace with
					$replace[] = '<?php
					if(isset(' . $variable . ') && count(' . $variable . ') != 0 && ' . $variable . ' != \'\' && ' . $variable . ' !== false)
					{
						?>';
					$replace[] = '<?php } ?>';

					// inverse option
					$replace[] = '<?php if(!isset(' . $variable . ') || count(' . $variable . ') == 0 || ' . $variable . ' == \'\' || ' . $variable . ' === false): ?>';
					$replace[] = '<?php endif; ?>';

					// go replace
					$content = str_replace($search, $replace, $content);

					// reset vars
					unset($search);
					unset($replace);
				}
			}

			// no matches
			else break;
		}

		return $content;
	}


	/**
	 * Parse the template to a file.
	 */
	public function parseToFile()
	{
		SpoonFile::setContent($this->compileDirectory . '/' . $this->getCompileName($this->template), $this->getContent());
	}


	/**
	 * Parse all the variables in this string.
	 *
	 * @return	string				The updated content, containing the parsed variables.
	 * @param	string $content		The content that may contain variables.
	 */
	protected function parseVariables($content)
	{
		// we want to keep parsing vars until none can be found
		while(1)
		{
			// regex pattern
			$pattern = '/\{\$([a-z0-9_]*)((\.[a-z0-9_]*)*)(-\>[a-z0-9_]*((\.[a-z0-9_]*)*))?((\|[a-z_][a-z0-9_]*(:.*?)*)*)\}/i';

			// find matches
			if(preg_match_all($pattern, $content, $matches, PREG_SET_ORDER))
			{
				// loop matches
				foreach($matches as $match)
				{
					// check if no variable has been used inside our variable (as argument for a modifier)
					$test = substr($match[0], 1);
					$testContent = $this->parseVariables($test);

					// inner variable found
					if($test != $testContent)
					{
						// variable has been parsed, so change the content to reflect this
						$content = str_replace($match[0], substr($match[0], 0, 1) . $testContent, $content);

						// next variable please
						continue;
					}

					// no inner variable found
					else
					{
						// variable doesn't already exist
						if(array_search($match[0], $this->templateVariables, true) === false)
						{
							// unique key
							$varKey = md5($match[0]);

							// variable within iteration
							if(isset($match[4]) && $match[4] != '')
							{
								// base
								$variable = '${\'' . $match[1] . '\'}';

								// add separate chunks
								foreach(explode('.', ltrim($match[2] . str_replace('->', '.', $match[4]), '.')) as $chunk)
								{
									$variable .= "['" . $chunk . "']";
								}
							}

							// regular variable
							else
							{
								// base
								$variable = '$this->variables';

								// add separate chunks
								foreach(explode('.', $match[1] . $match[2]) as $chunk)
								{
									$variable .= "['" . $chunk . "']";
								}
							}

							// save PHP code
							$PHP = $variable;

							// has modifiers
							if(isset($match[7]) && $match[7] != '')
							{
								// modifier pattern
								$pattern = '/\|([a-z_][a-z0-9_]*)((:("[^"]*?"|\'[^\']*?\'|[^:|]*))*)/i';

								// has match
								if(preg_match_all($pattern, $match[7], $modifiers))
								{
									// loop modifiers
									foreach($modifiers[1] as $key => $modifier)
									{
										// modifier doesn't exist
										if(!isset($this->modifiers[$modifier])) throw new SpoonTemplateException('The modifier "' . $modifier . '" does not exist.');

										// add call
										else
										{
											// method call
											if(is_array($this->modifiers[$modifier])) $PHP = implode('::', $this->modifiers[$modifier]) . '(' . $PHP;

											// function call
											else $PHP = $this->modifiers[$modifier] . '(' . $PHP;
										}

										// has arguments
										if($modifiers[2][$key] != '')
										{
											// arguments pattern (don't just explode on ':', it might be used inside a string argument)
											$pattern = '/:("[^"]*?"|\'[^\']*?\'|[^:|]*)/';

											// has arguments
											if(preg_match_all($pattern, $modifiers[2][$key], $arguments))
											{
												// loop arguments
												foreach($arguments[1] as $argument)
												{
													// string argument?
													if(in_array(substr($argument, 0, 1), array('\'', '"')))
													{
														// in compiled code: single quotes! (and escape single quotes in the content!)
														$argument = '\'' . str_replace('\'', '\\\'', substr($argument, 1, -1)) . '\'';

														// make sure that variables inside string arguments are correctly parsed
														$argument = preg_replace('/\[\$.*?\]/', '\' . \\0 .\'', $argument);
													}

													// add argument
													$PHP .= ', ' . $argument;
												}
											}
										}

										// add close tag
										$PHP .= ')';
									}
								}
							}

							/**
							 * Variables may have other variables used as parameters in modifiers
							 * so loop all currently known variables to replace them.
							 * It does not matter that we do not yet know all variables, we only
							 * need those inside this particular variable, and those will
							 * certainly already be parsed because we parse our variables outwards.
							 */
							// temporary variable which is a list of 'variables to check before parsing'
							$variables = array($variable);

							// loop all known template variables
							foreach($this->templateVariables as $key => $value)
							{
								// replace variables
								$PHP = str_replace('[$' . $key . ']', $value['content'], $PHP);

								// debug enabled
								if(SPOON_DEBUG)
								{
									// check if this variable is found
									if(strpos($match[0], '[$' . $key . ']') !== false)
									{
										// add variable name to list of 'variables to check before parsing'
										$variables = array_merge($variables, $value['variables']);
									}
								}
							}

							// PHP conversion for this template variable
							$this->templateVariables[$varKey]['content'] = $PHP;

							// debug enabled: variable not assigned = revert to template code
							if(SPOON_DEBUG)
							{
								// holds checks to see if this variable can be parsed (along with the variables that may be used inside it)
								$exists = array();

								// loop variables
								foreach((array) $variables as $variable)
								{
									// get array containing variable
									$array = preg_replace('/(\[\'[a-z_][a-z0-9_]*\'\])$/i', '', $variable);

									// get variable name
									preg_match('/\[\'([a-z_][a-z0-9_]*)\'\]$/i', $variable, $variable);
									$variable = $variable[1];

									// container array is index of higher array
									if(preg_match('/\[\'[a-z_][a-z0-9_]*\'\]/i', $array)) $exists[] = 'isset(' . $array . ')';
									$exists[] = 'array_key_exists(\'' . $variable . '\', (array) ' . $array . ')';
								}

								// save info for error fallback
								$this->templateVariables[$varKey]['if'] = implode(' && ', $exists);
								$this->templateVariables[$varKey]['variables'] = $variables;
								$this->templateVariables[$varKey]['template'] = $match[0];
							}
						}

						// replace in content
						$content = str_replace($match[0], '[$' . $varKey . ']', $content);
					}
				}
			}

			// break the loop, no matches were found
			else break;
		}

		return $content;
	}


	/**
	 * Prepare iterations (recursively).
	 * Every single iteration (even iterations that are the same) has to be unique: iterations that are
	 * the same can be nested in each other and cycle tags need to know exactly which iteration is cycling.
	 *
	 * @return	string				The updated content, containing reworked (unique) iteration tags.
	 * @param	string $content		The content that may contain the iteration tags.
	 */
	protected function prepareIterations($content)
	{
		// fetch iterations - for iterations that are present more than one, only the last one will
		// be matched, previous ones will be matches later on another run through our while loop
		$pattern = '/(\{iteration:([a-z][a-z0-9_]*(\.[a-z_][a-z0-9_]*)*)\})(?!.*?\{iteration:\\2\})(.*?)(\{\/iteration:\\2\})/is';

		// we want to keep parsing iterations until none can be found
		while(1)
		{
			// replace iteration names to ensure that they're unique
			$content = preg_replace_callback($pattern, array($this, 'prepareIterationsCallback'), $content, -1, $count);

			// break the loop, no matches were found
			if(!$count) break;
		}

		return $content;
	}


	/**
	 * Prepare iterations: callback function.
	 *
	 * @return	string				The updated iteration, containing a reworked (unique) iteration tag.
	 * @param	string $match		The regex-match for an iteration.
	 */
	protected function prepareIterationsCallback($match)
	{
		// increment iterations counter
		$this->iterationsCounter++;

		// return the modified iteration name
		return '{iteration_' . $this->iterationsCounter . ':' . $match[2] . '}' . $match[4] . '{/iteration_' . $this->iterationsCounter . ':' . $match[2] . '}';
	}


	/**
	 * Now loop the vars again, but this time parse them in the content we're actually working with.
	 *
	 * @return	string				The updated content, containing reworked fully parsed variables.
	 * @param	string $content		The content that may contain the partially parsed variables.
	 */
	protected function replaceVariables($content)
	{
		// keep finding those variables!
		while(1)
		{
			// counter to check if we have actually replaced something
			$replaced = 0;

			// loop all variables
			foreach($this->templateVariables as $key => $value)
			{
				// replace variables in the content
				if(SPOON_DEBUG) $content = str_replace('[$' . $key . ']', '<?php if(' . $value['if'] . ') { echo ' . $value['content'] . '; } else { ?>' . $value['template'] . '<?php } ?>', $content, $count);
				else $content = str_replace('[$' . $key . ']', '<?php echo ' . $value['content'] . '; ?>', $content, $count);

				// add amount of replacements to our counter
				$replaced += $count;
			}

			// break the loop, no matches were found
			if($replaced == 0) break;
		}

		return $content;
	}


	/**
	 * Set the cache directory.
	 *
	 * @param	string $path	The location of the cache directory to store cached template blocks.
	 */
	public function setCacheDirectory($path)
	{
		$this->cacheDirectory = (string) $path;
	}


	/**
	 * Set the compile directory.
	 *
	 * @param	string $path	The location of the compile directory to store compiled templates in.
	 */
	public function setCompileDirectory($path)
	{
		$this->compileDirectory = (string) $path;
	}


	/**
	 * If enabled, recompiles a template even if it has already been compiled.
	 *
	 * @param	bool[optional] $on	Should this template be recompiled every time it's loaded.
	 */
	public function setForceCompile($on = true)
	{
		$this->forceCompile = (bool) $on;
	}


	/**
	 * Sets the forms.
	 *
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
	protected function stripCode($content)
	{
		return $content = preg_replace('/\<\?(php)?(.*)\?\>/si', '', $content);
	}


	/**
	 * Strip comments from the output.
	 *
	 * @return	string				The updated content, no longer containing template comments.
	 * @param	string $content		The content that may contain template comments.
	 */
	protected function stripComments($content)
	{
		// we want to keep stripping comments until none can be found
		do
		{
			// strip comments from output
			$content = preg_replace('/\{\*(?!.*?\{\*).*?\*\}/s', '', $content, -1, $count);
		}
		while($count > 0);

		return $content;
	}
}
