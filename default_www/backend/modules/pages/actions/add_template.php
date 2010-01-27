<?php

/**
 * BackendSnippetsAdd
 *
 * This is the add-action, it will display a form to create a new item
 *
 * @package		backend
 * @subpackage	snippets
 *
 * @author 		Davy Hellemans <davy@netlash.com>
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
		$this->frm->addTextField('label');
		$this->frm->addTextField('path');
		$this->frm->addDropDown('num_blocks', array(1 => 1, 2, 3, 4, 5, 6, 7, 8, 9, 10), 1);
		$this->frm->addTextField('format');
		$this->frm->addCheckBox('active');
		$this->frm->addCheckBox('default');
		$this->frm->addButton('save', ucfirst(BL::getLabel('AddTemplate')), 'submit');

		// init var
		$names = array();

		for($i = 1; $i <= 10; $i++)
		{
			$names[$i]['i'] = $i;
			$names[$i]['formElements']['txtName'] = $this->frm->addTextField('name_'. $i);
		}

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
			$this->frm->getField('path')->isFilled(BL::getError('FieldIsRequired'));
			$this->frm->getField('label')->isFilled(BL::getError('FieldIsRequired'));
			$this->frm->getField('format')->isFilled(BL::getError('FieldIsRequired'));

			for($i = 1; $i <= $this->frm->getField('num_blocks')->getValue(); $i++)
			{
				$this->frm->getField('name_'. $i)->isFilled(BL::getError('FieldIsRequired'));
			}

			// no errors?
			if($this->frm->isCorrect())
			{
				$template = array();
				$template['label'] = $this->frm->getField('label')->getValue();
				$template['path'] = $this->frm->getField('path')->getValue();
				$template['num_blocks'] = $this->frm->getField('num_blocks')->getValue();
				$template['active'] = ($this->frm->getField('active')->getChecked()) ? 'Y' : 'N';
				$template['is_default'] = ($this->frm->getField('default')->getChecked()) ? 'Y' : 'N';
				$template['data']['format'] = $this->frm->getField('format')->getValue();

				for($i = 1; $i <= $this->frm->getField('num_blocks')->getValue(); $i++)
				{
					$template['data']['names'][] = $this->frm->getField('name_'. $i)->getValue();
					$this->frm->getField('name_'. $i)->isFilled(BL::getError('FieldIsRequired'));
				}

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