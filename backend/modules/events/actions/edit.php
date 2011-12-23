<?php

/**
 * This is the edit-action, it will display a form to edit an existing item
 *
 * @package		backend
 * @subpackage	events
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.0
 */
class BackendEventsEdit extends BackendBaseActionEdit
{
	/**
	 * Datagrid for the drafts
	 *
	 * @var	BackendDatagrid
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
		if($this->id !== null && BackendEventsModel::exists($this->id))
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
		$this->record = (array) BackendEventsModel::get($this->id);

		// is there a revision specified?
		$revisionToLoad = $this->getParameter('revision', 'int');

		// if this is a valid revision
		if($revisionToLoad !== null)
		{
			// overwrite the current record
			$this->record = (array) BackendEventsModel::getRevision($this->id, $revisionToLoad);

			// show warning
			$this->tpl->assign('usingRevision', true);
		}

		// is there a revision specified?
		$draftToLoad = $this->getParameter('draft', 'int');

		// if this is a valid revision
		if($draftToLoad !== null)
		{
			// overwrite the current record
			$this->record = (array) BackendEventsModel::getRevision($this->id, $draftToLoad);

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
		$this->dgDrafts = new BackendDataGridDB(BackendEventsModel::QRY_DATAGRID_BROWSE_SPECIFIC_DRAFTS, array('draft', $this->record['id'], BL::getWorkingLanguage()));

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

		// create elements
		$this->frm->addText('title', $this->record['title'], null, 'inputText title', 'inputTextError title');
		$this->frm->addDate('starts_on_date', $this->record['starts_on']);
		$this->frm->addTime('starts_on_time', date('H:i', $this->record['starts_on']));
		$this->frm->addDate('ends_on_date', ($this->record['ends_on'] != null) ? $this->record['ends_on'] : '');
		$this->frm->addTime('ends_on_time', ($this->record['ends_on'] != null) ? date('H:i', $this->record['ends_on']) : '');
		$this->frm->addEditor('text', $this->record['text']);
		$this->frm->addImage('image');
		$this->frm->addHidden('remove_image');
		$this->frm->addEditor('introduction', $this->record['introduction']);
		$this->frm->addRadiobutton('hidden', $rbtHiddenValues, $this->record['hidden']);
		$this->frm->addCheckbox('allow_subscriptions', ($this->record['allow_subscriptions'] === 'Y'));
		$this->frm->addText('max_subscriptions', ($this->record['max_subscriptions'] != null) ? $this->record['max_subscriptions'] : '');
		$this->frm->addCheckbox('allow_comments', ($this->record['allow_comments'] === 'Y' ? true : false));
		$this->frm->addDropdown('category_id', BackendEventsModel::getCategories(), $this->record['category_id']);
		$this->frm->addDropdown('user_id', BackendUsersModel::getUsers(), $this->record['user_id']);
		$this->frm->addText('tags', BackendTagsModel::getTags($this->URL->getModule(), $this->record['revision_id']), null, 'inputText tagBox', 'inputTextError tagBox');
		$this->frm->addDate('publish_on_date', $this->record['publish_on']);
		$this->frm->addTime('publish_on_time', date('H:i', $this->record['publish_on']));
		$this->frm->addCheckbox('in_the_picture', ($this->record['in_the_picture'] == 'Y'));

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
		$this->dgRevisions = new BackendDataGridDB(BackendEventsModel::QRY_DATAGRID_BROWSE_REVISIONS, array('archived', $this->record['id'], BL::getWorkingLanguage()));

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
			$this->meta->setUrlCallback('BackendEventsModel', 'getURL', array($this->record['id']));

			// get the status
			$status = SpoonFilter::getPostValue('status', array('active', 'draft'), 'active');

			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// validate fields
			$this->frm->getField('title')->isFilled(BL::err('TitleIsRequired'));
			$this->frm->getField('starts_on_date')->isValid(BL::err('DateIsInvalid'));
			$this->frm->getField('starts_on_time')->isValid(BL::err('TimeIsInvalid'));
			if($this->frm->getField('ends_on_date')->isFilled() || $this->frm->getField('ends_on_time')->isFilled())
			{
				$this->frm->getField('ends_on_date')->isValid(BL::err('DateIsInvalid'));
				$this->frm->getField('ends_on_time')->isValid(BL::err('TimeIsInvalid'));
			}
			$this->frm->getField('text')->isFilled(BL::err('FieldIsRequired'));
			$this->frm->getField('publish_on_date')->isValid(BL::err('DateIsInvalid'));
			$this->frm->getField('publish_on_time')->isValid(BL::err('TimeIsInvalid'));
			if($this->frm->getField('max_subscriptions')->isFilled()) $this->frm->getField('max_subscriptions')->isInteger(BL::err('IntegerIsInvalid'));

			// validate meta
			$this->meta->validate();

			// no errors?
			if($this->frm->isCorrect())
			{
				// build item
				$item['id'] = $this->id;
				$item['revision_id'] = $this->record['revision_id']; // this is used to let our model know the status (active, archive, draft) of the edited item
				$item['meta_id'] = $this->meta->save();
				$item['category_id'] = $this->frm->getField('category_id')->getValue();
				$item['user_id'] = $this->frm->getField('user_id')->getValue();
				$item['language'] = BL::getWorkingLanguage();
				$item['title'] = $this->frm->getField('title')->getValue();
				$item['starts_on'] = BackendModel::getUTCDate(null, BackendModel::getUTCTimestamp($this->frm->getField('starts_on_date'), $this->frm->getField('starts_on_time')));
				$item['ends_on'] = ($this->frm->getField('ends_on_date')->isFilled() || $this->frm->getField('ends_on_time')->isFilled()) ? BackendModel::getUTCDate(null, BackendModel::getUTCTimestamp($this->frm->getField('ends_on_date'), $this->frm->getField('ends_on_time'))) : null;
				$item['introduction'] = $this->frm->getField('introduction')->getValue();
				$item['text'] = $this->frm->getField('text')->getValue();
				$item['publish_on'] = BackendModel::getUTCDate(null, BackendModel::getUTCTimestamp($this->frm->getField('publish_on_date'), $this->frm->getField('publish_on_time')));
				$item['edited_on'] = BackendModel::getUTCDate();
				$item['hidden'] = $this->frm->getField('hidden')->getValue();
				$item['allow_comments'] = $this->frm->getField('allow_comments')->getChecked() ? 'Y' : 'N';
				$item['allow_subscriptions'] = $this->frm->getField('allow_subscriptions')->getChecked() ? 'Y' : 'N';
				$item['max_subscriptions'] = ($item['allow_subscriptions'] == 'Y' && $this->frm->getField('max_subscriptions')->isFilled()) ? (int) $this->frm->getField('max_subscriptions')->getValue() : null;
				$item['status'] = $status;

				// update the item
				$item['revision_id'] = BackendEventsModel::update($item);

				// recalculate comment count so the new revision has the correct count
				BackendEventsModel::reCalculateCommentCount(array($this->id));
				BackendEventsModel::reCalculateSubscriptionCount(array($this->id));

				// save the tags
				BackendTagsModel::saveTags($item['revision_id'], $this->frm->getField('tags')->getValue(), $this->URL->getModule());

				// active
				if($item['status'] == 'active')
				{
					// edit search index
					if(method_exists('BackendSearchModel', 'editIndex')) BackendSearchModel::editIndex('events', $item['id'], array('title' => $item['title'], 'text' => $item['text']));

					// ping
					if(BackendModel::getModuleSetting($this->URL->getModule(), 'ping_services', false)) BackendModel::ping(SITE_URL . BackendModel::getURLForBlock($this->URL->getModule(), 'detail') . '/' . $this->meta->getURL());

					// everything is saved, so redirect to the overview
					$this->redirect(BackendModel::createURLForAction('index') . '&report=edited&var=' . urlencode($item['title']) . '&id=' . $this->id . '&highlight=row-' . $item['revision_id']);
				}

				// draft
				elseif($item['status'] == 'draft')
				{
					// everything is saved, so redirect to the edit action
					$this->redirect(BackendModel::createURLForAction('edit') . '&report=saved_as_draft&var=' . urlencode($item['title']) . '&id=' . $item['id'] . '&draft=' . $item['revision_id'] . '&highlight=row-' . $item['revision_id']);
				}
			}
		}
	}
}

?>