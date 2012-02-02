<?php

/**
 * MinifyCSS class
 *
 * This source file can be used to minify CSS files.
 *
 * The class is documented in the file itself. If you find any bugs help me out and report them. Reporting can be done by sending an email to minify@mullie.eu.
 * If you report a bug, make sure you give me enough information (include your code).
 *
 * License
 * Copyright (c) 2012, Matthias Mullie. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 * 3. The name of the author may not be used to endorse or promote products derived from this software without specific prior written permission.
 *
 * This software is provided by the author "as is" and any express or implied warranties, including, but not limited to, the implied warranties of merchantability and fitness for a particular purpose are disclaimed. In no event shall the author be liable for any direct, indirect, incidental, special, exemplary, or consequential damages (including, but not limited to, procurement of substitute goods or services; loss of use, data, or profits; or business interruption) however caused and on any theory of liability, whether in contract, strict liability, or tort (including negligence or otherwise) arising in any way out of the use of this software, even if advised of the possibility of such damage.
 *
 * @author Matthias Mullie <minify@mullie.eu>
 * @author Tijs Verkoyen <php-css-to-inline-styles@verkoyen.eu>
 * @version 1.0.0
 *
 * @copyright Copyright (c) 2012, Matthias Mullie. All rights reserved.
 * @license BSD License
 */
