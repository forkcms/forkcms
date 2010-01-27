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
class BackendPagesEditTemplate extends BackendBaseActionEdit
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

		// parse the datagrid
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
		if(!BackendPagesModel::existsTemplate($this->id)) $this->redirect(BackendModel::createURLForAction('templates') .'&error=non-existing');

		// get the record
		$this->record = BackendPagesModel::getTemplate($this->id);
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

		$data = unserialize($this->record['data']);

		// create elements
		$this->frm->addTextField('label', $this->record['label']);
		$this->frm->addTextField('path', $this->record['path']);
		$this->frm->addDropDown('num_blocks', array(1 => 1, 2, 3, 4, 5, 6, 7, 8, 9, 10), $this->record['num_blocks']);
		$this->frm->addTextField('format', $data['format']);
		$this->frm->addCheckBox('active', ($this->record['active'] == 'Y'));
		$this->frm->addCheckBox('default', ($this->record['is_default'] == 'Y')); // @todo davy - is_default van het veld maken in form
		$this->frm->addButton('save', ucfirst(BL::getLabel('EditTemplate')), 'submit');

		// init var
		$names = array();

		for($i = 1; $i <= 10; $i++)
		{
			$value = isset($data['names'][$i - 1]) ? $data['names'][$i - 1] : null;
			$names[$i]['i'] = $i;
			$names[$i]['formElements']['txtName'] = $this->frm->addTextField('name_'. $i, $value);
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
				BackendPagesModel::updateTemplate($this->id, $template);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('templates') .'&report=edited&var='. urlencode($template['label']));
			}
		}
	}
}

?>