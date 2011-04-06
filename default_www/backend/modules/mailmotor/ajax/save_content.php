<?php

/**
 * This saves the mailing content
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendMailmotorAjaxSaveContent extends BackendBaseAJAXAction
{
	/**
	 * The mailing record
	 *
	 * @var	array
	 */
	private $mailing;


	/**
	 * Adds Google UTM GET Parameters to all anchor links in the mailing
	 *
	 * @return	string
	 * @param	string $HTML	The HTML wherin the parameters will be added.
	 */
	private function addUTMParameters($HTML)
	{
		// init var
		$matches = array();

		// search for all hrefs
		preg_match_all('/href="(.*)"/isU', $HTML, $matches);

		// reserve searhc vars
		$search = array();
		$replace = array();

		// check if we have matches
		if(!isset($matches[1]) || empty($matches[1])) return $HTML;

		// build the google vars query
		$params['utm_source'] = 'mailmotor';
		$params['utm_medium'] = 'email';
		$params['utm_campaign'] = SpoonFilter::urlise($this->mailing['name']);

		// build google vars query
		$googleQuery = http_build_query($params);

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
		return str_replace($search, $replace, $HTML);
	}


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// get parameters
		$mailingId = SpoonFilter::getPostValue('mailing_id', null, '', 'int');
		$subject = SpoonFilter::getPostValue('subject', null, '');
		$contentHTML = urldecode(SpoonFilter::getPostValue('content_html', null, ''));
		$contentPlain = SpoonFilter::getPostValue('content_plain', null, '');
		$fullContentHTML = SpoonFilter::getPostValue('full_content_html', null, '');

		// validate mailing ID
		if($mailingId == '') $this->output(self::BAD_REQUEST, null, 'No mailing ID provided');

		// get mailing record
		$this->mailing = BackendMailmotorModel::getMailing($mailingId);

		// record is empty
		if(empty($this->mailing)) $this->output(self::BAD_REQUEST, null, BL::err('MailingDoesNotExist', 'mailmotor'));

		// validate other fields
		if($subject == '') $this->output(900, array('element' => 'subject', 'element_error' => BL::err('NoSubject', 'mailmotor')), BL::err('FormError'));

		// set full HTML
		$HTML = $this->getEmailContent($this->mailing['template'], $contentHTML, $fullContentHTML);

		// build data
		$item['id'] = $this->mailing['id'];
		$item['subject'] = $subject;
		$item['content_plain'] = empty($contentPlain) ? SpoonFilter::stripHTML($HTML) : $contentPlain;
		$item['content_html'] = $contentHTML;
		$item['data'] = serialize(array('full_content_html' => $HTML));
		$item['edited_on'] = date('Y-m-d H:i:s');

		// update mailing
		BackendMailmotorModel::updateMailing($item);

		// output
		$this->output(self::OK, array('mailing_id' => $mailingId), BL::msg('MailingEdited', 'mailmotor'));
	}


	/**
	 * Returns the fully parsed e-mail content
	 *
	 * @return	string
	 * @param	string $template			The template to use.
	 * @param	string $contentHTML			The content.
	 * @param	string $fullContentHTML		The full content.
	 */
	private function getEmailContent($template, $contentHTML, $fullContentHTML)
	{
		// require the CSSToInlineStyles class
		require 'external/css_to_inline_styles.php';

		// fetch the template contents for this mailing
		$template = BackendMailmotorModel::getTemplate($this->mailing['language'], $template);

		// template content is empty
		if(!isset($template['content'])) $this->output(self::ERROR, array('mailing_id' => $this->mailing['id'], 'error' => true), BL::err('TemplateDoesNotExist', 'mailmotor'));

		// remove TinyMCE
		$fullContentHTML = preg_replace('/<!-- tinymce  -->.*?<!-- \/tinymce  -->/is', $contentHTML, $fullContentHTML);

		// replace bracketed entities with their proper counterpart
		$fullContentHTML = preg_replace('/\[ent=(.*?)]/', '&${1};', $fullContentHTML);

		// add Google UTM parameters to all anchors
		$fullContentHTML = $this->addUTMParameters($fullContentHTML);

		// search values
		$search[] = '{$siteURL}';
		$search[] = '&quot;';
		$search[] = 'src="/';

		// replace values
		$replace[] = SITE_URL;
		$replace[] = '"';
		$replace[] = 'src="' . SITE_URL . '/';

		// replace some variables
		$fullContentHTML = str_replace($search, $replace, $fullContentHTML);

		// set CSS object
		$css = new CSSToInlineStyles($fullContentHTML, $template['css']);
		$fullContentHTML = $css->convert();

		// return the content
		return $fullContentHTML;
	}


	/**
	 * Returns the text between 2 tags
	 *
	 * @return	array
	 * @param	string $tag					The tag.
	 * @param	string $html				The HTML to search in.
	 * @param	bool[optional] $strict		Use strictmode?
	 */
	private function getTextBetweenTags($tag, $html, $strict = false)
	{
		// new dom document
		$dom = new domDocument;

		// load HTML
		($strict == true) ? $dom->loadXML($html) : $dom->loadHTML($html);

		// discard whitespace
		$dom->preserveWhiteSpace = false;

		// the array with results
		$results = array();

		// fetch the tag by name
		$content = $dom->getElementsByTagname($tag);

		// loop the content
		foreach($content as $item)
		{
			// add node value to results
			$results[] = $item->nodeValue;
		}

		// return the results
		return $results;
	}
}

?>