<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the settings-action, it will display a form to set general faq settings
 *
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 */
class BackendFaqSettings extends BackendBaseActionEdit
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		$this->loadForm();
		$this->validateForm();

		$this->parse();
		$this->display();
	}

	/**
	 * Loads the settings form
	 */
	private function loadForm()
	{
		// init settings form
		$this->frm = new BackendForm('settings');
		$this->frm->addDropdown('overview_number_of_items_per_category', array_combine(range(1, 30), range(1, 30)), BackendModel::getModuleSetting($this->URL->getModule(), 'overview_num_items_per_category', 10));
		$this->frm->addDropdown('most_read_number_of_items', array_combine(range(1, 10), range(1, 10)), BackendModel::getModuleSetting($this->URL->getModule(), 'most_read_num_items', 10));
		$this->frm->addDropdown('related_number_of_items', array_combine(range(1, 10), range(1, 10)), BackendModel::getModuleSetting($this->URL->getModule(), 'related_num_items', 3));
		$this->frm->addCheckbox('allow_multiple_categories', BackendModel::getModuleSetting($this->URL->getModule(), 'allow_multiple_categories', false));
		$this->frm->addCheckbox('spamfilter', BackendModel::getModuleSetting($this->URL->getModule(), 'spamfilter', false));
		$this->frm->addCheckbox('allow_feedback', BackendModel::getModuleSetting($this->URL->getModule(), 'allow_feedback', false));
		$this->frm->addCheckbox('allow_own_question', BackendModel::getModuleSetting($this->URL->getModule(), 'allow_own_question', false));
		$this->frm->addCheckbox('send_email_on_new_feedback', BackendModel::getModuleSetting($this->URL->getModule(), 'send_email_on_new_feedback', false));

		// no Akismet-key, so we can't enable spam-filter
		if(BackendModel::getModuleSetting('core', 'akismet_key') == '')
		{
			$this->frm->getField('spamfilter')->setAttribute('disabled', 'disabled');
			$this->tpl->assign('noAkismetKey', true);
		}
	}

	/**
	 * Validates the settings form
	 */
	private function validateForm()
	{
		if($this->frm->isSubmitted())
		{
			if($this->frm->isCorrect())
			{
				// set our settings
				BackendModel::setModuleSetting($this->URL->getModule(), 'overview_num_items_per_category', (int) $this->frm->getField('overview_number_of_items_per_category')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'most_read_num_items', (int) $this->frm->getField('most_read_number_of_items')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'related_num_items', (int) $this->frm->getField('related_number_of_items')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'allow_multiple_categories', (bool) $this->frm->getField('allow_multiple_categories')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'spamfilter', (bool) $this->frm->getField('spamfilter')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'allow_feedback', (bool) $this->frm->getField('allow_feedback')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'allow_own_question', (bool) $this->frm->getField('allow_own_question')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'send_email_on_new_feedback', (bool) $this->frm->getField('send_email_on_new_feedback')->getValue());
				if(BackendModel::getModuleSetting('core', 'akismet_key') === null) BackendModel::setModuleSetting($this->URL->getModule(), 'spamfilter', false);

				// redirect to the settings page
				$this->redirect(BackendModel::createURLForAction('settings') . '&report=saved');
			}
		}
	}
}
