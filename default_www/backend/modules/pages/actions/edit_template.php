<?php

/**
 * BackendPagesEditTemplate
 * This is the edit-action, it will display a form to edit an item
 *
 * @package		backend
 * @subpackage	pages
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @author 		Tijs Verkoyen <tijs@netlash.com>
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
		if($this->id === null || !BackendPagesModel::existsTemplate($this->id)) $this->redirect(BackendModel::createURLForAction('templates') .'&error=non-existing');

		// get the record
		$this->record = BackendPagesModel::getTemplate($this->id);

		// unserialize
		$this->record['data'] = unserialize($this->record['data']);

		// assign
		$this->tpl->assign('template', $this->record);

		// determine if deleting is allowed
		$deleteAllowed = true;
		if($this->record['id'] == BackendModel::getModuleSetting('pages', 'default_template')) $deleteAllowed = false;
		elseif(count(BackendPagesModel::getTemplates()) == 1) $deleteAllowed = false;
		elseif(BackendPagesModel::isTemplateInUse($this->id))
		{
			// show that the template is used
			$this->tpl->assign('inUse', true);
			$deleteAllowed = false;
		}

		// assign
		$this->tpl->assign('deleteAllowed', $deleteAllowed);
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

		// init var
		$maximumBlocks = 20;

		// create elements
		$this->frm->addText('label', $this->record['label']);
		$this->frm->addText('file', str_replace('core/layout/templates/', '', $this->record['path']));
		$this->frm->addDropdown('num_blocks', array_combine(range(1, $maximumBlocks), range(1, $maximumBlocks)), $this->record['num_blocks']);
		if(BackendPagesModel::isTemplateInUse($this->id)) $this->frm->getField('num_blocks')->setAttributes(array('disabled' => 'disabled'));
		$this->frm->addTextarea('format', str_replace('],[', "],\n[", $this->record['data']['format']));
		$this->frm->addCheckbox('active', ($this->record['active'] == 'Y'));
		$this->frm->addCheckbox('default', ($this->record['id'] == BackendModel::getModuleSetting('pages', 'default_template')));

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
		$defaultExtras = array('' => array('editor' => BL::getLabel('Editor')),
								ucfirst(BL::getLabel('Modules')) => $blocks,
								ucfirst(BL::getLabel('Widgets')) => $widgets);

		// add some fields
		for($i = 1; $i <= $maximumBlocks; $i++)
		{
			// grab values
			$name = isset($this->record['data']['names'][$i - 1]) ? $this->record['data']['names'][$i - 1] : null;
			$extra = isset($this->record['data']['default_extras'][$i - 1]) ? $this->record['data']['default_extras'][$i - 1] : null;

			// build array
			$names[$i]['i'] = $i;
			$names[$i]['formElements']['txtName'] = $this->frm->addText('name_'. $i, $name);
			$names[$i]['formElements']['ddmType'] = $this->frm->addDropdown('type_'. $i, $defaultExtras, $extra);
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


			// validate syntax
			$syntax = trim(str_replace(array("\n", "\r"), '', $this->frm->getField('format')->getValue()));

			// init var
			$table = BackendPagesModel::templateSyntaxToArray($syntax);
			$cellCount = 0;
			$first = true;

			// loop rows
			foreach($table as $row)
			{
				// first row defines the cellcount
				if($first) $cellCount = count($row);

				// not same number of cells
				if(count($row) != $cellCount)
				{
					// add error
					$this->frm->getField('format')->addError(BL::getError('InvalidTemplateSyntax'));

					// stop
					break;
				}

				// reset
				$first = false;
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

				if(BackendPagesModel::isTemplateInUse($this->id)) $template['num_blocks'] = $this->record['num_blocks'];

				// loop fields
				for($i = 1; $i <= $template['num_blocks']; $i++)
				{
					$template['data']['names'][] = $this->frm->getField('name_'. $i)->getValue();
					$template['data']['default_extras'][] = $this->frm->getField('type_'. $i)->getValue();
				}

				// serialize
				$template['data'] = serialize($template['data']);

				// insert the item
				BackendPagesModel::updateTemplate($this->id, $template);

				// set default template
				if($this->frm->getField('default')->getChecked()) BackendModel::setModuleSetting('pages', 'default_template', $this->id);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('templates') .'&report=edited-template&var='. urlencode($template['label']));
			}
		}
	}
}

?>