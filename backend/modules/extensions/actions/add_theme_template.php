<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the add-action, it will display a form to create a new item
 *
 * @author Matthias Mullie <matthias@mullie.eu>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendExtensionsAddThemeTemplate extends BackendBaseActionAdd
{
	/**
	 * All available themes.
	 *
	 * @var	array
	 */
	private $availableThemes = array();

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
	 * The theme we are adding a template for.
	 *
	 * @var string
	 */
	private $selectedTheme;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		// load additional js
		$this->header->addJS('theme_template.js');

		// load data
		$this->loadData();
		$this->loadForm();
		$this->validateForm();
		$this->parse();
		$this->display();
	}

	/**
	 * Load necessary data.
	 */
	private function loadData()
	{
		// get data
		$this->selectedTheme = $this->getParameter('theme', 'string');

		// build available themes
		foreach(BackendExtensionsModel::getThemes() as $theme) $this->availableThemes[$theme['value']] = $theme['label'];

		// determine selected theme, based upon submitted form or default theme
		$this->selectedTheme = SpoonFilter::getValue($this->selectedTheme, array_keys($this->availableThemes), BackendModel::getModuleSetting('core', 'theme', 'core'));
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('add');

		// init var
		$maximumPositions = 30;

		// create elements
		$this->frm->addDropdown('theme', $this->availableThemes, $this->selectedTheme);
		$this->frm->addText('label');
		$this->frm->addText('file');
		$this->frm->addTextarea('format');
		$this->frm->addCheckbox('active', true);
		$this->frm->addCheckbox('default');

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
		$defaultExtras = array('' => array(0 => SpoonFilter::ucfirst(BL::lbl('Editor'))),
								SpoonFilter::ucfirst(BL::lbl('Widgets')) => $widgets);

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
			foreach($this->extras[$name] as $extra) $position['blocks'][]['formElements']['ddmType'] = $this->frm->addDropdown('type_' . $position['i'] . '_' . 0, $defaultExtras, $extra, false, 'positionBlock', 'positionBlockError');
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
				$item['theme'] = $this->frm->getField('theme')->getValue();
				$item['label'] = $this->frm->getField('label')->getValue();
				$item['path'] = 'core/layout/templates/' . $this->frm->getField('file')->getValue();
				$item['active'] = $this->frm->getField('active')->getChecked() ? 'Y' : 'N';
				$item['data']['format'] = trim(str_replace(array("\n", "\r", ' '), '', $this->frm->getField('format')->getValue()));
				$item['data']['names'] = $this->names;
				$item['data']['default_extras'] = $this->extras;
				$item['data']['default_extras_' . BackendLanguage::getWorkingLanguage()] = $this->extras;

				// serialize the data
				$item['data'] = serialize($item['data']);

				// insert the item
				$item['id'] = BackendExtensionsModel::insertTemplate($item);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_add_template', array('item' => $item));

				// set default template
				if($this->frm->getField('default')->getChecked() && $item['theme'] == BackendModel::getModuleSetting('core', 'theme', 'core')) BackendModel::setModuleSetting($this->getModule(), 'default_template', $item['id']);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('theme_templates') . '&theme=' . $item['theme'] . '&report=added-template&var=' . urlencode($item['label']) . '&highlight=row-' . $item['id']);
			}
		}
	}
}
