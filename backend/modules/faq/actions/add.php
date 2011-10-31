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
 * @author Lester Lievens <lester@netlash.com>
 * @author Matthias Mullie <matthias@mullie.eu>
 */
class BackendFaqAdd extends BackendBaseActionAdd
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
		parent::execute();
		$this->getData();
		$this->loadForm();
		$this->validateForm();
		$this->parse();
		$this->display();
	}

	/**
	 * Get the data for a question
	 */
	private function getData()
	{
		$this->categories = BackendFaqModel::getCategoriesForDropdown();
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		$this->frm = new BackendForm('add');

		// set hidden values
		$rbtHiddenValues[] = array('label' => BL::lbl('Hidden', $this->URL->getModule()), 'value' => 'Y');
		$rbtHiddenValues[] = array('label' => BL::lbl('Published'), 'value' => 'N');

		$this->frm->addText('question')->setAttribute('id', 'title');
		$this->frm->getField('question')->setAttribute('class', 'title ' . $this->frm->getField('question')->getAttribute('class'));
		$this->frm->addEditor('answer');
		$this->frm->addDropdown('categories', $this->categories);
		$this->frm->addRadiobutton('hidden', $rbtHiddenValues, 'N');
	}

	/**
	 * Parse the form
	 */
	protected function parse()
	{
		parent::parse();
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
				$item['user_id'] = BackendAuthentication::getUser()->getUserId();
				$item['category_id'] = $this->frm->getField('categories')->getValue();
				$item['language'] = BL::getWorkingLanguage();
				$item['question'] = $this->frm->getField('question')->getValue();
				$item['answer'] = $this->frm->getField('answer')->getValue(true);
				$item['hidden'] = $this->frm->getField('hidden')->getValue();
				$item['sequence'] = BackendFaqModel::getMaximumQuestionSequence($this->frm->getField('categories')->getValue()) + 1;
				$item['created_on'] = BackendModel::getUTCDate();

				// insert the item
				$item['id'] = BackendFaqModel::insertQuestion($item);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_add', array('item' => $item));

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') . '&report=added&var=' . urlencode($item['question']) . '&highlight=row-' . $item['id']);
			}
		}
	}
}
