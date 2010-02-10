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
	private $dgDrafts;


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

			// load drafts
			$this->loadDrafts();

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

		// is there a revision specified?
		$draftToLoad = $this->getParameter('draft', 'int');

		// if this is a valid revision
		if($draftToLoad !== null)
		{
			// overwrite the current record
			$this->record = (array) BackendBlogModel::getDraft($this->id, $draftToLoad);

			// show warning
			$this->tpl->assign('usingDraft', true);

			// assign draft
			$this->tpl->assign('draftId', $draftToLoad);
		}
	}


	/**
	 * Load the datagrid with drafts
	 *
	 * @return	void
	 */
	private function loadDrafts()
	{
		// create datagrid
		$this->dgDrafts = new BackendDataGridDB(BackendBlogModel::QRY_DATAGRID_BROWSE_DRAFTS, array('draft', $this->record['id'], BackendAuthentication::getUser()->getUserId()));

		// hide columns
		$this->dgDrafts->setColumnsHidden(array('id', 'revision_id'));

		// disable paging
		$this->dgDrafts->setPaging(false);

		// set headers
		$this->dgDrafts->setHeaderLabels(array('title' => BL::getLabel('Title'), 'edited_on' => BL::getLabel('LastEditedOn')));

		// set column-functions
		$this->dgDrafts->setColumnFunction(array('BackendDataGridFunctions', 'getLongDate'), array('[edited_on]'), 'edited_on', true);

		// add use column
		$this->dgDrafts->addColumn('use_draft', null, ucfirst(BL::getLabel('UseThisdraft')), BackendModel::createURLForAction('edit') .'&id=[id]&draft=[revision_id]', BL::getLabel('UseThisDraft'));
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
		$this->dgRevisions->addColumn('use_revision', null, ucfirst(BL::getLabel('UseThisVersion')), BackendModel::createURLForAction('edit') .'&id=[id]&revision=[revision_id]', BL::getLabel('UseThisVersion'));
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
		$this->tpl->assign('drafts', ($this->dgDrafts->getNumResults() != 0) ? $this->dgDrafts->getContent() : false);
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
			$this->meta->setUrlCallback('BackendBlogModel', 'getURL', array($this->record['id']));

			$status = SpoonFilter::getPostValue('status', array('active', 'draft'), 'active');

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
				// build item
				$item['meta_id'] = $this->meta->save();
				$item['category_id'] = $ddmCategoryId->getValue();
				$item['user_id'] = $ddmUserId->getValue();
				$item['language'] = BL::getWorkingLanguage();
				$item['title'] = $txtTitle->getValue();
				$item['introduction'] = $txtIntroduction->getValue();
				$item['text'] = $txtText->getValue();
				$item['publish_on'] = BackendModel::getUTCDate(null, BackendModel::getUTCTimestamp($txtPublishDate, $txtPublishTime));
				$item['created_on'] = BackendModel::getUTCDate(null, $this->record['created_on']);
				$item['hidden'] = $rbtHidden->getValue();
				$item['allow_comments'] = $chkAllowComments->getChecked() ? 'Y' : 'N';
				$item['num_comments'] = 0;
				$item['status'] = $status;

				// active
				if($status == 'active')
				{
					// insert the item
					$id = (int) BackendBlogModel::update($this->id, $item);

					// save the tags
					BackendTagsModel::saveTags($id, $this->frm->getField('tags')->getValue(), $this->URL->getModule());

					// ping
					if(BackendModel::getSetting('blog', 'ping_services', false)) BackendModel::ping(SITE_URL . BackendModel::getURLForBlock('blog', 'detail') .'/'. $this->meta->getURL());

					// everything is saved, so redirect to the overview
					$this->redirect(BackendModel::createURLForAction('index') .'&report=edited&var='. urlencode($item['title']) .'&highlight=id-'. $id);
				}

				// draft
				else
				{
					// insert the item
					$id = (int) BackendBlogModel::insertDraft($this->id, $item);

					// save the tags
					BackendTagsModel::saveTags($id, $this->frm->getField('tags')->getValue(), $this->URL->getModule());

					// everything is saved, so redirect to the overview
					$this->redirect(BackendModel::createURLForAction('edit') .'&id='. $this->id .'&draft='. $id .'&report=saved_as_draft&var='. urlencode($item['title']) .'&highlight=id-'. $id);
				}
			}
		}
	}
}

?>