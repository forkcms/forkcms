<?php

/**
 * This is the add-action, it will display a form to create a new item
 *
 * @package		backend
 * @subpackage	content_blocks
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.0
 */
class BackendContentBlocksAdd extends BackendBaseActionAdd
{
	/**
	 * The available templates
	 *
	 * @var	array
	 */
	private $templates = array();


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// fetch available templates
		$this->getTemplates();

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
	 * Get available templates
	 *
	 * @return	void
	 */
	private function getTemplates()
	{
		// fetch templates available in core
		$this->templates = SpoonFile::getList(FRONTEND_MODULES_PATH . '/content_blocks/layout/widgets');

		// fetch current active theme
		$theme = BackendModel::getModuleSetting('core', 'theme', 'core');

		// fetch theme templates if a theme is selected
		if($theme != 'core') $this->templates = array_merge($this->templates, SpoonFile::getList(FRONTEND_PATH . '/themes/' . $theme . '/modules/content_blocks/layout/widgets'));

		// no duplicates (core templates will be overridden by theme templates) and sort alphabetically
		$this->templates = array_unique($this->templates);
		sort($this->templates);
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
		$this->frm->addText('title');
		$this->frm->addEditor('text');
		$this->frm->addCheckbox('hidden', true);

		// if we have multiple templates, add a dropdown to select them
		if(count($this->templates) > 1) $this->frm->addDropdown('template', array_combine($this->templates, $this->templates));
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

			// validate fields
			$this->frm->getField('title')->isFilled(BL::err('TitleIsRequired'));

			// no errors?
			if($this->frm->isCorrect())
			{
				// build item
				$item['id'] = BackendContentBlocksModel::getMaximumId() + 1;
				$item['user_id'] = BackendAuthentication::getUser()->getUserId();
				$item['template'] = count($this->templates) > 1 ? $this->frm->getField('template')->getValue() : $this->templates[0];
				$item['language'] = BL::getWorkingLanguage();
				$item['title'] = $this->frm->getField('title')->getValue();
				$item['text'] = $this->frm->getField('text')->getValue();
				$item['hidden'] = $this->frm->getField('hidden')->getValue() ? 'N' : 'Y';
				$item['status'] = 'active';
				$item['created_on'] = BackendModel::getUTCDate();
				$item['edited_on'] = BackendModel::getUTCDate();

				// insert the item
				$item['revision_id'] = BackendContentBlocksModel::insert($item);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') . '&report=added&var=' . urlencode($item['title']) . '&highlight=row-' . $item['id']);
			}
		}
	}
}

?>