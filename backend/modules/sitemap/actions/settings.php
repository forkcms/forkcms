<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the settings-action, it will display a form to set general faq settings
 *
 * @author Jonas Goderis <jonas.goderis@wijs.be>
 */
class BackendSitemapSettings extends BackendBaseActionEdit
{
	/**
	 * @var BackendSitemapGenerator
	 */
	private $sitemap;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		$this->loadData();
		$this->loadForm();
		$this->validateForm();

		$this->parse();
		$this->display();
	}

	/**
	 * Load the data
	 */
	private function loadData()
	{
		$this->sitemap = new BackendSitemapGenerator();
	}

	/**
	 * Loads the settings form
	 */
	private function loadForm()
	{
		// init settings form
		$this->frm = new BackendForm('settings');
	}

	protected function parse()
	{
		parent::parse();

		// assign some usefull data
		$this->tpl->assign('nonImplementedModules', $this->sitemap->getNonImplementedModules());
		$this->tpl->assign('indexes', $this->sitemap->getIndexesNames());
	}

	/**
	 * Validates the settings form
	 */
	private function validateForm()
	{
		if($this->frm->isSubmitted())
		{
			if($this->frm->isCorrect())
			{
				// save xml files
				$this->sitemap->saveXml();

				// redirect to the settings page
				$this->redirect(BackendModel::createURLForAction('settings') . '&report=saved');
			}
		}
	}
}
