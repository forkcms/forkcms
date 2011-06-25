<?php

/**
 * This is the edit-action, it will display a form to edit an existing item
 *
 * @package		backend
 * @subpackage	blog
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendBlogEditComment extends BackendBaseActionEdit
{
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
		if($this->id !== null && BackendBlogModel::existsComment($this->id))
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
	 * Get the data
	 * If a revision-id was specified in the URL we load the revision and not the actual data.
	 *
	 * @return	void
	 */
	private function getData()
	{
		// get the record
		$this->record = (array) BackendBlogModel::getComment($this->id);

		// no item found, throw an exceptions, because somebody is fucking with our URL
		if(empty($this->record)) $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('editComment');

		// create elements
		$this->frm->addText('author', $this->record['author']);
		$this->frm->addText('email', $this->record['email']);
		$this->frm->addText('website', $this->record['website'], null);
		$this->frm->addTextarea('text', $this->record['text']);

		// assign URL
		$this->tpl->assign('itemURL', BackendModel::getURLForBlock('blog', 'detail') . '/' . $this->record['post_url'] . '#comment-' . $this->record['post_id']);
		$this->tpl->assign('itemTitle', $this->record['post_title']);
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
			$this->frm->getField('author')->isFilled(BL::err('AuthorIsRequired'));
			$this->frm->getField('email')->isEmail(BL::err('EmailIsInvalid'));
			$this->frm->getField('text')->isFilled(BL::err('FieldIsRequired'));
			if($this->frm->getField('website')->isFilled()) $this->frm->getField('website')->isURL(BL::err('InvalidURL'));

			// no errors?
			if($this->frm->isCorrect())
			{
				// build item
				$item['id'] = $this->id;
				$item['status'] = $this->record['status'];
				$item['author'] = $this->frm->getField('author')->getValue();
				$item['email'] = $this->frm->getField('email')->getValue();
				$item['website'] = ($this->frm->getField('website')->isFilled()) ? $this->frm->getField('website')->getValue() : null;
				$item['text'] = $this->frm->getField('text')->getValue();

				// insert the item
				BackendBlogModel::updateComment($item);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('comments') . '&report=edited-comment&id=' . $item['id'] . '&highlight=row-' . $item['id'] . '#tab' . SpoonFilter::toCamelCase($item['status']));
			}
		}
	}
}

?>