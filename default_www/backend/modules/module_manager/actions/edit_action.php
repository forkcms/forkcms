<?php

/**
 * BackendModulemanagerEditAction
 * This is the edit-action-action
 *
 * @package		backend
 * @subpackage	module_manager
 *
 * @author		Frederik Heyninck <frederik@figure8.be>
 * @since		2.0
 */
class BackendModulemanagerEditAction extends BackendBaseActionEdit
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
		$this->id = $this->getParameter('id', 'int');

		// validate id
		if($this->id === null || !BackendModulemanagerModel::actionExists($this->id)) $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');

		// get the record
		$this->record = BackendModulemanagerModel::getAction($this->id);
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
		$this->frm->addText('action', $this->record['action']);
		$this->frm->addDropdown('group_id', BackendModulemanagerModel::getGroupsForDropdown(),$this->record['group_id']);
		$this->frm->addDropdown('modules_list', BackendModulemanagerModel::getModulesForDropdown(), $this->record['module']);
		$this->frm->addDropdown('levels', array('1' => '1','3' => '3','5' => '5','7' => '7'), $this->record['level']);
		
		$this->frm->getField('group_id')->setDefaultElement(ucfirst(BL::getLabel('ChooseAGroup')));
		$this->frm->getField('modules_list')->setDefaultElement(ucfirst(BL::getLabel('ChooseAModule')));
		$this->frm->getField('levels')->setDefaultElement(ucfirst(BL::getLabel('ChooseALevel')));
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
			$this->frm->getField('action')->isFilled(BL::err('FieldIsRequired'));
			$this->frm->getField('group_id')->isFilled(BL::err('FieldIsRequired'));
			$this->frm->getField('modules_list')->isFilled(BL::err('FieldIsRequired'));
			$this->frm->getField('levels')->isFilled(BL::err('FieldIsRequired'));
		
			$item['action'] = $this->frm->getField('action')->getValue();
			$item['group_id'] = $this->frm->getField('group_id')->getValue();
			$item['module'] = $this->frm->getField('modules_list')->getValue();
			$item['level'] = $this->frm->getField('levels')->getValue();
			$item['id'] = $this->id;
			
			// no errors?
			if($this->frm->isCorrect())
			{
				BackendModulemanagerModel::updateAction($item);
				$this->redirect(BackendModel::createURLForAction('actions') . '&report=saved&module=' . $this->record['module'] . '&highlight=row-' . $this->id);
			}
		}
	}


}

?>