<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the edit-action, it will display a form with the item data to edit
 *
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 */
class BackendSitemapSettings extends BackendBaseActionEdit
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
	 * Load the form
	 */
	protected function loadForm()
	{
		// create form
		$this->frm = new BackendForm('settings');
		$this->frm->addDropdown('sitemap_pages_items', range(25, 500, 25), BackendModel::getModuleSetting($this->getModule(), 'sitemap_pages_items', 100));
		$this->frm->addDropdown('sitemap_images_items', range(25, 500, 25), BackendModel::getModuleSetting($this->getModule(), 'sitemap_images_items', 100));
	}

	/**
	 * Validate the form
	 */
	protected function validateForm()
	{
		if($this->frm->isSubmitted())
		{
			$this->frm->cleanupFields();

			if($this->frm->isCorrect())
			{
				BackendModel::setModuleSetting($this->getModule(), 'sitemap_pages_items', (int) $this->frm->getField('sitemap_pages_items')->getValue());
				BackendModel::setModuleSetting($this->getModule(), 'sitemap_images_items', (int) $this->frm->getField('sitemap_images_items')->getValue());

				// redirect to the settings page
				$this->redirect(BackendModel::createURLForAction('settings') . '&report=saved');
			}
		}
	}
}
