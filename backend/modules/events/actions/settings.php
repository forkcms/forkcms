<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the settings-action, it will display a form to set general settings
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendEventsSettings extends BackendBaseActionEdit
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load form
		$this->loadForm();

		// validates the form
		$this->validateForm();

		// parse
		$this->parse();

		// display the page
		$this->display();
	}

	/**
	 * Loads the settings form
	 */
	private function loadForm()
	{
		// init settings form
		$this->frm = new BackendForm('settings');

		// add fields for pagination
		$this->frm->addDropdown('overview_number_of_items', array_combine(range(1, 30), range(1, 30)), BackendModel::getModuleSetting($this->URL->getModule(), 'overview_num_items', 10));

		// add fields for spam
		$this->frm->addCheckbox('spamfilter_comments', BackendModel::getModuleSetting($this->URL->getModule(), 'spamfilter_comments', false));
		$this->frm->addCheckbox('spamfilter_subscriptions', BackendModel::getModuleSetting($this->URL->getModule(), 'spamfilter_subscriptions', false));

		// no Akismet-key, so we can't enable spam-filter
		if(BackendModel::getModuleSetting('core', 'akismet_key') == '')
		{
			$this->frm->getField('spamfilter_comments')->setAttribute('disabled', 'disabled');
			$this->frm->getField('spamfilter_subscriptions')->setAttribute('disabled', 'disabled');
			$this->tpl->assign('noAkismetKey', true);
		}

		// add fields for comments
		$this->frm->addCheckbox('allow_comments', BackendModel::getModuleSetting($this->URL->getModule(), 'allow_comments', false));
		$this->frm->addCheckbox('moderation_comments', BackendModel::getModuleSetting($this->URL->getModule(), 'moderation_comments', false));

		// add fields for subscriptions
		$this->frm->addCheckbox('allow_subscriptions', BackendModel::getModuleSetting($this->URL->getModule(), 'allow_subscriptions', false));
		$this->frm->addCheckbox('moderation_subscriptions', BackendModel::getModuleSetting($this->URL->getModule(), 'moderation_subscriptions', false));

		// add fields for notifications
		$this->frm->addCheckbox('notify_by_email_on_new_comment_to_moderate', BackendModel::getModuleSetting($this->URL->getModule(), 'notify_by_email_on_new_comment_to_moderate', false));
		$this->frm->addCheckbox('notify_by_email_on_new_comment', BackendModel::getModuleSetting($this->URL->getModule(), 'notify_by_email_on_new_comment', false));
		$this->frm->addCheckbox('notify_by_email_on_new_subscription', BackendModel::getModuleSetting($this->URL->getModule(), 'notify_by_email_on_new_subscription', false));

		// add fields for SEO
		$this->frm->addCheckbox('ping_services', BackendModel::getModuleSetting($this->URL->getModule(), 'ping_services', false));

		// add fields for RSS
		$this->frm->addCheckbox('rss_meta', BackendModel::getModuleSetting($this->URL->getModule(), 'rss_meta_' . BL::getWorkingLanguage(), true));
		$this->frm->addText('rss_title', BackendModel::getModuleSetting($this->URL->getModule(), 'rss_title_' . BL::getWorkingLanguage()));
		$this->frm->addTextarea('rss_description', BackendModel::getModuleSetting($this->URL->getModule(), 'rss_description_' . BL::getWorkingLanguage()));
		$this->frm->addText('feedburner_url', BackendModel::getModuleSetting($this->URL->getModule(), 'feedburner_url_' . BL::getWorkingLanguage()));
	}

	/**
	 * Parse the form
	 */
	protected function parse()
	{
		// call parent
		parent::parse();

		// parse additional variables
		$this->tpl->assign('commentsRSSURL', SITE_URL . BackendModel::getURLForBlock($this->URL->getModule(), 'comments_rss'));
	}

	/**
	 * Validates the settings form
	 */
	private function validateForm()
	{
		// form is submitted
		if($this->frm->isSubmitted())
		{
			// shorten fields
			$feedburnerURL = $this->frm->getField('feedburner_url');

			// validation
			$this->frm->getField('rss_title')->isFilled(BL::err('FieldIsRequired'));

			// feedburner URL is set
			if($feedburnerURL->isFilled())
			{
				// check if http:// is set and add if necessary
				$feedburner = !strstr($feedburnerURL->getValue(), 'http://') ? 'http://' . $feedburnerURL->getValue() : $feedburnerURL->getValue();

				// check if feedburner URL is valid
				if(!SpoonFilter::isURL($feedburner)) $feedburnerURL->addError(BL::err('InvalidURL'));
			}

			// init variable
			else $feedburner = null;

			// form is validated
			if($this->frm->isCorrect())
			{
				// set our settings
				BackendModel::setModuleSetting($this->URL->getModule(), 'overview_num_items', (int) $this->frm->getField('overview_number_of_items')->getValue());

				// comments
				BackendModel::setModuleSetting($this->URL->getModule(), 'spamfilter_comments', (bool) $this->frm->getField('spamfilter_comments')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'allow_comments', (bool) $this->frm->getField('allow_comments')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'moderation_comments', (bool) $this->frm->getField('moderation_comments')->getValue());

				// subscriptions
				BackendModel::setModuleSetting($this->URL->getModule(), 'spamfilter_subscriptions', (bool) $this->frm->getField('spamfilter_subscriptions')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'allow_subscriptions', (bool) $this->frm->getField('allow_subscriptions')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'moderation_subscriptions', (bool) $this->frm->getField('moderation_subscriptions')->getValue());

				// notifications
				BackendModel::setModuleSetting($this->URL->getModule(), 'notify_by_email_on_new_comment_to_moderate', (bool) $this->frm->getField('notify_by_email_on_new_comment_to_moderate')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'notify_by_email_on_new_comment', (bool) $this->frm->getField('notify_by_email_on_new_comment')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'notify_by_email_on_new_subscription', (bool) $this->frm->getField('notify_by_email_on_new_subscription')->getValue());

				BackendModel::setModuleSetting($this->URL->getModule(), 'ping_services', (bool) $this->frm->getField('ping_services')->getValue());

				BackendModel::setModuleSetting($this->URL->getModule(), 'rss_title_' . BL::getWorkingLanguage(), $this->frm->getField('rss_title')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'rss_description_' . BL::getWorkingLanguage(), $this->frm->getField('rss_description')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'rss_meta_' . BL::getWorkingLanguage(), $this->frm->getField('rss_meta')->getValue());
				if($feedburner !== null) BackendModel::setModuleSetting($this->URL->getModule(), 'feedburner_url_' . BL::getWorkingLanguage(), $feedburner);

				if(BackendModel::getModuleSetting('core', 'akismet_key') === null) BackendModel::setModuleSetting($this->URL->getModule(), 'spamfilter', false);

				// redirect to the settings page
				$this->redirect(BackendModel::createURLForAction('settings') . '&report=saved');
			}
		}
	}
}
