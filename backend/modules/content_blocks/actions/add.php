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
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Matthias Mullie <forkcms@mullie.eu>
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
	 */
	public function execute()
	{
		parent::execute();
		$this->templates = BackendContentBlocksModel::getTemplates();
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
		$this->frm = new BackendForm('add');
		$this->frm->addText('title', null, null, 'inputText title', 'inputTextError title');
		$this->frm->addEditor('text');
		$this->frm->addCheckbox('hidden', true);

		// if we have multiple templates, add a dropdown to select them
		if(count($this->templates) > 1)
		{
			$this->frm->addDropdown('template', array_combine($this->templates, $this->templates));
		}
	}

	/**
	 * Validate the form
	 */
	private function validateForm()
	{
		if($this->frm->isSubmitted())
		{
			$this->frm->cleanupFields();

			// validate fields
			$this->frm->getField('title')->isFilled(BL::err('TitleIsRequired'));

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

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_add', array('item' => $item));

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') . '&report=added&var=' . urlencode($item['title']) . '&highlight=row-' . $item['id']);
			}
		}
	}
}