class MinifyCSS extends Minify
{
	/**
	 * Combine CSS from import statements.
	 * @import's will be loaded and their content merged into the original file, to save HTTP requests.
	 *
	 * @param string $source The file to combine imports for.
	 * @param string[optional] $path The path the data should be written to.
	 * @return string
	 */
	public function combineImports($source, $path = false)
	{
		// little "hack" for internal use
		$content = @func_get_arg(2);

		// load the content
		if($content === false) $content = $this->load($source);

		// validate data
		if($content == $source) throw new MinifyException('The data for "' . $source . '" could not be loaded, please make sure the path is correct.');

		// the regex to match import statements
		$importRegex = '/

			# import statement
			@import

			# whitespace
			\s+

				# (optional) open url()
				(?<url>url\()?

					# open path enclosure
					(?<quotes>["\'])

						# fetch path
						(?<path>

							# do not fetch data uris
							(?!(
								["\']?
								data:
							))

							.+
						)

					# close path enclosure
					(?P=quotes)

				# (optional) close url()
				(?(url)\))

				# (optional) trailing whitespace
				\s*

				# (optional) media statement(s)
				(?<media>.*?)

				# (optional) trailing whitespace
				\s*

			# (optional) closing semi-colon
			;?

			/ix';

		// find all relative imports in css (for now we don't support imports with media, and imports should use url(xxx))
		if(preg_match_all($importRegex, $content, $matches, PREG_SET_ORDER))
		{
			$search = array();
			$replace = array();

			// loop the matches
			foreach($matches as $i => $match)
			{
				// get the path for the file that will be imported
				$importPath = dirname($source) . '/' . $match['path'];

				// only replace the import with the content if we can grab the content of the file
				if(@file_exists($importPath) && is_file($importPath))
				{
					// grab content
					$importContent = @file_get_contents($importPath);

					// fix relative paths
					$importContent = $this->move($importPath, $source, $importContent);

					// check if this is only valid for certain media
					if($match['media']) $importContent = '@media '. $match['media'] . '{' . "\n" . $importContent . "\n" . '}';

					// add to replacement array
					$search[] = $match[0];
					$replace[] = $importContent;
				}
			}

			// replace the import statements
			$content = str_replace($search, $replace, $content);

			// ge recursive (if imports have occured)
			if($search) $content = $this->combineImports($source, false, $content);
		}

		// save to path
		if($path !== false && @func_get_arg(2) === false) $this->save($content, $path);

		return $content;
	}

	/**
	 * Convert relative paths based upon 1 path to another.
	 *
	 * E.g.
	 * ../images/img.gif based upon /home/forkcms/frontend/core/layout/css, should become
	 * ../../core/layout/images/img.gif based upon /home/forkcms/frontend/cache/minified_css
	 *
	 * @param string $path The relative path that needs to be converted.
	 * @param string $from The original base path.
	 * @param string $to The new base path.
	 * @return string The new relative path.
	 */
	protected function convertRelativePath($path, $from, $to)
	{
		// make sure we're dealing with directories
		$from = @is_file($from) ? dirname($from) : $from;
		$to = @is_file($to) ? dirname($to) : $to;

		// deal with different operating systems' directory structure
		$path = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $path), '/');
		$from = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $from), '/');
		$to = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $to), '/');

		// if we're not dealing with a relative path, just return absolute
		if(strpos($path, '/') === 0) return $path;

		/*
		 * Example:
		 * $path = ../images/img.gif
		 * $from = /home/forkcms/frontend/cache/compiled_templates/../../core/layout/css
		 * $to = /home/forkcms/frontend/cache/minified_css
		 */

		// normalize paths
		do
		{
			$path = preg_replace('/[^\.\.\/]+?\/\.\.\//', '', $path, -1, $count);
		}
		while($count);
		do
		{
			$from = preg_replace('/[^\/]+?\/\.\.\//', '', $from, -1, $count);
		}
		while($count);
		do
		{
			$to = preg_replace('/[^\/]+?\/\.\.\//', '', $to, -1, $count);
		}
		while($count);

		/*
		 * At this point:
		 * $path = ../images/img.gif
		 * $from = /home/forkcms/frontend/core/layout/css
		 * $to = /home/forkcms/frontend/cache/minified_css
		 */

		// resolve path the relative url is based upon
		do
		{
			$path = preg_replace('/^\.\.\//', '', $path, 1, $count);

			// for every level up, adjust dirname
			if($count) $from = dirname($from);
		}
		while($count);

		/*
		 * At this point:
		 * $path = images/img.gif
		 * $from = /home/forkcms/frontend/core/layout
		 * $to = /home/forkcms/frontend/cache/minified_css
		 */

		// compare paths & strip identical parents
		$from = explode('/', $from);
		$to = explode('/', $to);
		foreach($from as $i => $chunk)
		{
			if($from[$i] == $to[$i]) unset($from[$i], $to[$i]);
			else break;
		}

		/*
		 * At this point:
		 * $path = images/img.gif
		 * $from = array('core', 'layout')
		 * $to = array('cache', 'minified_css')
		 */

		// add .. for every directory that needs to be traversed for new path
		$new = str_repeat('../', count($to));

		/*
		 * At this point:
		 * $path = images/img.gif
		 * $from = array('core', 'layout')
		 * $to = *no longer matters*
		 * $new = ../../
		 */

		// add path, relative from this point, to traverse to image
		$new .= implode('/', $from);

		// if $from contained no elements, we still have a redundant trailing slash
		if(empty($from)) $new = rtrim($new, '/');

		/*
		 * At this point:
		 * $path = images/img.gif
		 * $from = *no longer matters*
		 * $to = *no longer matters*
		 * $new = ../../core/layout
		 */

		// add remaining path
		$new .= '/' . $path;

		/*
		 * At this point:
		 * $path = *no longer matters*
		 * $from = *no longer matters*
		 * $to = *no longer matters*
		 * $new = ../../core/layout/images/img.gif
		 */

		// Tada!
		return $new;
	}

	/**
	 * Import images into the CSS, base64-ized.
	 * @url(image.jpg) images will be loaded and their content merged into the original file, to save HTTP requests.
	 *
	 * @param string $source The file to import images for.
	 * @param string[optional] $path The path the data should be written to.
	 * @return string
	 */
	public function importImages($source, $path = false)
	{
		// little "hack" for internal use
		$content = @func_get_arg(2);

		// load the content
		if($content === false) $content = $this->load($source);

		// validate data
		if($content == $source) throw new MinifyException('The data for "' . $source . '" could not be loaded, please make sure the path is correct.');

		if(preg_match_all('/url\((["\']?)((?!["\']?data:).*?\.(gif|png|jpg|jpeg|tiff|svg))\\1\)/i', $content, $matches, PREG_SET_ORDER))
		{
			$search = array();
			$replace = array();

			// loop the matches
			foreach($matches as $match)
			{
				// get the path for the file that will be imported
				$path = $match[2];
				$path = dirname($source) . '/' . $path;

				// only replace the import with the content if we can grab the content of the file
				if(@file_exists($path) && is_file($path))
				{
					// grab content
					$importContent = @file_get_contents($path);

					// base-64-ize
					$importContent = base64_encode($importContent);

					// build replacement
					$search[] = $match[0];
					$replace[] = 'url(data:image/' . $match[3] . ';base64,' . $importContent  .')';
				}
			}

			// replace the import statements
			$content = str_replace($search, $replace, $content);
		}

		// save to path
		if($path !== false && @func_get_arg(2) === false) $this->save($content, $path);

		return $content;
	}

	/**
	 * Minify the data.
	 * Perform CSS optimizations.
	 *
	 * @param string[optional] $path The path the data should be written to.
	 * @return string The minified data.
	 */
	public function minify($path = false, $stripComments = true, $stripWhitespace = true, $shortenHex = true, $combineImports = true, $importImages = true)
	{
		$content = '';

		// loop files
		foreach($this->data as $source => $css)
		{
			// if we're saving to a new path, we'll have to fix the relative paths
			if($path !== false && $source !== 0) $css = $this->move($source, $path, $css);

			// combine css
			$content .= $css;
		}

		if($combineImports) $content = $this->combineImports($path, false, $content);
		if($stripComments) $content = $this->stripComments($content);
		if($stripWhitespace) $content = $this->stripWhitespace($content);
		if($shortenHex) $content = $this->shortenHex($content);
		if($importImages) $content = $this->importImages($path, false, $content);

		// save to path
		if($path !== false) $this->save($content, $path);

		return $content;
	}

	/**
	 * Moving a css file should update all relative urls.
	 * Relative references (e.g. ../images/image.gif) in a certain css file, will have to be updated when a file is
	 * being saved at another location (e.g. ../../images/image.gif, if the new CSS file is 1 folder deeper)
	 *
	 * @param string $source The file to update relative urls for.
	 * @param string $path The path the data will be written to.
	 * @return string
	 */
	public function move($source, $path)
	{
		// little "hack" for internal use
		$content = @func_get_arg(2);

		// load the content
		if($content === false) $content = $this->load($source);

		// validate data
		if($content == $source) throw new MinifyException('The data for "' . $source . '" could not be loaded, please make sure the path is correct.');

		// regex to match paths
		$pathsRegex = '/

		# enable possiblity of giving multiple subpatterns same name
		(?J)

		# @import "xxx" or @import url(xxx)

			# import statement
			@import

			# whitespace
			\s+

				# (optional) open url()
				(?<url>url\()?

					# open path enclosure
					(?<quotes>["\'])

						# fetch path
						(?<path>

							# do not fetch data uris
							(?!(
								["\']?
								data:
							))

							.+
						)

					# close path enclosure
					(?P=quotes)

				# (optional) close url()
				(?(url)\))
		|

		# url(xxx)

			# open url()
			url\(

				# open path enclosure
				(?<quotes>["\'])?

					# fetch path
					(?<path>

						# do not fetch data uris
						(?!(
							["\']?
							data:
						))

						.+
					)

				# close path enclosure
				(?(quotes)(?P=quotes))

			# close url()
			\)

		/ix';

		// find all relative urls in css
		if(preg_match_all($pathsRegex, $content, $matches, PREG_SET_ORDER))
		{
			$search = array();
			$replace = array();

			// loop all urls
			foreach($matches as $match)
			{
				// determine if it's a url() or an @import match
				$type = (strpos($match[0], '@import') === 0 ? 'import' : 'url');

				// fix relative url
				$url = $this->convertRelativePath($match['path'], dirname($source), dirname($path));

				// build replacement
				$search[] = $match[0];
				if($type == 'url') $replace[] = 'url(' . $url . ')';
				elseif($type == 'import') $replace[] = '@import "' . $url . '"';
			}

			// replace urls
			$content = str_replace($search, $replace, $content);
		}

		// save to path (not for internal use!)
		if(@func_get_arg(2) === false) $this->save($content, $path);

		return $content;
	}

	/**
	 * Shorthand hex color codes.
	 * #FF0000 -> #F00
	 *
	 * @param string $content The file/content to shorten the hex color codes for.
	 * @param string[optional] $path The path the data should be written to.
	 * @return string
	 */
	public function shortenHex($content, $path = false)
	{
		// load the content
		$content = $this->load($content);

		// shorthand hex color codes
		$content = preg_replace('/#([0-9a-z])\\1([0-9a-z])\\2([0-9a-z])\\3/i', '#$1$2$3', $content);

		// save to path
		if($path !== false) $this->save($content, $path);

		return $content;
	}

	/**
	 * Strip comments.
	 *
	 * @param string $content The file/content to strip the comments for.
	 * @param string[optional] $path The path the data should be written to.
	 * @return string
	 */
	public function stripComments($content, $path = false)
	{
		// load the content
		$content = $this->load($content);

		// strip comments
		$content = preg_replace('/\/\*(.*?)\*\//is', '', $content);

		// save to path
		if($path !== false) $this->save($content, $path);

		return $content;
	}

	/**
	 * Strip whitespace.
	 *
	 * @param string $content The file/content to strip the whitespace for.
	 * @param string[optional] $path The path the data should be written to.
	 * @return string
	 */
	public function stripWhitespace($content, $path = false)
	{
		// load the content
		$content = $this->load($content);

		// semicolon/space before closing bracket > replace by bracket
		$content = preg_replace('/;?\s*}/', '}', $content);

		// bracket, colon, semicolon or comma preceeded or followed by whitespace > remove space
		$content = preg_replace('/\s*([\{:;,])\s*/', '$1', $content);

		// preceeding/trailing whitespace > remove
		$content = preg_replace('/^\s*|\s*$/m', '', $content);

		// newlines > remove
		$content = preg_replace('/\n/', '', $content);

		// save to path
		if($path !== false) $this->save($content, $path);

		return $content;
	}
}

/**
 * MinifyJS class
 *
 * This source file can be used to minify Javascript files.
 *
 * The class is documented in the file itself. If you find any bugs help me out and report them. Reporting can be done by sending an email to minify@mullie.eu.
 * If you report a bug, make sure you give me enough information (include your code).
 *
 * License
 * Copyright (c) 2012, Matthias Mullie. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 * 3. The name of the author may not be used to endorse or promote products derived from this software without specific prior written permission.
 *
 * This software is provided by the author "as is" and any express or implied warranties, including, but not limited to, the implied warranties of merchantability and fitness for a particular purpose are disclaimed. In no event shall the author be liable for any direct, indirect, incidental, special, exemplary, or consequential damages (including, but not limited to, procurement of substitute goods or services; loss of use, data, or profits; or business interruption) however caused and on any theory of liability, whether in contract, strict liability, or tort (including negligence or otherwise) arising in any way out of the use of this software, even if advised of the possibility of such damage.
 *
 * @author Matthias Mullie <minify@mullie.eu>
 * @author Tijs Verkoyen <php-css-to-inline-styles@verkoyen.eu>
 * @version 1.0.0
 *
 * @copyright Copyright (c) 2012, Matthias Mullie. All rights reserved.
 * @license BSD License
 */
class MinifyJS extends Minify
{
	/**
	 * Extract comments & strings from source code (and replace them with a placeholder)
	 * This fancy parsing is neccessary because comments can contain string demarcators and vice versa, and both can
	 * contain content that is very similar to the rest of the code.
	 *
	 * @param string $content The file/content to extract comments & strings for.
	 * @return array An array containing the (manipulated) content, the strings & the comments.
	 */
	protected function extract($content)
	{
		// load the content
		$content = $this->load($content);

		// initialize array that will contain all strings found in the code
		$strings = array();
		$comments = array();

		// loop all characters
		for($i = 0; $i < strlen($content); $i++)
		{
			$character = $content[$i];

			switch($content[$i])
			{
				// string demarcation: ' or "
				case '\'':
				case '"':
					$stringOpener = $character;
					$string = $character;

					// process through content until we find the end of the string
					for($j = $i + 1; $j < strlen($content); $j++)
					{
						$character = $content[$j];
						$previousCharacter = isset($content[$j - 1]) ? $content[$j - 1] : '';

						/*
						 * Find end of string:
						 * - string started with double quotes ends in double quotes, likewise for single quotes.
						 * - unterminated string ends at newline (bad code), unless newline is escaped (though nobody
						 *   knows this.)
						 */
						if(($stringOpener == $character && $previousCharacter != '\\') || (in_array($character, array("\r", "\n")) && $previousCharacter != '\\'))
						{
							// save string
							$replacement = '[MINIFY-STRING-' . count($strings) . ']';
							$strings[$replacement] = substr($content, $i, $j - $i + 1);

							// replace string by stub
							$content = substr_replace($content, $replacement, $i, $j - $i + 1);

							// reset pointer to the end of this string
							$i += strlen($replacement);

							break;
						}
					}
					break;

				// comment demarcation: // or /*
				case '/':
					$commentOpener = $character . (isset($content[$i + 1]) ? $content[$i + 1] : '');

					/*
					 * Both comment opening tags are 2 characters, so grab the next character and verify we're really
					 * opening a comment here.
					 */
					if(in_array($commentOpener, array('//', '/*')))
					{
						// process through content until we find the end of the comment
						for($j = $i + 1; $j < strlen($content); $j++)
						{
							$character = $content[$j];
							$previousCharacter = isset($content[$j - 1]) ? $content[$j - 1] : '';

							/*
							 * Find end of comment:
							 * - // single line comments end at newline.
							 * - /* multiline comments and at their respective closing tag, which I can't use here or
							 *   it'd end this very comment.
							 */
							if(($commentOpener == '//' && in_array($character, array("\r", "\n"))) || ($commentOpener == '/*' && $previousCharacter . $character == '*/'))
							{
								// save comment
								$replacement = '[MINIFY-COMMENT-' . count($comments) . ']';
								$comments[$replacement] = substr($content, $i, $j - $i + 1);

								// replace comment by stub
								$content = substr_replace($content, $replacement, $i, $j - $i + 1);

								// reset pointer to the end of this string
								$i += strlen($replacement);

								break;
							}
						}
					}
					break;
			}
		}

		return array($content, $strings, $comments);
	}

	/**
	 * Minify the data.
	 * Perform JS optimizations.
	 *
	 * @param string[optional] $path The path the data should be written to.
	 * @return string The minified data.
	 */
	public function minify($path = false, $stripComments = true, $stripWhitespace = true)
	{
		$content = '';

		// loop files
		foreach($this->data as $source => $js)
		{
			// combine js
			$content .= $js;
		}

		// extract comments & strings from content
		list($content, $strings, $comments) = $this->extract($content);

		// minify
		if($stripComments) $content = $this->stripComments($content, false, $comments);

		// strip whitespace
		if($stripWhitespace) $content = $this->stripWhitespace($content, false, $strings, $comments);

		// reset strings
		$content = str_replace(array_keys($strings), array_values($strings), $content);

		// save to path
		if($path !== false) $this->save($content, $path);

		return $content;
	}

	/**
	 * Strip comments from source code.
	 *
	 * @param string $content The file/content to strip the comments for.
	 * @param string[optional] $path The path the data should be written to.
	 * @return string
	 */
	public function stripComments($content, $path = false)
	{
		// little "hack" for internal use
		$comments = @func_get_arg(2);

		// load the content
		$content = $this->load($content);

		// content has not been parsed before, do so now
		if($comments === false)
		{
			// extract strings & comments
			list($content, $strings, $comments) = $this->extract($content);

			// reset strings
			$content = str_replace(array_keys($strings), array_values($strings), $content);
		}

		// strip comments
		$content = str_replace(array_keys($comments), array_fill(0, count($comments), ''), $content);

		// save to path (not for internal use!)
		if(@func_get_arg(2) === false) $this->save($content, $path);

		return $content;
	}

	/**
	 * Strip whitespace.
	 *
	 * @param string $content The file/content to strip the whitespace for.
	 * @param string[optional] $path The path the data should be written to.
	 * @return string
	 */
	public function stripWhitespace($content, $path = false)
	{
		// little "hack" for internal use
		$strings = @func_get_arg(2);
		$comments = @func_get_arg(3);

		// load the content
		$content = $this->load($content);

		// content has not been parsed before, do so now
		if($strings === false || $comments === false)
		{
			// extract strings & comments
			list($content, $strings, $comments) = $this->extract($content);
		}

		// newlines > linefeed
		$content = str_replace(array("\r\n", "\r", "\n"), "\n", $content);

		// empty lines > collapse
		$content = preg_replace('/^[ \t]*|[ \t]*$/m', '', $content);
		$content = preg_replace('/\n+/m', "\n", $content);
		$content = trim($content);

		// redundant whitespace > remove
		$content = preg_replace('/(?<=[{}\[\]\(\)=><&\|;:,\?!\+-])[ \t]*|[ \t]*(?=[{}\[\]\(\)=><&\|;:,\?!\+-])/i', '', $content);
		$content = preg_replace('/[ \t]+/', ' ', $content);

		// redundant semicolons (followed by another semicolon or closing curly bracket) > remove
		$content = preg_replace('/;\s*(?=[;}])/s', '', $content);

		/*
		 * @todo: we could remove all line feeds, but then we have to be certain that all statements are properly
		 * terminated with a semi-colon. So we'd first have to parse the statements to see which require a semi-colon,
		 * add it if it's not present, and then remove the line feeds. The semi-colon just before a closing curly
		 * bracket can then also be omitted.
		 */

		// reset data if this function has not been called upon through internal methods
		if(@func_get_arg(2) === false || @func_get_arg(3) === false)
		{
			// reset strings & comments
			$content = str_replace(array_keys($strings), array_values($strings), $content);
			$content = str_replace(array_keys($comments), array_values($comments), $content);

			// save to path
			if($path !== false) $this->save($content, $path);
		}

		return $content;
	}
}

/**
 * Minify abstract class
 *
 * This source file can be used to write minifiers for multiple file types.
 *
 * The class is documented in the file itself. If you find any bugs help me out and report them. Reporting can be done by sending an email to minify@mullie.eu.
 * If you report a bug, make sure you give me enough information (include your code).
 *
 * License
 * Copyright (c) 2012, Matthias Mullie. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 * 3. The name of the author may not be used to endorse or promote products derived from this software without specific prior written permission.
 *
 * This software is provided by the author "as is" and any express or implied warranties, including, but not limited to, the implied warranties of merchantability and fitness for a particular purpose are disclaimed. In no event shall the author be liable for any direct, indirect, incidental, special, exemplary, or consequential damages (including, but not limited to, procurement of substitute goods or services; loss of use, data, or profits; or business interruption) however caused and on any theory of liability, whether in contract, strict liability, or tort (including negligence or otherwise) arising in any way out of the use of this software, even if advised of the possibility of such damage.
 *
 * @author Matthias Mullie <minify@mullie.eu>
 * @version 1.0.0
 *
 * @copyright Copyright (c) 2012, Matthias Mullie. All rights reserved.
 * @license BSD License
 */
abstract class Minify
{
	/**
	 * The data to be minified
	 *
	 * @var array
	 */
	protected $data = array();

	/**
	 * Init the minify class - optionally, css may be passed along already.
	 *
	 * @param string[optional] $css
	 */
	public function __construct()
	{
		// it's possible to add the css through the constructor as well ;)
		$arguments = func_get_args();
		if(func_num_args()) call_user_func_array(array($this, 'add'), $arguments);
	}

	/**
	 * Add a file or straight-up code to be minified.
	 *
	 * @param string $data
	 */
	public function add($data)
	{
		// this method can be overloaded
		foreach(func_get_args() as $data)
		{
			// redefine var
			$data = (string) $data;

			// load data
			$value = $this->load($data);
			$key = ($data != $value) ? $data : 0;

			// initialize key
			if(!array_key_exists($key, $this->data)) $this->data[$key] = '';

			// store data
			$this->data[$key] .= $value;
		}
	}

	/**
	 * Load data.
	 *
	 * @param string $data Either a path to a file or the content itself.
	 * @return string
	 */
	protected function load($data)
	{
		// check if the data is a file
		if(@file_exists($data) && is_file($data))
		{
			// grab content
			return @file_get_contents($data);
		}

		// no file, just return the data itself
		else return $data;
	}

	/**
	 * Minify the data.
	 *
	 * @param string[optional] $path The path the data should be written to.
	 * @return string The minified data.
	 */
	abstract public function minify($path = null);

	/**
	 * Save to file
	 *
	 * @param string $content The minified data.
	 * @param string $path The path to save the minified data to.
	 */
	public function save($content, $path)
	{
		// create file & open for writing
		if(($handler = @fopen($path, 'w')) === false) throw new MinifyException('The file "' . $path . '" could not be opened. Check if PHP has enough permissions.');

		// write to file
		if(@fwrite($handler, $content) === false) throw new MinifyException('The file "' . $path . '" could not be written to. Check if PHP has enough permissions.');

		// close the file
		@fclose($handler);
	}
}

/**
 * Minify Exception class
 *
 * @author Matthias Mullie <minify@mullie.eu>
 */
class MinifyException extends Exception
{
}