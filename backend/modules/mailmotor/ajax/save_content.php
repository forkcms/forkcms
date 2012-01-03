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

		// validate mailing ID
		if($mailingId == '')
		{
			$this->output(self::BAD_REQUEST, null, 'No mailing ID provided');
		}

		// get mailing record
		$this->mailing = BackendMailmotorModel::getMailing($mailingId);

		// record is empty
		if(empty($this->mailing))
		{
			$this->output(
				self::BAD_REQUEST,
				null,
				BL::err('MailingDoesNotExist', $this->getModule())
			);
		}

		// validate other fields
		if($subject == '')
		{
			$this->output(
				900,
				array(
					'element' => 'subject',
					'element_error' => BL::err('NoSubject', $this->getModule())
				),
				BL::err('FormError')
			);
		}

		// set plain content
		$contentPlain = empty($contentPlain) ? SpoonFilter::stripHTML($contentHTML) : $contentPlain;

		// add unsubscribe link
		if(mb_strpos($contentPlain, '[unsubscribe]') === false)
		{
			$contentPlain .= PHP_EOL . '[unsubscribe]';
		}

		// build data
		$item['id'] = $this->mailing['id'];
		$item['subject'] = $subject;
		$item['content_plain'] = $contentPlain;
		$item['content_html'] = $contentHTML;
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
			// CM did not receive a valid URL
			if(strpos($e->getMessage(), 'HTML Content URL Required'))
			{
				$message = BL::err('HTMLContentURLRequired', $this->getModule());
			}

			// no payment details were set for the CM client yet
			elseif(strpos($e->getMessage(), 'Payment details required'))
			{
				$error = BL::err('PaymentDetailsRequired', $this->getModule());
				$cmUsername = BackendModel::getModuleSetting($this->getModule(), 'cm_username');
				$message = sprintf($error, $cmUsername);
			}

			// the campaign name already exists in CM
			elseif(strpos($e->getMessage(), 'Duplicate Campaign Name'))
			{
				$message = BL::err('DuplicateCampaignName', $this->getModule());
			}

			// we received an unknown error
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
