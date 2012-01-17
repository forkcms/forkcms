<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This builds the mailing's body
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class MailingBodyBuilder
{
	/**
	 * @var bool
	 */
	private $plaintext = false;

	/**
	 * @var string
	 */
	private $html, $css, $templateHtml;

	/**
	 * @var array The UTM campaign parameters used in Google
	 */
	private $utmParameters = array(
		'campaign' => '',
		'source' => '',
		'medium' => '',
	);

	public function __construct()
	{
		require 'external/css_to_inline_styles.php';
	}

	/**
	 * Builds and returns the generated mailing body HTML or plaintext.
	 *
	 * @param array $replacements An array of key/value pairs, where key is the string to replace with the value.
	 * @return string The generated mailing body.
	 */
	public function buildBody(array $replacements = null)
	{
		$templateHtml = $this->getTemplateContent();
		$editorHtml = $this->getEditorContent();
		$css = $this->getCSS();

		if(empty($templateHtml))
		{
			throw new Exception('No valid template HTML was set.');
		}

		if(empty($editorHtml))
		{
			throw new Exception('No valid editor content HTML was set.');
		}

		// we replace the editor tags with the content the user gave into the editor in the CMS
		$body = preg_replace('/<!-- editor -->.*?<!-- \/editor -->/is', $editorHtml, $templateHtml);

		// we have to do this so we have entities in our body instead
		$body = mb_convert_encoding($body, 'HTML-ENTITIES', 'UTF-8');

		// add Google UTM parameters to all anchors
		$body = $this->processUTMParameters($body);
		$body = $this->processReplacements($body, $replacements);
		$body = $this->processCSS($body);

		// we parse the template CSS into the template, and re-build our body
		$body = $this->processCSS($body);

		// we will return plaintext if the user asked for it.
		if($this->isPlaintext())
		{
			$body = SpoonFilter::stripHTML($body);
		}

		return $body;
	}

	/**
	 * Disable plaintext.
	 */
	public function disablePlaintext()
	{
		$this->plaintext = false;
	}

	/**
	 * Enable plaintext.
	 */
	public function enablePlaintext()
	{
		$this->plaintext = true;
	}

	/**
	 * Retrieve the CSS.
	 */
	public function getCSS()
	{
		return $this->css;
	}

	/**
	 * Retrieve the editor content.
	 */
	public function getEditorContent()
	{
		return $this->html;
	}

	/**
	 * Retrieve template content.
	 */
	public function getTemplateContent()
	{
		return $this->templateHtml;
	}

	/**
	 * Get the Google UTM GET parameters.
	 *
	 * @return array
	 */
	public function getUTMParameters()
	{
		return $this->utmParameters;
	}

	/**
	 * Should we return a plaintext mailing or not.
	 *
	 * @return bool
	 */
	public function isPlaintext()
	{
		return $this->plaintext;
	}

	/**
	 * This method parses the template css in the template by using Tijs Verkoyen's CSSToInlineStyles parser.
	 *
	 * @param string $body
	 * @return string
	 */
	protected function processCSS($body)
	{
		$css = $this->getCSS();

		// stop here if no template CSS was set
		if(empty($css))
		{
			return $body;
		}

		$css = new CSSToInlineStyles($body, $css);
		return $css->convert();
	}

	/**
	 * Process the replacements.
	 *
	 * @param string $body
	 * @param array $replacements An array of key/value pairs, where key is the string to replace with the value.
	 * @return string
	 */
	protected function processReplacements($body, $replacements)
	{
		if(empty($replacements)) return;

		$search = array_keys($replacements);
		$replace = array_values($replacements);
		$body = str_replace($search, $replace, $body);

		return $body;
	}

	/**
	 * Adds Google UTM GET parameters to all anchor links in the mailing
	 *
	 * @param string $body
	 * @return string The given HTML content, with the UTM-vars assigned.
	 */
	private function processUTMParameters($body)
	{
		// search for all hrefs
		preg_match_all('/href="(.*)"/isU', $body, $matches);

		// check if we have matches
		if(!isset($matches[1]) || empty($matches[1])) return $body;

		// build the google vars query
		$utm = $this->getUTMParameters();
		$params['utm_source'] = $utm['source'];
		$params['utm_medium'] = $utm['medium'];
		$params['utm_campaign'] = $utm['campaign'];

		// build google vars query
		$googleQuery = http_build_query($params);

		// reserve search vars
		$search = array();
		$replace = array();

		// loop the matches
		foreach($matches[1] as $match)
		{
			// ignore #
			if(strpos($match, '#') > -1) continue;

			// add results to search/replace stack
			$search[] = 'href="' . $match . '"';
			$replace[] = 'href="' . $match . ((strpos($match, '?') !== false) ? '&' : '?') . $googleQuery . '"';
		}

		// replace the content HTML with the replace values
		return str_replace($search, $replace, $body);
	}

	/**
	 * Set the css.
	 *
	 * @param string $css The template CSS to parse through the body.
	 */
	public function setCSS($css)
	{
		$this->css = $css;
	}

	/**
	 * Set the editor content.
	 *
	 * @param string $html What the user entered in the editor in the CMS.
	 */
	public function setEditorContent($html)
	{
		$this->html = $html;
	}

	/**
	 * Set the template content.
	 *
	 * @param string $html The HTML of the template the user selected in the CMS.
	 */
	public function setTemplateContent($html)
	{
		$this->templateHtml = $html;
	}

	/**
	 * Set the Google UTM GET parameters.
	 *
	 * @param string $html What the user entered in the editor in the CMS.
	 * @param string[optional] $source
	 * @param string[optional] $medium
	 */
	public function setUTMParameters($campaign, $source = 'mailmotor', $medium = 'email')
	{
		$this->utmParameters['campaign'] = $campaign;
		$this->utmParameters['source'] = $source;
		$this->utmParameters['medium'] = $medium;
	}
}