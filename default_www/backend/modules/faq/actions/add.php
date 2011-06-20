<?php

/**
 * This is the add-action, it will display a form to create a new item
 *
 * @package		backend
 * @subpackage	faq
 *
 * @author		Lester Lievens <lester@netlash.com>
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.1
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
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// get all data
		$this->getData();

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
	 * Get the data for a question
	 *
	 * @return	void
	 */
	private function getData()
	{
		// get categories
		$this->categories = BackendFaqModel::getCategoriesForDropdown();
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

		// set hidden values
		$rbtHiddenValues[] = array('label' => BL::lbl('Hidden', $this->URL->getModule()), 'value' => 'Y');
		$rbtHiddenValues[] = array('label' => BL::lbl('Published'), 'value' => 'N');

		// create elements
		$this->frm->addText('question')->setAttribute('id', 'title');
		$this->frm->getField('question')->setAttribute('class', 'title ' . $this->frm->getField('question')->getAttribute('class'));
		$this->frm->addEditor('answer');
		$this->frm->addDropdown('categories', $this->categories);
		$this->frm->addRadiobutton('hidden', $rbtHiddenValues, 'N');
	}


	/**
	 * Parse the form
	 *
	 * @return	void
	 */
	protected function parse()
	{
		// call parent
		parent::parse();

		// assign categories
		$this->tpl->assign('categories', $this->categories);
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
			$this->frm->getField('question')->isFilled(BL::err('QuestionIsRequired'));
			$this->frm->getField('answer')->isFilled(BL::err('AnswerIsRequired'));
			$this->frm->getField('categories')->isFilled(BL::err('CategoryIsRequired'));

			// no errors?
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

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') . '&report=added&var=' . urlencode($item['question']) . '&highlight=row-' . $item['id']);
			}
		}
	}
}

?>