<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This class will create a mail a friend form
 *
 * @author Jelmer Snoeck <jelmer@netlash.com>
 */
class FrontendMailToFriend
{
	/**
	 * The mail to friend form
	 *
	 * @var FrontendForm
	 */
	private $frm;

	/**
	 * The page url
	 *
	 * @var string
	 */
	private $pageUrl;

	/**
	 * The template
	 *
	 * @var SpoonTemplate
	 */
	private $tpl;

	/**
	 * The constructor
	 *
	 * @param SpoonTemplate $template
	 */
	public function __construct(SpoonTemplate $template)
	{
		$this->tpl = $template;
		$this->createForm();
	}

	/**
	 * This function will create the mail to a friend form
	 */
	protected function createForm()
	{
		$this->frm = new FrontendForm('mailtofriend');
		$this->frm->addText('friend_name');
		$this->frm->addText('friend_email');
		$this->frm->addText('own_name');
		$this->frm->addText('own_email');
		$this->frm->addTextarea('mail_message');
	}

	/**
	 * This will fetch the pageUrl
	 *
	 * @return string
	 */
	public function getPageUrl()
	{
		// if there is nog page url set yet, build it
		if(!isset($this->pageUrl)) $this->setPageUrl();

		return $this->pageUrl;
	}

	/**
	 * Inserts the data
	 *
	 * @param array $data
	 */
	public function insert(array $data)
	{
		$data['created_on'] = FrontendModel::getUTCDate();
		FrontendModel::getDB(true)->insert('mail_to_friend', $data);
	}

	/**
	 * This function creates a new template to parse the form into
	 */
	public function parse()
	{
		$this->validateForm();
		$this->frm->parse($this->tpl);
	}

	/**
	 * Set the page url
	 *
	 * @param string[optional] $pageUrl
	 */
	public function setPageUrl($pageUrl = null)
	{
		if($pageUrl == null) $this->pageUrl = SITE_URL;
		else
		{
			$pageUrl = ltrim($pageUrl, '/');
			$this->pageUrl = SITE_URL . '/' . $pageUrl;
		}
	}

	/**
	 * Validate the form
	 */
	protected function validateForm()
	{
		if($this->frm->isSubmitted())
		{
			$this->tpl->assign('mailToFriendSubmitted', true);
			$this->frm->cleanupFields();

			// the validation
			$this->frm->getField('friend_name')->isFilled(FL::err('FieldIsRequired'));
			$this->frm->getField('friend_email')->isEmail(FL::err('EmailIsInvalid'));
			$this->frm->getField('own_name')->isFilled(FL::err('FieldIsRequired'));
			$this->frm->getField('own_email')->isEmail(FL::err('EmailIsInvalid'));

			if($this->frm->isCorrect())
			{
				$friendName = $this->frm->getField('friend_name')->getValue();
				$friendEmail = $this->frm->getField('friend_email')->getValue();
				$ownName = $this->frm->getField('own_name')->getValue();
				$ownEmail = $this->frm->getField('own_email')->getValue();

				$mailParameters['message'] = sprintf(FL::msg('MailShareMessage'), $friendName, $ownName, $this->getPageUrl());
				$mailParameters['website'] = SITE_DEFAULT_TITLE;
				$mailParameters['mail_message'] = $this->frm->getField('mail_message')->getValue();
				FrontendMailer::addEmail(sprintf(FL::lbl('SharedLink'), $ownName), FRONTEND_CORE_PATH . '/layout/templates/mails/mail_to_friend.tpl', $mailParameters, $friendEmail, $friendName, $ownEmail, $ownName);

				$data['own'] = serialize(array('name' => $ownName, 'email' => $ownEmail));
				$data['friend'] = serialize(array('name' => $friendName, 'email' => $friendEmail));
				$data['language'] = FRONTEND_LANGUAGE;
				$data['message'] = $this->frm->getField('mail_message')->getValue();
				$data['page'] = $this->getPageUrl();
				$this->insert($data);

				// trigger an event so people can do other things with this
				FrontendModel::triggerEvent('core', 'after_mail_to_friend', $data);

				// assign the success message
				$this->tpl->assign('mailToFriendSend', true);
			}
		}
	}
}
