<?php
/**
 * BackendModulemanagerAddAction
 * This is the add-action
 *
 * @package		backend
 * @subpackage	module_manager
 *
 * @author 		Frederik Heyninck <frederik@figure8.be>
 * @since		2.0
 */
class BackendModulemanagerAddAction extends BackendBaseActionAdd
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
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// get default category id
		$module = $this->getParameter('module','string');

		// create form
		$this->frm = new BackendForm('add');

		// create elements
		$this->frm->addText('action');
		$this->frm->addDropdown('group_id', BackendModulemanagerModel::getGroupsForDropdown());
		$this->frm->addDropdown('modules_list', BackendModulemanagerModel::getModulesForDropdown(), $module);
		$this->frm->addDropdown('levels', array('1' => '1','3' => '3','5' => '5','7' => '7'), '7');
		
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
			
			if(BackendModulemanagerModel::rightActionExists($item['group_id'], $item['action'], $item['module'], $item['level']))
			{
				$this->frm->getField('action')->addError(BL::err('ActionAllreadyExists'));	
			}

			// no errors?
			if($this->frm->isCorrect())
			{
				BackendModulemanagerModel::insertAction($item);
				$this->redirect(BackendModel::createURLForAction('index').'&report=added');
			}
		}
	}
}

?>