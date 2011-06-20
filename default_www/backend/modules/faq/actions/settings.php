<?php

/**
 * This is the settings-action, it will display a form to set general faq settings
 *
 * @package		backend
 * @subpackage	faq
 *
 * @author		Annelies Van Extergem <annelies@netlash.com>
 * @since		2.3
 */
class BackendFaqSettings extends BackendBaseActionEdit
{
	/**
	 * Execute the action
	 *
	 * @return	void
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
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// init settings form
		$this->frm = new BackendForm('settings');

		// add fields for pagination
		$this->frm->addDropdown('overview_number_of_items_per_category', array_combine(range(1, 30), range(1, 30)), BackendModel::getModuleSetting($this->URL->getModule(), 'overview_num_items_per_category', 10));
		$this->frm->addDropdown('most_read_number_of_items', array_combine(range(1, 10), range(1, 10)), BackendModel::getModuleSetting($this->URL->getModule(), 'most_read_num_items', 10));
		$this->frm->addDropdown('related_number_of_items', array_combine(range(1, 10), range(1, 10)), BackendModel::getModuleSetting($this->URL->getModule(), 'related_num_items', 3));

		// add fields for spam
		$this->frm->addCheckbox('spamfilter', BackendModel::getModuleSetting($this->URL->getModule(), 'spamfilter', false));

		// no Akismet-key, so we can't enable spam-filter
		if(BackendModel::getModuleSetting('core', 'akismet_key') == '')
		{
			$this->frm->getField('spamfilter')->setAttribute('disabled', 'disabled');
			$this->tpl->assign('noAkismetKey', true);
		}

		// add fields for feedback and own questions
		$this->frm->addCheckbox('allow_feedback', BackendModel::getModuleSetting($this->URL->getModule(), 'allow_feedback', false));
		$this->frm->addCheckbox('allow_own_question', BackendModel::getModuleSetting($this->URL->getModule(), 'allow_own_question', false));
	}


	/**
	 * Validates the settings form
	 *
	 * @return	void
	 */
	private function validateForm()
	{
		// form is submitted
		if($this->frm->isSubmitted())
		{
			// form is validated
			if($this->frm->isCorrect())
			{
				// set our settings
				BackendModel::setModuleSetting($this->URL->getModule(), 'overview_num_items_per_category', (int) $this->frm->getField('overview_number_of_items_per_category')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'most_read_num_items', (int) $this->frm->getField('most_read_number_of_items')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'related_num_items', (int) $this->frm->getField('related_number_of_items')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'spamfilter', (bool) $this->frm->getField('spamfilter')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'allow_feedback', (bool) $this->frm->getField('allow_feedback')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'allow_own_question', (bool) $this->frm->getField('allow_own_question')->getValue());
				if(BackendModel::getModuleSetting('core', 'akismet_key') === null) BackendModel::setModuleSetting($this->URL->getModule(), 'spamfilter', false);

				// redirect to the settings page
				$this->redirect(BackendModel::createURLForAction('settings') . '&report=saved');
			}
		}
	}
}

?>