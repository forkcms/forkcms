<?php

/**
 * This is the themes-action, it will display a form to set theme settings
 *
 * @package		backend
 * @subpackage	settings
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.0
 */
class BackendSettingsThemes extends BackendBaseActionIndex
{
	/**
	 * The form instance
	 *
	 * @var	BackendForm
	 */
	private $frm;


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

		// parse
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
		$this->frm = new BackendForm('settingsThemes');

		// theme
		$this->frm->addDropdown('theme', BackendModel::getThemes(), BackendModel::getModuleSetting('core', 'theme', 'core'));
	}


	/**
	 * Parse the form
	 *
	 * @return	void
	 */
	private function parse()
	{
		// parse the form
		$this->frm->parse($this->tpl);
	}


	/**
	 * Validates the form
	 *
	 * @return	void
	 */
	private function validateForm()
	{
		// is the form submitted?
		if($this->frm->isSubmitted())
		{
			// no errors?
			if($this->frm->isCorrect())
			{
				// determine themes
				$newTheme = $this->frm->getField('theme')->getValue();
				$oldTheme = BackendModel::getModuleSetting('core', 'theme', 'core');

				// check if we actually switched themes
				if($newTheme != $oldTheme)
				{
					// fetch templates
					$oldTemplates = BackendPagesModel::getTemplates($oldTheme);
					$newTemplates = BackendPagesModel::getTemplates($newTheme);

					// check if templates already exist
					if(empty($newTemplates))
					{
						// templates do not yet exist; don't switch
						$this->redirect(BackendModel::createURLForAction('themes') . '&error=no-templates-available');
						exit;
					}

					// fetch current default template
					$oldDefaultTemplatePath = $oldTemplates[BackendModel::getModuleSetting('pages', 'default_template')]['path'];

					// loop new templates
					foreach($newTemplates as $newTemplateId => $newTemplate)
					{
						// check if a a similar default template exists
						if($newTemplate['path'] == $oldDefaultTemplatePath)
						{
							// set new default id
							$newDefaultTemplateId = (int) $newTemplateId;
							break;
						}
					}

					// no default template was found, set first template as default
					if(!isset($newDefaultTemplateId))
					{
						$newDefaultTemplateId = array_keys($newTemplates);
						$newDefaultTemplateId = $newDefaultTemplateId[0];
					}

					// update theme
					BackendModel::setModuleSetting('core', 'theme', $newTheme);

					// set amount of blocks
					BackendPagesModel::setMaximumBlocks();

					// save new default template
					BackendModel::setModuleSetting('pages', 'default_template', $newDefaultTemplateId);

					// loop old templates
					foreach($oldTemplates as $oldTemplateId => $oldTemplate)
					{
						// loop new templates
						foreach($newTemplates as $newTemplateId => $newTemplate)
						{
							// check if we have a matching template
							if($oldTemplate['path'] == $newTemplate['path'])
							{
								// switch template
								BackendPagesModel::updatePagesTemplates($oldTemplateId, $newTemplateId);

								// break loop
								continue 2;
							}
						}

						// getting here meant we found no matching template for the new theme; pick first theme's template as default
						BackendPagesModel::updatePagesTemplates($oldTemplateId, $newDefaultTemplateId);
					}
				}

				// assign report
				$this->tpl->assign('report', true);
				$this->tpl->assign('reportMessage', BL::msg('Saved'));
			}
		}
	}
}

?>