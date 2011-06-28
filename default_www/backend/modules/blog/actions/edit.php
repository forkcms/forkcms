<?php

/**
 * This is the edit-action, it will display a form to edit an existing item
 *
 * @package		backend
 * @subpackage	blog
 *
 * @author		Dave Lens <dave@netlash.com>
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Matthias Mullie <matthias@netlash.com>
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.0
 */
class BackendBlogEdit extends BackendBaseActionEdit
{
	/**
	 * The id of the category where is filtered on
	 *
	 * @var	int
	 */
	private $categoryId;


	/**
	 * DataGrid for the drafts
	 *
	 * @var	BackendDataGrid
	 */
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
		if($this->id !== null && BackendBlogModel::exists($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// set category id
			$this->categoryId = SpoonFilter::getGetValue('category', null, null, 'int');
			if($this->categoryId == 0) $this->categoryId = null;

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
		$this->record = (array) BackendBlogModel::get($this->id);

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
			$this->record = (array) BackendBlogModel::getRevision($this->id, $draftToLoad);

			// show warning
			$this->tpl->assign('usingDraft', true);

			// assign draft
			$this->tpl->assign('draftId', $draftToLoad);
		}

		// no item found, throw an exceptions, because somebody is fucking with our URL
		if(empty($this->record)) $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}


