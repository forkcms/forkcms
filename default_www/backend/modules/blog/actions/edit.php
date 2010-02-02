<?php

/**
 * BackendBlogEdit
 *
 * This is the edit-action, it will display a form to edit an existing item
 *
 * @package		backend
 * @subpackage	blog
 *
 * @author 		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendBlogEdit extends BackendBaseActionEdit
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
		if(BackendBlogModel::exists($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get all data for the item we want to edit
			$this->getData();

			// load the datagrid with revisions
			$this->loadRevisions();

			// load the form
			$this->loadForm();

			// validate the form
			$this->validateForm();

			// parse the datagrid
			$this->parse();

			// display the page
			$this->display();
		}

		// no item found, throw an exceptions, because somebody is fucking with our URL
		else $this->redirect(BackendModel::createURLForAction('index') .'&error=non-existing');
	}


	/**
	 * Get the data
	 * If a revision-id was specified in the URL we load the revision and not the actual data
	 *
	 * @return	void
	 */
	private function getData()
	{
		// get the record
		$this->record = (array) BackendBlogModel::get($this->id);

		// get categories
		$this->categories = BackendBlogModel::getCategories();

		// get users
		$this->users = BackendUsersModel::getUsers();

		// is there a revision specified?
		$revisionToLoad = $this->getParameter('revision', 'int');

		// if this is a valid revision
		if($revisionToLoad !== null)
		{
			// overwrite the current record
			$this->record = (array) BackendBlogModel::getRevision($this->id, $revisionToLoad);

			// show warning
			$this->tpl->assign('usingRevision', true);
		}
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
		$rbtHiddenValues[] = array('label' => BL::getLabel('Hidden'), 'value' => 'Y');
		$rbtHiddenValues[] = array('label' => BL::getLabel('Published'), 'value' => 'N');

		// create elements
		$this->frm->addTextField('title', $this->record['title']);
		$this->frm->addEditorField('text', $this->record['text']);
		$this->frm->addEditorField('introduction', $this->record['introduction']);
		$this->frm->addRadioButton('hidden', $rbtHiddenValues, $this->record['hidden']);
		$this->frm->addCheckBox('allow_comments', ($this->record['allow_comments'] === 'Y' ? true : false));
		$this->frm->addDropDown('category_id', $this->categories, $this->record['category_id']);
		$this->frm->addDropDown('user_id', $this->users, $this->record['user_id']);
		$this->frm->addTextField('tags', BackendTagsModel::getTags($this->URL->getModule(), $this->id), null, 'inputTextfield tagBox', 'inputTextfieldError tagBox');
		$this->frm->addDateField('publish_on_date', $this->record['publish_on']);
		$this->frm->addTimeField('publish_on_time', SpoonDate::getDate('H:i', $this->record['publish_on']));

		// meta object
		$this->meta = new BackendMeta($this->frm, $this->record['meta_id'], 'title', true);

		// add button
		$this->frm->addButton('edit', ucfirst(BL::getLabel('Edit')), 'submit', 'inputButton button mainButton');
		$this->frm->addButton('publish', ucfirst(BL::getLabel('Publish')), 'submit', 'inputButton button mainButton');
		$this->frm->addButton('preview', ucfirst(BL::getLabel('Preview')), 'submit', 'inputButton button previewButton');
		$this->frm->addButton('save', ucfirst(BL::getLabel('Save')), 'submit', 'inputButton button saveButton');
	}


	/**
	 * Load the datagrid with revisions
	 *
	 * @return	void
	 */
	private function loadRevisions()
	{
		// create datagrid
		$this->dgRevisions = new BackendDataGridDB(BackendBlogModel::QRY_DATAGRID_BROWSE_REVISIONS, array('archived', $this->record['id']));

		// hide columns
		$this->dgRevisions->setColumnsHidden(array('id', 'revision_id'));

		// disable paging
		$this->dgRevisions->setPaging(false);

		// set headers
		$this->dgRevisions->setHeaderLabels(array('title' => BL::getLabel('Title'), 'edited_on' => BL::getLabel('LastEditedOn')));

		// set column-functions
		$this->dgRevisions->setColumnFunction(array('BackendDataGridFunctions', 'getLongDate'), array('[edited_on]'), 'edited_on', true);

		// add use column
		$this->dgRevisions->addColumn('use', null, BL::getLabel('UseThisVersion'), BackendModel::createURLForAction('edit') .'&id=[id]&revision=[revision_id]', BL::getLabel('Edit'));
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
		$this->tpl->assign('blog', $this->record);
		$this->tpl->assign('blogUrl', SITE_URL . BackendModel::getURLForBlock('blog', 'detail'));
		$this->tpl->assign('status', BL::getLabel(ucfirst($this->record['status'])));

		// show the summary
		if(!empty($this->record['introduction']) || $this->frm->getField('introduction')->isFilled()) $this->tpl->assign('oShowSummary', true);

		// assign revisions-datagrid
		$this->tpl->assign('revisions', ($this->dgRevisions->getNumResults() != 0) ? $this->dgRevisions->getContent() : false);
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

			// shorten fields
			$txtTitle = $this->frm->getField('title');
			$txtIntroduction = $this->frm->getField('introduction');
			$txtText = $this->frm->getField('text');
			$txtPublishDate = $this->frm->getField('publish_on_date');
			$txtPublishTime = $this->frm->getField('publish_on_time');
			$ddmUserId = $this->frm->getField('user_id');
			$ddmCategoryId = $this->frm->getField('category_id');
			$rbtHidden = $this->frm->getField('hidden');
			$chkAllowComments = $this->frm->getField('allow_comments');

			// validate fields
			$this->frm->getField('title')->isFilled(BL::getError('TitleIsRequired'));
			$this->frm->getField('text')->isFilled(BL::getError('TextIsRequired'));

			// validate meta
			$this->meta->validate();

			// no errors?
			if($this->frm->isCorrect())
			{
				// set callback for generating an unique URL
				$this->meta->setURLCallback('BackendBlogModel', 'getURL', array($txtTitle->getValue()));

				// set formatted date and time
				$formattedDate = SpoonDate::getDate('Y-m-d', $txtPublishDate->getTimestamp());
				$formattedTime = SpoonDate::getDate('H:i:s', strtotime($txtPublishTime->getValue())); // @todo switch this to $txtPublishTime->getTimestamp whenever it is available

				// build item
				$item['meta_id'] = $this->meta->save(true);
				$item['category_id'] = $ddmCategoryId->getValue();
				$item['user_id'] = $ddmUserId->getValue();
				$item['language'] = BL::getWorkingLanguage();
				$item['title'] = $txtTitle->getValue();
				$item['introduction'] = $txtIntroduction->getValue();
				$item['text'] = $txtText->getValue();
				$item['publish_on'] = $formattedDate.' '.$formattedTime; // @todo davy - dit moet nog geswitched worden naar de correcte UTC tijd + testen of het effectief 1 uur teruggaat.
				$item['created_on'] = BackendModel::getUTCDate('Y-m-d H:i:s', $this->record['created_on']);
				$item['hidden'] = $rbtHidden->getValue();
				$item['allow_comments'] = $chkAllowComments->getChecked() ? 'Y' : 'N';
				$item['num_comments'] = 0;

				// insert the item
				$id = (int) BackendBlogModel::update($this->id, $item);

				// save the tags
				BackendTagsModel::saveTags($id, $this->frm->getField('tags')->getValue(), $this->URL->getModule());

				// ping
				BackendModel::ping(SITE_URL . BackendModel::getURLForBlock('blog', 'detail') .'/'. $this->meta->getURL());

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') .'&report=edited&var='. urlencode($item['title']) .'&highlight=id-'. $id);
			}
		}
	}
}

?>