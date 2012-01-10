<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the settings-action, it will display a form to set general pages settings
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dave Lens <dave.lens@netlash.com>
 */
class BackendPagesSettings extends BackendBaseActionEdit
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->loadForm();
		$this->validateForm();
		$this->parse();
		$this->display();
	}

	/**
	 * Loads the settings form
	 */
	private function loadForm()
	{
		// init settings form
		$this->frm = new BackendForm('settings');

		// add fields for meta navigation
		$this->frm->addCheckbox('meta_navigation', BackendModel::getModuleSetting($this->getModule(), 'meta_navigation', false));

		$itemsNumber = range(25, 500, 25);
		$itemsNumber = array_combine($itemsNumber, $itemsNumber);
		$this->frm->addDropdown('sitemap_pages_items', $itemsNumber, FrontendModel::getModuleSetting('pages', 'sitemap_pages_items', 100));
		$this->frm->addDropdown('sitemap_images_items', $itemsNumber, FrontendModel::getModuleSetting('pages', 'sitemap_images_items', 100));
	}

	/**
	 * Validates the settings form
	 */
	private function validateForm()
	{
		// form is submitted
		if($this->frm->isSubmitted())
		{
			$fields = $this->frm->getFields();

			// form is validated
			if($this->frm->isCorrect())
			{
				// set our settings
				BackendModel::setModuleSetting($this->getModule(), 'meta_navigation', (bool) $fields['meta_navigation']->getValue());
				BackendModel::setModuleSetting($this->getModule(), 'sitemap_pages_items', (int) $fields['sitemap_pages_items']->getValue());
				BackendModel::setModuleSetting($this->getModule(), 'sitemap_images_items', (int) $fields['sitemap_images_items']->getValue());

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_saved_settings');

				// redirect to the settings page
				$this->redirect(BackendModel::createURLForAction('settings') . '&report=saved');
			}
		}
	}
}
