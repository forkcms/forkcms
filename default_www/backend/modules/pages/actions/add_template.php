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
	 * All available themes
	 *
	 * @var	array
	 */
	private $availableThemes;


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
		$maximumBlocks = 30;

		// create elements
		$this->frm->addDropdown('theme', $this->availableThemes, $this->selectedTheme);
		$this->frm->addText('label');
		$this->frm->addText('file');
		$this->frm->addDropdown('num_blocks', array_combine(range(1, $maximumBlocks), range(1, $maximumBlocks)), 3);
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
			if($item['type'] == 'block') $blocks[$item['id']] = ucfirst(BL::lbl($item['label']));
			if($item['type'] == 'widget')
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
			$names[$i]['i'] = $i;
			$names[$i]['formElements']['txtName'] = $this->frm->addText('name_' . $i);
			$names[$i]['formElements']['ddmType'] = $this->frm->addDropdown('type_' . $i, $defaultExtras);
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
			$this->frm->getField('file')->isFilled(BL::err('FieldIsRequired'));
			$this->frm->getField('label')->isFilled(BL::err('FieldIsRequired'));
			$this->frm->getField('format')->isFilled(BL::err('FieldIsRequired'));

			// loop the know fields and validate them
			for($i = 1; $i <= $this->frm->getField('num_blocks')->getValue(); $i++)
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
				$item['theme'] = $this->frm->getField('theme')->getValue();
				$item['label'] = $this->frm->getField('label')->getValue();
				$item['path'] = 'core/layout/templates/' . $this->frm->getField('file')->getValue();
				$item['num_blocks'] = $this->frm->getField('num_blocks')->getValue();
				$item['active'] = ($this->frm->getField('active')->getChecked()) ? 'Y' : 'N';
				$item['data']['format'] = trim(str_replace(array("\n", "\r"), '', $this->frm->getField('format')->getValue()));

				// loop fields
				for($i = 1; $i <= $this->frm->getField('num_blocks')->getValue(); $i++)
				{
					$item['data']['names'][] = $this->frm->getField('name_' . $i)->getValue();
					$item['data']['default_extras'][] = $this->frm->getField('type_' . $i)->getValue();
					$item['data']['default_extras_' . BackendLanguage::getWorkingLanguage()][] = $this->frm->getField('type_' . $i)->getValue();
				}

				// serialize the data
				$item['data'] = serialize($item['data']);

				// insert the item
				$item['id'] = BackendPagesModel::insertTemplate($item);

				// set default template
				if($this->frm->getField('default')->getChecked() && $item['theme'] == BackendModel::getModuleSetting('core', 'theme', 'core')) BackendModel::setModuleSetting('pages', 'default_template', $item['id']);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('templates') . '&theme=' . $item['theme'] . '&report=added-template&var=' . urlencode($item['label']) . '&highlight=row-' . $item['id']);
			}
		}
	}
}

?>
