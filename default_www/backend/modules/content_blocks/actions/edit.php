<?php

/**
 * This is the edit-action, it will display a form to edit an existing item
 *
 * @package		backend
 * @subpackage	content_blocks
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.0
 */
class BackendContentBlocksEdit extends BackendBaseActionEdit
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

		// does the item exist
		if($this->id !== null && BackendContentBlocksModel::exists($this->id))
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

		// no item found, throw an exceptions, because somebody is fucking with our url
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}


	/**
	 * Get the data
	 * If a revision-id was specified in the URL we load the revision and not the most recent data.
	 *
	 * @return	void
	 */
	private function getData()
	{
		// fetch record
		$this->record = BackendContentBlocksModel::get($this->id);

		// specific revision?
		$revisionToLoad = $this->getParameter('revision', 'int');

		// if this is a valid revision
		if($revisionToLoad !== null)
		{
			// overwrite the current record
			$this->record = BackendContentBlocksModel::getRevision($this->id, $revisionToLoad);

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

		// create elements
		$this->frm->addText('title', $this->record['title']);
		$this->frm->addEditor('text', $this->record['text']);
		$this->frm->addCheckbox('hidden', ($this->record['hidden'] == 'N'));
	}


	/**
	 * Load the datagrid with revisions
	 *
	 * @return	void
	 */
	private function loadRevisions()
	{
		// create datagrid
		$this->dgRevisions = new BackendDataGridDB(BackendContentBlocksModel::QRY_BROWSE_REVISIONS, array('archived', $this->record['id'], BL::getWorkingLanguage()));

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

		// assign fields
		$this->tpl->assign('id', $this->record['id']);
		$this->tpl->assign('title', $this->record['title']);
		$this->tpl->assign('revision_id', $this->record['revision_id']);

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

			// validate fields
			$this->frm->getField('title')->isFilled(BL::err('TitleIsRequired'));

			// no errors?
			if($this->frm->isCorrect())
			{
				// build item
				$item['id'] = $this->id;
				$item['user_id'] = BackendAuthentication::getUser()->getUserId();
				$item['language'] = $this->record['language'];
				$item['extra_id'] = $this->record['extra_id'];
				$item['title'] = $this->frm->getField('title')->getValue();
				$item['text'] = $this->frm->getField('text')->getValue();
				$item['hidden'] = $this->frm->getField('hidden')->getChecked() ? 'N' : 'Y';
				$item['status'] = 'active';
				$item['edited_on'] = BackendModel::getUTCDate();

				// insert the item
				$item['revision_id'] = BackendContentBlocksModel::update($item);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') . '&report=edited&var=' . urlencode($item['title']) . '&highlight=row-' . $item['id']);
			}
		}
	}
}

?>