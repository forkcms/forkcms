<?php

/**
 * This is the edit-action, it will display a form to edit an existing item
 *
 * @package		backend
 * @subpackage	faq
 *
 * @author		Lester Lievens <lester@netlash.com>
 * @author		Matthias Mullie <matthias@netlash.com>
 * @author		Annelies Van Extergem <annelies@netlash.com>
 * @author		Jelmer Snoeck <jelmer.snoeck@netlash.com>
 * @since		2.1
 */
class BackendFaqEdit extends BackendBaseActionEdit
{
	/**
	 * Feedback
	 *
	 * @var	array
	 */
	private $feedback;


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
		if($this->id !== null && BackendFaqModel::exists($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get all data for the item we want to edit
			$this->getData();

			// load the form
			$this->loadForm();

			// validate the form
			$this->validateForm();

			// parse the dataGrid
			$this->parse();

			// display the page
			$this->display();
		}

		// no item found, throw an exception, because somebody is fucking with our URL
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}


	/**
	 * Get the data
	 *
	 * @return	void
	 */
	private function getData()
	{
		// get the record
		$this->record = (array) BackendFaqModel::get($this->id);

		// the feedback
		$this->feedback = BackendFaqModel::getAllFeedback($this->id);
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

		// get categories
		$categories = BackendFaqModel::getCategories();

		// create elements
		$this->frm->addText('title', $this->record['question'], null, 'inputText title', 'inputTextError title');
		$this->frm->addEditor('answer', $this->record['answer']);
		$this->frm->addRadiobutton('hidden', $rbtHiddenValues, $this->record['hidden']);
		$this->frm->addDropdown('category_id', $categories, $this->record['category_id']);
		$this->frm->addText('tags', BackendTagsModel::getTags($this->URL->getModule(), $this->record['id']), null, 'inputText tagBox', 'inputTextError tagBox');

		// meta object
		$this->meta = new BackendMeta($this->frm, $this->record['meta_id'], 'title', true);
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

		// get url
		$url = BackendModel::getURLForBlock($this->URL->getModule(), 'detail');
		$url404 = BackendModel::getURL(404);

		// parse additional variables
		if($url404 != $url) $this->tpl->assign('detailURL', SITE_URL . $url);

		// assign the active record and additional variables
		$this->tpl->assign('item', $this->record);
		$this->tpl->assign('feedback', $this->feedback);
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
			// set callback for generating an unique URL
			$this->meta->setUrlCallback('BackendFaqModel', 'getURL', array($this->record['id']));

			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// validate fields
			$this->frm->getField('title')->isFilled(BL::err('QuestionIsRequired'));
			$this->frm->getField('answer')->isFilled(BL::err('AnswerIsRequired'));
			$this->frm->getField('category_id')->isFilled(BL::err('CategoryIsRequired'));

			// validate meta
			$this->meta->validate();

			// no errors?
			if($this->frm->isCorrect())
			{
				// build item
				$item['id'] = $this->id;
				$item['meta_id'] = $this->meta->save(true);
				$item['category_id'] = $this->frm->getField('category_id')->getValue();
				$item['language'] = $this->record['language'];
				$item['question'] = $this->frm->getField('title')->getValue();
				$item['answer'] = $this->frm->getField('answer')->getValue(true);
				$item['hidden'] = $this->frm->getField('hidden')->getValue();

				// update the item
				BackendFaqModel::update($item);

				// save the tags
				BackendTagsModel::saveTags($item['id'], $this->frm->getField('tags')->getValue(), $this->URL->getModule());

				// edit search index
				if(is_callable(array('BackendSearchModel', 'editIndex'))) BackendSearchModel::editIndex('faq', $item['id'], array('title' => $item['question'], 'text' => $item['answer']));

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') . '&report=saved&var=' . urlencode($item['question']) . '&highlight=row-' . $item['id']);
			}
		}
	}
}

?>