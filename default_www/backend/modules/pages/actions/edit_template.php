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
	 * The position's default extras.
	 *
	 * @var	array
	 */
	private $extras = array();


	/**
	 * The position's names.
	 *
	 * @var	array
	 */
	private $names = array();


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load additional js
		$this->header->addJS('templates.js');

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
		$this->names = $this->record['data']['names'];
		if(isset($this->record['data']['default_extras_' . BL::getWorkingLanguage()])) $this->extras = $this->record['data']['default_extras_' . BL::getWorkingLanguage()];
		else $this->extras = $this->record['data']['default_extras'];

		// assign
		$this->tpl->assign('positions', $positions);

		// assign
		$this->tpl->assign('template', $this->record);

		// is the template being used
		$inUse = BackendPagesModel::isTemplateInUse($this->id);

		// determine if deleting is allowed
		$deleteAllowed = true;
		if($this->record['id'] == BackendModel::getModuleSetting($this->getModule(), 'default_template')) $deleteAllowed = false;
		elseif(count(BackendPagesModel::getTemplates()) == 1) $deleteAllowed = false;
		elseif($inUse) $deleteAllowed = false;

		// assign
		$this->tpl->assign('inUse', $inUse);
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
		$defaultId = BackendModel::getModuleSetting($this->getModule(), 'default_template');

		// create elements
		$this->frm->addDropdown('theme', BackendModel::getThemes(), BackendModel::getModuleSetting('core', 'theme', 'core'));
		$this->frm->addText('label', $this->record['label']);
		$this->frm->addText('file', str_replace('core/layout/templates/', '', $this->record['path']));
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
		$positions = array();
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

		// create default position field
		$position = array();
		$position['i'] = 0;
		$position['formElements']['txtPosition'] = $this->frm->addText('position_' . $position['i'], null, 255, 'inputText positionName', 'inputTextError positionName');
		$position['blocks'][]['formElements']['ddmType'] = $this->frm->addDropdown('type_' . $position['i'] . '_' . 0, $defaultExtras, null, false, 'positionBlock', 'positionBlockError');
		$positions[] = $position;

		// content has been submitted: re-create submitted content rather than the db-fetched content
		if(isset($_POST['position_0']))
		{
			// init vars
			$this->names = array();
			$this->extras = array();
			$i = 1;

			// loop submitted positions
			while(isset($_POST['position_' . $i]))
			{
				// init vars
				$j = 1;
				$extras = array();

				// gather position names
				$name = $_POST['position_' . $i];

				// loop submitted blocks
				while(isset($_POST['type_' . $i . '_' . $j]))
				{
					// gather blocks id
					$extras[] = $_POST['type_' . $i . '_' . $j];

					// increment counter; go fetch next block
					$j++;
				}

				// increment counter; go fetch next position
				$i++;

				// position already exists -> error
				if(in_array($name, $this->names)) $this->frm->addError(sprintf(BL::getError('DuplicatePositionName'), $name));

				// position name == fallback -> error
				if($name == 'fallback') $this->frm->addError(sprintf(BL::getError('ReservedPositionName'), $name));

				// not alphanumeric -> error
				if(!SpoonFilter::isValidAgainstRegexp('/^[a-z0-9]+$/i', $name)) $this->frm->addError(sprintf(BL::getError('NoAlphaNumPositionName'), $name));

				// save positions
				$this->names[] = $name;
				$this->extras[$name] = $extras;
			}
		}

		// build blocks array
		foreach($this->names as $i => $name)
		{
			// create default position field
			$position = array();
			$position['i'] = $i + 1;
			$position['formElements']['txtPosition'] = $this->frm->addText('position_' . $position['i'], $name, 255, 'inputText positionName', 'inputTextError positionName');
			foreach($this->extras[$name] as $extra) $position['blocks'][]['formElements']['ddmType'] = $this->frm->addDropdown('type_' . $position['i'] . '_' . 0, $defaultExtras, $extra, false, 'positionBlock', 'positionBlockError');
			$positions[] = $position;
		}

		// assign
		$this->tpl->assign('positions', $positions);
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

		// assign form errors
		$this->tpl->assign('formErrors', (string) $this->frm->getErrors());
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
			$this->frm->getField('file')->isFilled(BL::err('FieldIsRequired'));
			$this->frm->getField('label')->isFilled(BL::err('FieldIsRequired'));
			$this->frm->getField('format')->isFilled(BL::err('FieldIsRequired'));

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

				// doublecheck position names
				foreach($row as $cell)
				{
					// not alphanumeric -> error
					if($cell != '/' && !in_array($cell, $this->names)) $this->frm->getField('format')->addError(sprintf(BL::getError('NonExistingPositionName'), $cell));
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
				$item['active'] = ($this->frm->getField('active')->getChecked()) ? 'Y' : 'N';
				$item['data']['format'] = trim(str_replace(array("\n", "\r"), '', $this->frm->getField('format')->getValue()));
				$item['data']['names'] = $this->names;
				$item['data']['default_extras'] = $this->extras;
				$item['data']['default_extras_' . BackendLanguage::getWorkingLanguage()] = $this->extras;

				// serialize
				$item['data'] = serialize($item['data']);

				// if this is the default template make the template active
				if(BackendModel::getModuleSetting($this->getModule(), 'default_template') == $this->record['id']) $item['active'] = 'Y';

				// if the template is in use we can't de-activate it
				if(BackendPagesModel::isTemplateInUse($item['id'])) $item['active'] = 'Y';

				// insert the item
				BackendPagesModel::updateTemplate($item);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_edit_template', array('item' => $item));

				// set default template
				if($this->frm->getField('default')->getChecked() && $item['theme'] == BackendModel::getModuleSetting('core', 'theme', 'core')) BackendModel::setModuleSetting($this->getModule(), 'default_template', $item['id']);

				// update all existing pages using this template to add the newly inserted block(s)
//				if(BackendPagesModel::isTemplateInUse($item['id'])) BackendPagesModel::updatePagesTemplates($item['id'], $item['id']); // @todo: this will have to be changed completely (is it even neccassary?) think about this later

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('templates') . '&theme=' . $item['theme'] . '&report=edited-template&var=' . urlencode($item['label']) . '&highlight=row-' . $item['id']);
			}
		}
	}
}

?>