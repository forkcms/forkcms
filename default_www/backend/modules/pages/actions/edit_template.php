<?php

/**
 * This is the edit-action, it will display a form to edit an item
 *
 * @package		backend
 * @subpackage	pages
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
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
		if($this->id === null || !BackendPagesModel::existsTemplate($this->id)) $this->redirect(BackendModel::createURLForAction('templates') . '&error=non-existing');

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
		$maximumBlocks = 30;
		$defaultId = BackendModel::getModuleSetting('pages', 'default_template');

		// create elements
		$this->frm->addDropdown('theme', BackendModel::getThemes(), BackendModel::getModuleSetting('core', 'theme', 'core'));
		$this->frm->addText('label', $this->record['label']);
		$this->frm->addText('file', str_replace('core/layout/templates/', '', $this->record['path']));
		$this->frm->addDropdown('num_blocks', array_combine(range(1, $maximumBlocks), range(1, $maximumBlocks)), $this->record['num_blocks']);
		$this->frm->addTextarea('format', str_replace('],[', "],\n[", $this->record['data']['format']));
		$this->frm->addCheckbox('active', ($this->record['active'] == 'Y'));
		$this->frm->addCheckbox('default', ($this->record['id'] == $defaultId));

		// if this is the default template we can't alter the active/default state
		if(($this->record['id'] == $defaultId))
		{
			$this->frm->getField('active')->setAttributes(array('disabled' => 'disabled'));
			$this->frm->getField('default')->setAttributes(array('disabled' => 'disabled'));
		}

		// if the template is in use we cant alter the active state
		if(BackendPagesModel::isTemplateInUse($this->id))
		{
			$this->frm->getField('active')->setAttributes(array('disabled' => 'disabled'));
		}

		// init vars
		$names = array();
		$blocks = array();
		$widgets = array();
		$extras = BackendPagesModel::getExtras();

		// loop extras to populate the default extras
		foreach($extras as $item)
		{
			if($item['type'] == 'block')
			{
				$blocks[$item['id']] = ucfirst(BL::lbl($item['label']));
				if(isset($item['data']['extra_label'])) $blocks[$item['id']] = ucfirst($item['data']['extra_label']);
			}

			elseif($item['type'] == 'widget')
			{
				$widgets[$item['id']] = ucfirst(BL::lbl(SpoonFilter::toCamelCase($item['module']))) . ': ' . ucfirst(BL::lbl($item['label']));
				if(isset($item['data']['extra_label'])) $widgets[$item['id']] = ucfirst(BL::lbl(SpoonFilter::toCamelCase($item['module']))) . ': ' . $item['data']['extra_label'];
			}
		}

		// sort
		asort($blocks, SORT_STRING);
		asort($widgets, SORT_STRING);

		// create array
		$defaultExtras = array('' => array('editor' => BL::lbl('Editor')),
								ucfirst(BL::lbl('Modules')) => $blocks,
								ucfirst(BL::lbl('Widgets')) => $widgets);

		// add some fields
		for($i = 1; $i <= $maximumBlocks; $i++)
		{
			// grab values
			$name = isset($this->record['data']['names'][$i - 1]) ? $this->record['data']['names'][$i - 1] : null;
			$extra = isset($this->record['data']['default_extras'][$i - 1]) ? $this->record['data']['default_extras'][$i - 1] : null;

			// build array
			$names[$i]['i'] = $i;
			$names[$i]['formElements']['txtName'] = $this->frm->addText('name_' . $i, $name);
			$names[$i]['formElements']['ddmType'] = $this->frm->addDropdown('type_' . $i, $defaultExtras, $extra);
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

			// num blocks cant be altered when the template is in use
			$numBlocks = (int) $this->frm->getField('num_blocks')->getValue();

			// required fields
			$this->frm->getField('file')->isFilled(BL::err('FieldIsRequired'));
			$this->frm->getField('label')->isFilled(BL::err('FieldIsRequired'));
			$this->frm->getField('format')->isFilled(BL::err('FieldIsRequired'));

			// loop the know fields and validate them
			for($i = 1; $i <= $numBlocks; $i++)
			{
				$this->frm->getField('name_' . $i)->isFilled(BL::err('FieldIsRequired'));
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
					$this->frm->getField('format')->addError(BL::err('InvalidTemplateSyntax'));

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
				$item['id'] = $this->id;
				$item['theme'] = $this->frm->getField('theme')->getValue();
				$item['label'] = $this->frm->getField('label')->getValue();
				$item['path'] = 'core/layout/templates/' . $this->frm->getField('file')->getValue();
				$item['num_blocks'] = $numBlocks;
				$item['active'] = ($this->frm->getField('active')->getChecked()) ? 'Y' : 'N';

				// if this is the default template make the template active
				if(BackendModel::getModuleSetting('pages', 'default_template') == $this->record['id']) $item['active'] = 'Y';

				// if the template is in use we can't de-activate it
				if(BackendPagesModel::isTemplateInUse($item['id'])) $item['active'] = 'Y';

				// init template data
				$item['data']['format'] = $this->record['data']['format'];

				// loop fields
				for($i = 1; $i <= $item['num_blocks']; $i++)
				{
					$item['data']['names'][$i - 1] = $this->frm->getField('name_' . $i)->getValue();
					$item['data']['default_extras'][$i - 1] = $this->frm->getField('type_' . $i)->getValue();
					$item['data']['default_extras_' . BackendLanguage::getWorkingLanguage()][$i - 1] = $this->frm->getField('type_' . $i)->getValue();
				}

				// blocks layout
				$item['data']['format'] = trim(str_replace(array("\n", "\r"), '', $this->frm->getField('format')->getValue()));

				// serialize
				$item['data'] = serialize($item['data']);

				// insert the item
				BackendPagesModel::updateTemplate($item);

				// set default template
				if($this->frm->getField('default')->getChecked() && $item['theme'] == BackendModel::getModuleSetting('core', 'theme', 'core')) BackendModel::setModuleSetting('pages', 'default_template', $item['id']);

				// update all existing pages using this template to add the newly inserted block(s)
				if(BackendPagesModel::isTemplateInUse($item['id'])) BackendPagesModel::updatePagesTemplates($item['id'], $item['id']);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('templates') . '&theme=' . $item['theme'] . '&report=edited-template&var=' . urlencode($item['label']) . '&highlight=row-' . $item['id']);
			}
		}
	}
}

?>