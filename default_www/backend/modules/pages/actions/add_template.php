<?php

/**
 * BackendPagesAddTemplate
 * This is the add-action, it will display a form to create a new item
 *
 * @package		backend
 * @subpackage	pages
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendPagesAddTemplate extends BackendBaseActionAdd
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

		// parse the datagrid
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
		// create form
		$this->frm = new BackendForm('add');

		// create elements
		$this->frm->addText('label');
		$this->frm->addText('file');
		$this->frm->addDropdown('num_blocks', array(1 => 1, 2, 3, 4, 5, 6, 7, 8, 9, 10), 1);
		$this->frm->addText('format');
		$this->frm->addCheckbox('active');
		$this->frm->addCheckbox('default');

		// init vars
		$names = array();
		$types = BackendPagesModel::getTypes();

		// add some fields
		for($i = 1; $i <= 10; $i++)
		{
			$names[$i]['i'] = $i;
			$names[$i]['formElements']['txtName'] = $this->frm->addText('name_'. $i);
			$names[$i]['formElements']['ddmType'] = $this->frm->addDropdown('type_'. $i, $types);
		}

		// assign
		$this->tpl->assign('names', $names);
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

			// required fields
			$this->frm->getField('file')->isFilled(BL::getError('FieldIsRequired'));
			$this->frm->getField('label')->isFilled(BL::getError('FieldIsRequired'));
			$this->frm->getField('format')->isFilled(BL::getError('FieldIsRequired'));

			// loop the know fields and validate them
			for($i = 1; $i <= $this->frm->getField('num_blocks')->getValue(); $i++)
			{
				$this->frm->getField('name_'. $i)->isFilled(BL::getError('FieldIsRequired'));
			}

			// no errors?
			if($this->frm->isCorrect())
			{
				// build array
				$template = array();
				$template['label'] = $this->frm->getField('label')->getValue();
				$template['path'] = 'core/layout/templates/'. $this->frm->getField('file')->getValue();
				$template['num_blocks'] = $this->frm->getField('num_blocks')->getValue();
				$template['active'] = ($this->frm->getField('active')->getChecked()) ? 'Y' : 'N';
				$template['is_default'] = ($this->frm->getField('default')->getChecked()) ? 'Y' : 'N';
				$template['data']['format'] = $this->frm->getField('format')->getValue();

				// loop fields
				for($i = 1; $i <= $this->frm->getField('num_blocks')->getValue(); $i++)
				{
					$template['data']['names'][] = $this->frm->getField('name_'. $i)->getValue();
					$template['data']['types'][] = $this->frm->getField('type_'. $i)->getValue();
				}

				// serialize the data
				$template['data'] = serialize($template['data']);

				// insert the item
				BackendPagesModel::insertTemplate($template);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('templates') .'&report=added&var='. urlencode($template['label']));
			}
		}
	}
}

?>