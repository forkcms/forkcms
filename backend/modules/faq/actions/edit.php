<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the edit-action, it will display a form to edit an existing item
 *
 * @author Lester Lievens <lester@netlash.com>
 * @author Matthias Mullie <matthias@mullie.eu>
 */
class BackendFaqEdit extends BackendBaseActionEdit
{
	/**
	 * The available categories
	 *
	 * @var	array
	 */
	private $categories;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the item exists
		if($this->id !== null && BackendFaqModel::existsQuestion($this->id))
		{
			parent::execute();
			$this->getData();
			$this->loadForm();
			$this->validateForm();
			$this->parse();
			$this->display();
		}

		// no item found, throw an exception, because somebody is fucking with our URL
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}

	/**
	 * Get the data for a question
	 */
	private function getData()
	{
		$this->record = BackendFaqModel::getQuestion($this->id);
		$this->categories = BackendFaqModel::getCategoriesForDropdown();
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		$this->frm = new BackendForm('edit');

		// set hidden values
		$rbtHiddenValues[] = array('label' => BL::lbl('Hidden'), 'value' => 'Y');
		$rbtHiddenValues[] = array('label' => BL::lbl('Published'), 'value' => 'N');

		$this->frm->addText('question', $this->record['question'])->setAttribute('id', 'title');
		$this->frm->getField('question')->setAttribute('class', 'title ' . $this->frm->getField('question')->getAttribute('class'));
		$this->frm->addEditor('answer', $this->record['answer']);
		$this->frm->addDropdown('categories', $this->categories, $this->record['category_id']);
		$this->frm->addRadiobutton('hidden', $rbtHiddenValues, $this->record['hidden']);
	}

	/**
	 * Parse the form
	 */
	protected function parse()
	{
		parent::parse();

		$this->tpl->assign('item', $this->record);
		$this->tpl->assign('categories', $this->categories);
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
			$this->frm->getField('question')->isFilled(BL::err('QuestionIsRequired'));
			$this->frm->getField('answer')->isFilled(BL::err('AnswerIsRequired'));
			$this->frm->getField('categories')->isFilled(BL::err('CategoryIsRequired'));

			if($this->frm->isCorrect())
			{
				// build item
				$item['id'] = $this->id;
				$item['language'] = $this->record['language'];
				$item['category_id'] = $this->frm->getField('categories')->getValue();
				$item['question'] = $this->frm->getField('question')->getValue();
				$item['answer'] = $this->frm->getField('answer')->getValue(true);
				$item['hidden'] = $this->frm->getField('hidden')->getValue();

				// update question values in database
				BackendFaqModel::updateQuestion($item);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_edit', array('item' => $item));

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') . '&report=saved&var=' . urlencode($item['question']) . '&highlight=row-' . $item['id']);
			}
		}
	}
}
