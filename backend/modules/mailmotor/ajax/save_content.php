<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This saves the mailing content
 *
 * @author Dave Lens <dave.lens@netlash.com>
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
	 * @param string $HTML The HTML wherin the parameters will be added.
	 * @return string
	 */
	private function addUTMParameters($HTML)
	{
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
	 */
	public function execute()
	{
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
		if(empty($this->mailing)) $this->output(self::BAD_REQUEST, null, BL::err('MailingDoesNotExist', $this->getModule()));

		// validate other fields
		if($subject == '') $this->output(900, array('element' => 'subject', 'element_error' => BL::err('NoSubject', $this->getModule())), BL::err('FormError'));

		// set full HTML
		$HTML = $this->getEmailContent($this->mailing['template'], $contentHTML, $fullContentHTML);

		// set plain content
		$contentPlain = empty($contentPlain) ? SpoonFilter::stripHTML($HTML) : $contentPlain;

		// add unsubscribe link
		if(mb_strpos($contentPlain, '[unsubscribe]') === false) $contentPlain .= PHP_EOL . '[unsubscribe]';

		// build data
		$item['id'] = $this->mailing['id'];
		$item['subject'] = $subject;
		$item['content_plain'] = $contentPlain;
		$item['content_html'] = $contentHTML;
		$item['data'] = serialize(array('full_content_html' => $HTML));
		$item['edited_on'] = date('Y-m-d H:i:s');

		// update mailing in our database
		BackendMailmotorModel::updateMailing($item);

		/*
			we should insert the draft into campaignmonitor here,
			so we can use sendCampaignPreview in step 4.
		*/
		$item['groups'] = $this->mailing['groups'];
		$item['name'] = $this->mailing['name'];
		$item['from_name'] = $this->mailing['from_name'];
		$item['from_email'] = $this->mailing['from_email'];
		$item['reply_to_email'] = $this->mailing['reply_to_email'];

		try
		{
			BackendMailmotorCMHelper::saveMailingDraft($item);
		}
		catch(Exception $e)
		{
			// check what error we have
			if(strpos($e->getMessage(), 'HTML Content URL Required'))
			{
				$message = BL::err('HTMLContentURLRequired', $this->getModule());
			}
			elseif(strpos($e->getMessage(), 'Payment details required'))
			{
				$error = BL::err('PaymentDetailsRequired', $this->getModule());
				$cmUsername = BackendModel::getModuleSetting($this->getModule(), 'cm_username');
				$message = sprintf($error, $cmUsername);
			}
			elseif(strpos($e->getMessage(), 'Duplicate Campaign Name'))
			{
				$message = BL::err('DuplicateCampaignName', $this->getModule());
			}
			else
			{
				$message = $e->getMessage();
			}

			// stop the script and show our error
			$this->output(902, null, $message);
		}

		// trigger event
		BackendModel::triggerEvent($this->getModule(), 'after_edit_mailing_step3', array('item' => $item));

		// output
		$this->output(self::OK, array('mailing_id' => $mailingId), BL::msg('MailingEdited', $this->getModule()));
	}

	/**
	 * Returns the fully parsed e-mail content
	 *
	 * @param string $template The template to use.
	 * @param string $contentHTML The content.
	 * @param string $fullContentHTML The full content.
	 * @return string
	 */
	private function getEmailContent($template, $contentHTML, $fullContentHTML)
	{
		// require the CSSToInlineStyles class
		require 'external/css_to_inline_styles.php';

		// fetch the template contents for this mailing
		$template = BackendMailmotorModel::getTemplate($this->mailing['language'], $template);

		// template content is empty
		if(!isset($template['content'])) $this->output(self::ERROR, array('mailing_id' => $this->mailing['id'], 'error' => true), BL::err('TemplateDoesNotExist', $this->getModule()));

		// remove TinyMCE
		$fullContentHTML = preg_replace('/<!-- tinymce -->.*?<!-- \/tinymce -->/is', $contentHTML, $fullContentHTML);

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
	 * @param string $tag The tag.
	 * @param string $html The HTML to search in.
	 * @param bool[optional] $strict Use strictmode?
	 * @return array
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

		return $results;
	}
}
