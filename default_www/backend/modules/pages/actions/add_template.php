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
		$this->frm->addDropdown('num_blocks', array_combine(range(1, 10), range(1, 10)), 3);
		$this->frm->addTextarea('format');
		$this->frm->addCheckbox('active', true);
		$this->frm->addCheckbox('default');

		// init vars
		$names = array();
		$blocks = array();
		$widgets = array();
		$extras = BackendPagesModel::getExtras();

		// loop extras to populate the default extras
		foreach($extras as $item)
		{
			if($item['type'] == 'block') $blocks[$item['id']] = ucfirst(BL::getLabel($item['label']));
			if($item['type'] == 'widget')
			{
				$widgets[$item['id']] = ucfirst(BL::getLabel(SpoonFilter::toCamelCase($item['module']))) .': '. ucfirst(BL::getLabel($item['label']));
				if(isset($item['data']['extra_label'])) $widgets[$item['id']] = ucfirst(BL::getLabel(SpoonFilter::toCamelCase($item['module']))) .': '. $item['data']['extra_label'];
			}
		}

		// create array
		$defaultExtras = array('' => array('editor' =>  BL::getLabel('Editor')),
								ucfirst(BL::getLabel('Modules')) => $blocks,
								ucfirst(BL::getLabel('Widgets')) => $widgets);

		// add some fields
		for($i = 1; $i <= 10; $i++)
		{
			$names[$i]['i'] = $i;
			$names[$i]['formElements']['txtName'] = $this->frm->addText('name_'. $i);
			$names[$i]['formElements']['ddmType'] = $this->frm->addDropdown('type_'. $i, $defaultExtras);
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
				$template['data']['format'] = trim(str_replace(array("\n", "\r"), '', $this->frm->getField('format')->getValue()));

				// loop fields
				for($i = 1; $i <= $this->frm->getField('num_blocks')->getValue(); $i++)
				{
					$template['data']['names'][] = $this->frm->getField('name_'. $i)->getValue();
					$template['data']['default_extras'][] = $this->frm->getField('type_'. $i)->getValue();
				}

				// serialize the data
				$template['data'] = serialize($template['data']);

				// insert the item
				$id = BackendPagesModel::insertTemplate($template);

				// set default template
				if($this->frm->getField('default')->getChecked()) BackendModel::setModuleSetting('pages', 'default_template', $id);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('templates') .'&report=added-template&var='. urlencode($template['label']));
			}
		}
	}
}

?>