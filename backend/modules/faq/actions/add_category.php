<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the add-action, it will display a form to create a new category
 *
 * @author Lester Lievens <lester.lievens@netlash.com>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 * @author SIESQO <info@siesqo.be>
 */
class BackendFaqAddCategory extends BackendBaseActionAdd
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// only one category allowed, so we redirect
		if(!BackendModel::getModuleSetting('faq', 'allow_multiple_categories', true)) $this->redirect(BackendModel::createURLForAction('categories') . '&error=only-one-category-allowed');

		parent::execute();
		$this->loadForm();
		$this->validateForm();
		$this->parse();
		$this->display();
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		$this->frm = new BackendForm('addCategory');
		$this->frm->addText('title');

		$this->meta = new BackendMeta($this->frm, null, 'title', true);
	}

	/**
	 * Validate the form
	 */
	private function validateForm()
	{
		if($this->frm->isSubmitted())
		{
			$this->meta->setURLCallback('BackendFaqModel', 'getURLForCategory');

			$this->frm->cleanupFields();

			// validate fields
			$this->frm->getField('title')->isFilled(BL::err('TitleIsRequired'));
			$this->meta->validate();

			if($this->frm->isCorrect())
			{
				// build item
				$item['title'] = $this->frm->getField('title')->getValue();
				$item['language'] = BL::getWorkingLanguage();
				$item['meta_id'] = $this->meta->save();
				$item['sequence'] = BackendFaqModel::getMaximumCategorySequence() + 1;

				// save the data
				$item['id'] = BackendFaqModel::insertCategory($item);
				BackendModel::triggerEvent($this->getModule(), 'after_add_category', array('item' => $item));

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('categories') . '&report=added-category&var=' . urlencode($item['title']) . '&highlight=row-' . $item['id']);
			}
		}
	}
}
