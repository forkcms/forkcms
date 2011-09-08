<?php

/**
 * This is the add-action, it will display a form to create a new item
 *
 * @package		backend
 * @subpackage	pages
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendPagesAddTemplate extends BackendBaseActionAdd
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
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load additional js
		$this->header->addJS('templates.js');

		// load data
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
	 * Load necessary data.
	 *
	 * @return	void.
	 */
	private function loadData()
	{
		// get data
		$this->selectedTheme = $this->getParameter('theme', 'string');

		// build available themes
		$this->availableThemes = BackendModel::getThemes();

		// determine selected theme, based upon submitted form or default theme
		$this->selectedTheme = SpoonFilter::getValue($this->selectedTheme, array_keys($this->availableThemes), BackendModel::getModuleSetting('core', 'theme', 'core'));
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
				$item['theme'] = $this->frm->getField('theme')->getValue();
				$item['label'] = $this->frm->getField('label')->getValue();
				$item['path'] = 'core/layout/templates/' . $this->frm->getField('file')->getValue();
				$item['active'] = ($this->frm->getField('active')->getChecked()) ? 'Y' : 'N';
				$item['data']['format'] = trim(str_replace(array("\n", "\r"), '', $this->frm->getField('format')->getValue()));
				$item['data']['names'] = $this->names;
				$item['data']['default_extras'] = $this->extras;
				$item['data']['default_extras_' . BackendLanguage::getWorkingLanguage()] = $this->extras;

				// serialize the data
				$item['data'] = serialize($item['data']);

				// insert the item
				$item['id'] = BackendPagesModel::insertTemplate($item);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_add_template', array('item' => $item));

				// set default template
				if($this->frm->getField('default')->getChecked() && $item['theme'] == BackendModel::getModuleSetting('core', 'theme', 'core')) BackendModel::setModuleSetting($this->getModule(), 'default_template', $item['id']);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('templates') . '&theme=' . $item['theme'] . '&report=added-template&var=' . urlencode($item['label']) . '&highlight=row-' . $item['id']);
			}
		}
	}
}

?>
