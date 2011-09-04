<?php

/**
 * BackendModulemanagerEdit
 * This is the edit-action
 *
 * @package		backend
 * @subpackage	module_manager
 *
 * @author		Frederik Heyninck <frederik@figure8.be>
 * @since		2.0
 */
class BackendModulemanagerEdit extends BackendBaseActionEdit
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
		
		// load record
		$this->loadData();

		// load the form
		$this->loadForm();

		// validate the form
		$this->validateForm();

		// parse
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Load the record
	 *
	 * @return	void
	 */
	private function loadData()
	{
		// get record
		$this->module = $this->getParameter('module', 'string');

		// validate id
		if($this->module === null || !BackendModulemanagerModel::exists($this->module)) $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');

		// get the record
		$this->record = BackendModulemanagerModel::get($this->module);
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('edit');

		// create elements
		$this->frm->addText('name',$this->record['name']);
		$this->frm->addTextarea('description',$this->record['description']);
		$this->frm->addCheckbox('active', ($this->record['active'] == 'Y'));
	}


	/**
	 * Parse the form
	 *
	 * @return	void
	 */
	protected function parse()
	{
		// call parent
		parent::parse();
		$this->tpl->assign('item', $this->record);
	}


	/**
	 * Validate the form
	 *
	 * @return	void
	 */
	private function validateForm()
	{
		// is the form submitted?
		if($this->frm->isSubmitted())
		{
			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// validate fields
			$this->frm->getField('name')->isFilled(BL::err('FieldIsRequired'));

			// no errors?
			if($this->frm->isCorrect())
			{
				$item['name'] = $this->frm->getField('name')->getValue();
				$item['description'] = $this->frm->getField('description')->getValue();
				$item['active'] = $this->frm->getField('active')->isChecked();
				
				BackendModulemanagerModel::update($item);
				$this->redirect(BackendModel::createURLForAction('index') . '&report=saved');
			}
		}
	}
		
}

?>