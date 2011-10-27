<?php

/**
 * This class will create a mail a friend form
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author		Jelmer Snoeck <jelmer.snoeck@netlash.com>
 * @since		2.6.10
 */
class FrontendMailToFriend
{
	/**
	 * The mail to friend form
	 *
	 * @var	FrontendForm
	 */
	private $frm;


	/**
	 * The page url
	 *
	 * @var	string
	 */
	private $pageUrl;


	/**
	 * The template
	 *
	 * @var	SpoonTemplate
	 */
	private $tpl;


	/**
	 * The constructor
	 *
	 * @return	void
	 * @param	SpoonTemplate $template			The template to parse the form with.
	 */
	public function __construct(SpoonTemplate $template)
	{
		// assign the template
		$this->tpl = $template;

		// create the form
		$this->createForm();
	}


	/**
	 * This function will create the mail to a friend form
	 *
	 * @return	void
	 */
	protected function createForm()
	{
		// create the form
		$this->frm = new FrontendForm('mailtofriend');

		// add the elements
		$this->frm->addText('friend_name');
		$this->frm->addText('friend_email');
		$this->frm->addText('own_name');
		$this->frm->addText('own_email');
		$this->frm->addTextarea('mail_message');
	}


	/**
	 * This will fetch the pageUrl
	 *
	 * @return	string
	 */
	public function getPageUrl()
	{
		// if there is nog page url set yet, build it
		if(!isset($this->pageUrl)) $this->setPageUrl();

		// return the page url
		return $this->pageUrl;
	}


	/**
	 * Inserts the data
	 *
	 * @return	void
	 * @param	array $data			The data to insert.
	 */
	public function insert(array $data)
	{
		// set the created on date
		$data['created_on'] = FrontendModel::getUTCDate();

		// insert
		FrontendModel::getDB(true)->insert('mail_to_friend', $data);
	}


	/**
	 * This function creates a new template to parse the form into
	 *
	 * @return	void
	 */
	public function parse()
	{
		// validate the form
		$this->validateForm();

		// parse the form
		$this->frm->parse($this->tpl);
	}


	/**
	 * Set the page url
	 *
	 * @return	void
	 * @param	string[optional] $pageUrl			The page url to set.
	 */
	public function setPageUrl($pageUrl = null)
	{
		// no page url given, set to the default site url
		if($pageUrl == null) $this->pageUrl = SITE_URL;
		// we have a page url given
		else
		{
			// left trim the page url so we don't have multiple slashes
			$pageUrl = ltrim($pageUrl, '/');

			// add the page url to the site url
			$this->pageUrl = SITE_URL . '/' . $pageUrl;
		}
	}


	/**
	 * Validate the form
	 *
	 * @return	void
	 */
	protected function validateForm()
	{
		// is the form submitted?
		if($this->frm->isSubmitted())
		{
			// assign that the form is submitted, this is used for the dialog
			$this->tpl->assign('mailToFriendSubmitted', true);

			// cleanup the fields
			$this->frm->cleanupFields();

			// the validation
			$this->frm->getField('friend_name')->isFilled(FL::err('FieldIsRequired'));
			$this->frm->getField('friend_email')->isEmail(FL::err('EmailIsInvalid'));
			$this->frm->getField('own_name')->isFilled(FL::err('FieldIsRequired'));
			$this->frm->getField('own_email')->isEmail(FL::err('EmailIsInvalid'));

			// no errors?
			if($this->frm->isCorrect())
			{
				// get the values
				$friendName = $this->frm->getField('friend_name')->getValue();
				$friendEmail = $this->frm->getField('friend_email')->getValue();
				$ownName = $this->frm->getField('own_name')->getValue();
				$ownEmail = $this->frm->getField('own_email')->getValue();

				// get the values to the mail
				$mailParameters['message'] = sprintf(FL::msg('MailShareMessage'), $friendName, $ownName, $this->getPageUrl());
				$mailParameters['website'] = SITE_DEFAULT_TITLE;
				$mailParameters['mail_message'] = $this->frm->getField('mail_message')->getValue();

				// add the mail
				FrontendMailer::addEmail(sprintf(FL::lbl('SharedLink'), $ownName), FRONTEND_CORE_PATH . '/layout/templates/mails/mail_to_friend.tpl', $mailParameters, $friendEmail, $friendName, $ownEmail, $ownName);

				// assign the data to an array
				$data['own'] = serialize(array('name' => $ownName, 'email' => $ownEmail));
				$data['friend'] = serialize(array('name' => $friendName, 'email' => $friendEmail));
				$data['language'] = FRONTEND_LANGUAGE;
				$data['message'] = $this->frm->getField('mail_message')->getValue();
				$data['page'] = $this->getPageUrl();

				// insert
				$this->insert($data);

				// assign the success message
				$this->tpl->assign('mailToFriendSend', true);
			}
		}
	}
}
