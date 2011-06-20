<?php

/**
 * This is the edit-action, it will display a form to edit an existing item
 *
 * @package		backend
 * @subpackage	faq
 *
 * @author		Lester Lievens <lester@netlash.com>
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.1
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
	 *
	 * @return	void
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the item exists
		if($this->id !== null && BackendFaqModel::existsQuestion($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get all data for the item we want to edit
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

		// no item found, throw an exception, because somebody is fucking with our URL
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}


	/**
	 * Get the data for a question
	 *
	 * @return	void
	 */
	private function getData()
	{
		// get the record
		$this->record = BackendFaqModel::getQuestion($this->id);

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
		$this->frm = new BackendForm('edit');

		// set hidden values
		$rbtHiddenValues[] = array('label' => BL::lbl('Hidden'), 'value' => 'Y');
		$rbtHiddenValues[] = array('label' => BL::lbl('Published'), 'value' => 'N');

		// create elements
		$this->frm->addText('question', $this->record['question'])->setAttribute('id', 'title');
		$this->frm->getField('question')->setAttribute('class', 'title ' . $this->frm->getField('question')->getAttribute('class'));
		$this->frm->addEditor('answer', $this->record['answer']);
		$this->frm->addDropdown('categories', $this->categories, $this->record['category_id']);
		$this->frm->addRadiobutton('hidden', $rbtHiddenValues, $this->record['hidden']);
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

		// assign the active record and additional variables
		$this->tpl->assign('item', $this->record);

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
				$item['id'] = $this->id;
				$item['language'] = $this->record['language'];
				$item['category_id'] = $this->frm->getField('categories')->getValue();
				$item['question'] = $this->frm->getField('question')->getValue();
				$item['answer'] = $this->frm->getField('answer')->getValue(true);
				$item['hidden'] = $this->frm->getField('hidden')->getValue();

				// update question values in database
				BackendFaqModel::updateQuestion($item);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') . '&report=saved&var=' . urlencode($item['question']) . '&highlight=row-' . $item['id']);
			}
		}
	}
}

?>