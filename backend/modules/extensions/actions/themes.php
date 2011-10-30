<?php

/**
 * This is the themes-action, it will display the overview of modules.
 *
 * @package		backend
 * @subpackage	extensions
 *
 * @author		Matthias Mullie <matthias@mullie.eu>
 * @since		3.0.0
 */
class BackendExtensionsThemes extends BackendBaseActionIndex
{
	/**
	 * The form instance.
	 *
	 * @var	BackendForm
	 */
	private $frm;


	/**
	 * List of available themes (installed & installable)
	 *
	 * @var	array
	 */
	private $installableThemes, $installedThemes;


	/**
	 * Execute the action.
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load the data
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
	 * Load the available themes.
	 *
	 * @return	void
	 */
	private function loadData()
	{
		// loop themes
		foreach(BackendExtensionsModel::getThemes() as $theme)
		{
			// themes that are already installed = have at least 1 template in DB
			if($theme['installed']) $this->installedThemes[] = $theme;

			// themes that are not yet installed, but contain valid info.xml installation data
			elseif($theme['installable']) $this->installableThemes[] = $theme;
		}
	}


	/**
	 * Load the form.
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('settingsThemes');

		// fetch the themes
		$themes = $this->installedThemes;

		// set selected theme
		$selected = isset($_POST['installedThemes']) ? $_POST['installedThemes'] : BackendModel::getModuleSetting('core', 'theme', 'core');

		// no themes found
		if(empty($themes)) $this->redirect(BackendModel::createURLForAction('edit') . '&amp;id=' . $this->id . '&amp;step=1&amp;error=no-themes');

		// loop the templates
		foreach($themes as &$record)
		{
			// reformat custom variables
			$record['variables'] = array('thumbnail' => $record['thumbnail']);

			// set selected template
			if($record['value'] == $selected) $record['variables']['selected'] = true;

			// unset the language field
			unset($record['thumbnail']);
		}

		// templates
		$this->frm->addRadiobutton('installedThemes', $themes, $selected);
	}


	/**
	 * Parse the form.
	 *
	 * @return	void
	 */
	private function parse()
	{
		// parse the form
		$this->frm->parse($this->tpl);

		// parse not yet installed themes
		$this->tpl->assign('installableThemes', $this->installableThemes);
	}


	/**
	 * Validates the form.
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
				$newTheme = $this->frm->getField('installedThemes')->getValue();
				$oldTheme = BackendModel::getModuleSetting('core', 'theme', 'core');

				// check if we actually switched themes
				if($newTheme != $oldTheme)
				{
					// fetch templates
					$oldTemplates = BackendExtensionsModel::getTemplates($oldTheme);
					$newTemplates = BackendExtensionsModel::getTemplates($newTheme);

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

					// trigger event
					BackendModel::triggerEvent($this->getModule(), 'after_changed_theme');
				}

				// assign report
				$this->tpl->assign('report', true);
				$this->tpl->assign('reportMessage', BL::msg('Saved'));
			}
		}
	}
}

?>