	/**
	 * Load the datagrid with drafts
	 *
	 * @return	void
	 */
	private function loadDrafts()
	{
		// create datagrid
		$this->dgDrafts = new BackendDataGridDB(BackendBlogModel::QRY_DATAGRID_BROWSE_SPECIFIC_DRAFTS, array('draft', $this->record['id'], BL::getWorkingLanguage()));

		// hide columns
		$this->dgDrafts->setColumnsHidden(array('id', 'revision_id'));

		// disable paging
		$this->dgDrafts->setPaging(false);

		// set headers
		$this->dgDrafts->setHeaderLabels(array('user_id' => ucfirst(BL::lbl('By')), 'edited_on' => ucfirst(BL::lbl('LastEditedOn'))));

		// set colum URLs
		$this->dgDrafts->setColumnURL('title', BackendModel::createURLForAction('edit') . '&amp;id=[id]&amp;draft=[revision_id]');

		// set column-functions
		$this->dgDrafts->setColumnFunction(array('BackendDataGridFunctions', 'getUser'), array('[user_id]'), 'user_id');
		$this->dgDrafts->setColumnFunction(array('BackendDataGridFunctions', 'getTimeAgo'), array('[edited_on]'), 'edited_on');

		// add use column
		$this->dgDrafts->addColumn('use_draft', null, ucfirst(BL::lbl('UseThisDraft')), BackendModel::createURLForAction('edit') . '&amp;id=[id]&amp;draft=[revision_id]', BL::lbl('UseThisDraft'));

		// our JS needs to know an id, so we can highlight it
		$this->dgDrafts->setRowAttributes(array('id' => 'row-[revision_id]'));
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
		$categories = BackendBlogModel::getCategories();
		$categories['new_category'] = ucfirst(BL::getLabel('AddCategory'));

		// create elements
		$this->frm->addText('title', $this->record['title'], null, 'inputText title', 'inputTextError title');
		$this->frm->addEditor('text', $this->record['text']);
		$this->frm->addEditor('introduction', $this->record['introduction']);
		$this->frm->addRadiobutton('hidden', $rbtHiddenValues, $this->record['hidden']);
		$this->frm->addCheckbox('allow_comments', ($this->record['allow_comments'] === 'Y' ? true : false));
		$this->frm->addDropdown('category_id', $categories, $this->record['category_id']);
		if(count($categories) != 2) $this->frm->getField('category_id')->setDefaultElement('');
		$this->frm->addDropdown('user_id', BackendUsersModel::getUsers(), $this->record['user_id']);
		$this->frm->addText('tags', BackendTagsModel::getTags($this->URL->getModule(), $this->record['id']), null, 'inputText tagBox', 'inputTextError tagBox');
		$this->frm->addDate('publish_on_date', $this->record['publish_on']);
		$this->frm->addTime('publish_on_time', date('H:i', $this->record['publish_on']));

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
		$this->dgRevisions = new BackendDataGridDB(BackendBlogModel::QRY_DATAGRID_BROWSE_REVISIONS, array('archived', $this->record['id'], BL::getWorkingLanguage()));

		// hide columns
		$this->dgRevisions->setColumnsHidden(array('id', 'revision_id'));

		// disable paging
		$this->dgRevisions->setPaging(false);

		// set headers
		$this->dgRevisions->setHeaderLabels(array('user_id' => ucfirst(BL::lbl('By')), 'edited_on' => ucfirst(BL::lbl('LastEditedOn'))));

		// set colum URLs
		$this->dgRevisions->setColumnURL('title', BackendModel::createURLForAction('edit') . '&amp;id=[id]&amp;revision=[revision_id]');

		// set column-functions
		$this->dgRevisions->setColumnFunction(array('BackendDataGridFunctions', 'getUser'), array('[user_id]'), 'user_id');
		$this->dgRevisions->setColumnFunction(array('BackendDataGridFunctions', 'getTimeAgo'), array('[edited_on]'), 'edited_on');

		// add use column
		$this->dgRevisions->addColumn('use_revision', null, ucfirst(BL::lbl('UseThisVersion')), BackendModel::createURLForAction('edit') . '&amp;id=[id]&amp;revision=[revision_id]', BL::lbl('UseThisVersion'));
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
		$this->tpl->assign('status', BL::lbl(ucfirst($this->record['status'])));

		// assign revisions-datagrid
		$this->tpl->assign('revisions', ($this->dgRevisions->getNumResults() != 0) ? $this->dgRevisions->getContent() : false);
		$this->tpl->assign('drafts', ($this->dgDrafts->getNumResults() != 0) ? $this->dgDrafts->getContent() : false);

		// assign category
		if($this->categoryId !== null) $this->tpl->assign('categoryId', $this->categoryId);
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

			// get the status
			$status = SpoonFilter::getPostValue('status', array('active', 'draft'), 'active');

			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// validate fields
			$this->frm->getField('title')->isFilled(BL::err('TitleIsRequired'));
			$this->frm->getField('text')->isFilled(BL::err('FieldIsRequired'));
			$this->frm->getField('publish_on_date')->isValid(BL::err('DateIsInvalid'));
			$this->frm->getField('publish_on_time')->isValid(BL::err('TimeIsInvalid'));
			$this->frm->getField('category_id')->isFilled(BL::err('FieldIsRequired'));

			// validate meta
			$this->meta->validate();

			// no errors?
			if($this->frm->isCorrect())
			{
				// build item
				$item['id'] = $this->id;
				$item['revision_id'] = $this->record['revision_id']; // this is used to let our model know the status (active, archive, draft) of the edited item
				$item['meta_id'] = $this->meta->save();
				$item['category_id'] = (int) $this->frm->getField('category_id')->getValue();
				$item['user_id'] = $this->frm->getField('user_id')->getValue();
				$item['language'] = BL::getWorkingLanguage();
				$item['title'] = $this->frm->getField('title')->getValue();
				$item['introduction'] = $this->frm->getField('introduction')->getValue();
				$item['text'] = $this->frm->getField('text')->getValue();
				$item['publish_on'] = BackendModel::getUTCDate(null, BackendModel::getUTCTimestamp($this->frm->getField('publish_on_date'), $this->frm->getField('publish_on_time')));
				$item['edited_on'] = BackendModel::getUTCDate();
				$item['hidden'] = $this->frm->getField('hidden')->getValue();
				$item['allow_comments'] = $this->frm->getField('allow_comments')->getChecked() ? 'Y' : 'N';
				$item['status'] = $status;

				// update the item
				$item['revision_id'] = BackendBlogModel::update($item);

				// recalculate comment count so the new revision has the correct count
				BackendBlogModel::reCalculateCommentCount(array($this->id));

				// save the tags
				BackendTagsModel::saveTags($item['id'], $this->frm->getField('tags')->getValue(), $this->URL->getModule());

				// active
				if($item['status'] == 'active')
				{
					// edit search index
					if(is_callable(array('BackendSearchModel', 'editIndex'))) BackendSearchModel::editIndex('blog', $item['id'], array('title' => $item['title'], 'text' => $item['text']));

					// ping
					if(BackendModel::getModuleSetting($this->URL->getModule(), 'ping_services', false)) BackendModel::ping(SITE_URL . BackendModel::getURLForBlock($this->URL->getModule(), 'detail') . '/' . $this->meta->getURL());

					// build URL
					$redirectUrl = BackendModel::createURLForAction('index') . '&report=edited&var=' . urlencode($item['title']) . '&id=' . $this->id . '&highlight=row-' . $item['revision_id'];
				}

				// draft
				elseif($item['status'] == 'draft')
				{
					// everything is saved, so redirect to the edit action
					$redirectUrl = BackendModel::createURLForAction('edit') . '&report=saved-as-draft&var=' . urlencode($item['title']) . '&id=' . $item['id'] . '&draft=' . $item['revision_id'] . '&highlight=row-' . $item['revision_id'];
				}

				// append to redirect URL
				if($this->categoryId != null) $redirectUrl .= '&category=' . $this->categoryId;

				// everything is saved, so redirect to the overview
				$this->redirect($redirectUrl);
			}
		}
	}
}

?>