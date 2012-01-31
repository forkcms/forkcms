<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the edit-action, it will display a form to edit an item
 *
 * @author Matthias Mullie <matthias@mullie.eu>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class BackendExtensionsEditThemeTemplate extends BackendBaseActionEdit
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
	 */
	public function execute()
	{
		parent::execute();
		$this->header->addJS('theme_template.js');
		$this->loadData();
		$this->loadForm();
		$this->validateForm();
		$this->parse();
		$this->display();
	}

	/**
	 * Load the record
	 */
	private function loadData()
	{
		// get record
		$this->id = $this->getParameter('id', 'int');

		// validate id
		if($this->id === null || !BackendExtensionsModel::existsTemplate($this->id)) $this->redirect(BackendModel::createURLForAction('theme_templates') . '&error=non-existing');

		// get the record
		$this->record = BackendExtensionsModel::getTemplate($this->id);

		// unserialize
		$this->record['data'] = unserialize($this->record['data']);
		$this->names = $this->record['data']['names'];
		if(isset($this->record['data']['default_extras_' . BL::getWorkingLanguage()])) $this->extras = $this->record['data']['default_extras_' . BL::getWorkingLanguage()];
		elseif(isset($this->record['data']['default_extras'])) $this->extras = $this->record['data']['default_extras'];

		// assign
		$this->tpl->assign('template', $this->record);

		// is the template being used
		$inUse = BackendExtensionsModel::isTemplateInUse($this->id);

		// determine if deleting is allowed
		$deleteAllowed = true;
		if($this->record['id'] == BackendModel::getModuleSetting($this->getModule(), 'default_template')) $deleteAllowed = false;
		elseif(count(BackendExtensionsModel::getTemplates()) == 1) $deleteAllowed = false;
		elseif($inUse) $deleteAllowed = false;
		elseif(!BackendAuthentication::isAllowedAction('delete_theme_template')) $deleteAllowed = false;

		// assign
		$this->tpl->assign('inUse', $inUse);
		$this->tpl->assign('showExtensionsDeleteThemeTemplate', $deleteAllowed);
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('edit');

		// init var
		$defaultId = BackendModel::getModuleSetting($this->getModule(), 'default_template');

		// build available themes
		$themes = array();
		foreach(BackendExtensionsModel::getThemes() as $theme) $themes[$theme['value']] = $theme['label'];

		// create elements
		$this->frm->addDropdown('theme', $themes, BackendModel::getModuleSetting('core', 'theme', 'core'));
		$this->frm->addText('label', $this->record['label']);
		$this->frm->addText('file', str_replace('core/layout/templates/', '', $this->record['path']));
		$this->frm->addTextarea('format', str_replace('],[', "],\n[", $this->record['data']['format']));
		$this->frm->addCheckbox('active', ($this->record['active'] == 'Y'));
		$this->frm->addCheckbox('default', ($this->record['id'] == $defaultId));
		$this->frm->addCheckbox('overwrite', false);

		// if this is the default template we can't alter the active/default state
		if(($this->record['id'] == $defaultId))
		{
			$this->frm->getField('active')->setAttributes(array('disabled' => 'disabled'));
			$this->frm->getField('default')->setAttributes(array('disabled' => 'disabled'));
		}

		// if the template is in use we cant alter the active state
		if(BackendExtensionsModel::isTemplateInUse($this->id))
		{
			$this->frm->getField('active')->setAttributes(array('disabled' => 'disabled'));
		}

		// init vars
		$positions = array();
		$blocks = array();
		$widgets = array();
		$extras = BackendExtensionsModel::getExtras();

		// loop extras to populate the default extras
		foreach($extras as $item)
		{
			if($item['type'] == 'block')
			{
				$blocks[$item['id']] = SpoonFilter::ucfirst(BL::lbl($item['label']));
				if(isset($item['data']['extra_label'])) $blocks[$item['id']] = SpoonFilter::ucfirst($item['data']['extra_label']);
			}

			elseif($item['type'] == 'widget')
			{
				$widgets[$item['id']] = SpoonFilter::ucfirst(BL::lbl(SpoonFilter::toCamelCase($item['module']))) . ': ' . SpoonFilter::ucfirst(BL::lbl($item['label']));
				if(isset($item['data']['extra_label'])) $widgets[$item['id']] = SpoonFilter::ucfirst(BL::lbl(SpoonFilter::toCamelCase($item['module']))) . ': ' . $item['data']['extra_label'];
			}
		}

		// sort
		asort($blocks, SORT_STRING);
		asort($widgets, SORT_STRING);

		// create array
		$defaultExtras = array(
			'' => array(0 => SpoonFilter::ucfirst(BL::lbl('Editor'))),
			SpoonFilter::ucfirst(BL::lbl('Widgets')) => $widgets
		);

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
			$errors = array();

			// loop submitted positions
			while(isset($_POST['position_' . $i]))
			{
				// init vars
				$j = 0;
				$extras = array();

				// gather position names
				$name = $_POST['position_' . $i];

				// loop submitted blocks
				while(isset($_POST['type_' . $i . '_' . $j]))
				{
					// gather blocks id
					$extras[] = (int) $_POST['type_' . $i . '_' . $j];

					// increment counter; go fetch next block
					$j++;
				}

				// increment counter; go fetch next position
				$i++;

				// position already exists -> error
				if(in_array($name, $this->names)) $errors[] = sprintf(BL::getError('DuplicatePositionName'), $name);

				// position name == fallback -> error
				if($name == 'fallback') $errors[] = sprintf(BL::getError('ReservedPositionName'), $name);

				// not alphanumeric -> error
				if(!SpoonFilter::isValidAgainstRegexp('/^[a-z0-9]+$/i', $name)) $errors[] = sprintf(BL::getError('NoAlphaNumPositionName'), $name);

				// save positions
				$this->names[] = $name;
				$this->extras[$name] = $extras;
			}

			// add errors
			if($errors) $this->frm->addError(implode('<br />', array_unique($errors)));
		}

		// build blocks array
		foreach($this->names as $i => $name)
		{
			// create default position field
			$position = array();
			$position['i'] = $i + 1;
			$position['formElements']['txtPosition'] = $this->frm->addText('position_' . $position['i'], $name, 255, 'inputText positionName', 'inputTextError positionName');

			if(isset($this->extras[$name]))
			{
				foreach($this->extras[$name] as $y => $extra)
				{
					$position['blocks'][]['formElements']['ddmType'] = $this->frm->addDropdown('type_' . $position['i'] . '_' . $y, $defaultExtras, $extra, false, 'positionBlock', 'positionBlockError');
				}
			}

			$positions[] = $position;
		}

		// assign
		$this->tpl->assign('positions', $positions);
	}

	/**
	 * Parse the form
	 */
	protected function parse()
	{
		parent::parse();

		// assign form errors
		$this->tpl->assign('formErrors', (string) $this->frm->getErrors());
	}

	/**
	 * Validate the form
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
			$syntax = trim(str_replace(array("\n", "\r", ' '), '', $this->frm->getField('format')->getValue()));

			// init var
			$table = BackendExtensionsModel::templateSyntaxToArray($syntax);

			// validate the syntax
			if($table === false) $this->frm->getField('format')->addError(BL::err('InvalidTemplateSyntax'));

			else
			{
				$html = BackendExtensionsModel::buildTemplateHTML($syntax);
				$cellCount = 0;
				$first = true;
				$errors = array();

				// loop rows
				foreach($table as $row)
				{
					// first row defines the cellcount
					if($first) $cellCount = count($row);

					// not same number of cells
					if(count($row) != $cellCount)
					{
						// add error
						$errors[] = BL::err('InvalidTemplateSyntax');

						// stop
						break;
					}

					// doublecheck position names
					foreach($row as $cell)
					{
						// ignore unavailable space
						if($cell != '/')
						{
							// not alphanumeric -> error
							if(!in_array($cell, $this->names)) $errors[] = sprintf(BL::getError('NonExistingPositionName'), $cell);

							// can't build proper html -> error
							elseif(substr_count($html, '"#position-' . $cell . '"') != 1) $errors[] = BL::err('InvalidTemplateSyntax');
						}
					}

					// reset
					$first = false;
				}

				// add errors
				if($errors) $this->frm->getField('format')->addError(implode('<br />', array_unique($errors)));
			}

			// no errors?
			if($this->frm->isCorrect())
			{
				// build array
				$item['id'] = $this->id;
				$item['theme'] = $this->frm->getField('theme')->getValue();
				$item['label'] = $this->frm->getField('label')->getValue();
				$item['path'] = 'core/layout/templates/' . $this->frm->getField('file')->getValue();
				$item['active'] = $this->frm->getField('active')->getChecked() ? 'Y' : 'N';
				$item['data']['format'] = trim(str_replace(array("\n", "\r", ' '), '', $this->frm->getField('format')->getValue()));
				$item['data']['names'] = $this->names;
				$item['data']['default_extras'] = $this->extras;
				$item['data']['default_extras_' . BackendLanguage::getWorkingLanguage()] = $this->extras;

				// serialize
				$item['data'] = serialize($item['data']);

				// if this is the default template make the template active
				if(BackendModel::getModuleSetting($this->getModule(), 'default_template') == $this->record['id']) $item['active'] = 'Y';

				// if the template is in use we can't de-activate it
				if(BackendExtensionsModel::isTemplateInUse($item['id'])) $item['active'] = 'Y';

				// insert the item
				BackendExtensionsModel::updateTemplate($item);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_edit_template', array('item' => $item));

				// set default template
				if($this->frm->getField('default')->getChecked() && $item['theme'] == BackendModel::getModuleSetting('core', 'theme', 'core')) BackendModel::setModuleSetting($this->getModule(), 'default_template', $item['id']);

				// update all existing pages using this template to add the newly inserted block(s)
				if(BackendExtensionsModel::isTemplateInUse($item['id'])) BackendPagesModel::updatePagesTemplates($item['id'], $item['id'], $this->frm->getField('overwrite')->getChecked());

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('theme_templates') . '&theme=' . $item['theme'] . '&report=edited-template&var=' . urlencode($item['label']) . '&highlight=row-' . $item['id']);
			}
		}
	}
}
