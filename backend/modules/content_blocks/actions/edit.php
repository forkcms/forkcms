<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the edit-action, it will display a form to edit an existing item
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Matthias Mullie <matthias@mullie.eu>
 */
class BackendContentBlocksEdit extends BackendBaseActionEdit
{
	/**
	 * The available templates
	 *
	 * @var	array
	 */
	private $templates = array();

	/**
	 * Execute the action
	 */
	public function execute()
	{
		$this->id = $this->getParameter('id', 'int');

		// does the item exist
		if($this->id !== null && BackendContentBlocksModel::exists($this->id))
		{
			parent::execute();
			$this->getData();
			$this->loadRevisions();
			$this->loadForm();
			$this->validateForm();
			$this->parse();
			$this->display();
		}

		// no item found, throw an exceptions, because somebody is fucking with our url
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}

	/**
	 * Get the data
	 * If a revision-id was specified in the URL we load the revision and not the most recent data.
	 */
	private function getData()
	{
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

		// get the templates
		// @todo why is $this->templates loaded twice?
		$this->templates = BackendContentBlocksModel::getTemplates();

		// check if selected template is still available
		if($this->record['template'] && !in_array($this->record['template'], $this->templates)) $this->record['template'] = '';
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		$this->frm = new BackendForm('edit');
		$this->frm->addText('title', $this->record['title'], null, 'inputText title', 'inputTextError title');
		$this->frm->addEditor('text', $this->record['text']);
		$this->frm->addCheckbox('hidden', ($this->record['hidden'] == 'N'));

		// if we have multiple templates, add a dropdown to select them
		if(count($this->templates) > 1)
		{
			$this->frm->addDropdown('template', array_combine($this->templates, $this->templates), $this->record['template']);
		}
	}

	/**
	 * Load the datagrid with revisions
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
		$this->dgRevisions->setHeaderLabels(array('user_id' => SpoonFilter::ucfirst(BL::lbl('By')), 'edited_on' => SpoonFilter::ucfirst(BL::lbl('LastEditedOn'))));

		// set column-functions
		$this->dgRevisions->setColumnFunction(array('BackendDataGridFunctions', 'getUser'), array('[user_id]'), 'user_id');
		$this->dgRevisions->setColumnFunction(array('BackendDataGridFunctions', 'getTimeAgo'), array('[edited_on]'), 'edited_on');

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('edit'))
		{
			// set colum URLs
			$this->dgRevisions->setColumnURL('title', BackendModel::createURLForAction('edit') . '&amp;id=[id]&amp;revision=[revision_id]');

			// add use column
			$this->dgRevisions->addColumn('use_revision', null, BL::lbl('UseThisVersion'), BackendModel::createURLForAction('edit') . '&amp;id=[id]&amp;revision=[revision_id]', BL::lbl('UseThisVersion'));
		}
	}

	/**
	 * Parse the form
	 */
	protected function parse()
	{
		parent::parse();

		$this->tpl->assign('id', $this->record['id']);
		$this->tpl->assign('title', $this->record['title']);
		$this->tpl->assign('revision_id', $this->record['revision_id']);

		// assign revisions-datagrid
		$this->tpl->assign('revisions', ($this->dgRevisions->getNumResults() != 0) ? $this->dgRevisions->getContent() : false);
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
			$this->frm->getField('title')->isFilled(BL::err('TitleIsRequired'));

			if($this->frm->isCorrect())
			{
				$item['id'] = $this->id;
				$item['user_id'] = BackendAuthentication::getUser()->getUserId();
				$item['template'] = count($this->templates) > 1 ? $this->frm->getField('template')->getValue() : $this->templates[0];
				$item['language'] = $this->record['language'];
				$item['extra_id'] = $this->record['extra_id'];
				$item['title'] = $this->frm->getField('title')->getValue();
				$item['text'] = $this->frm->getField('text')->getValue();
				$item['hidden'] = $this->frm->getField('hidden')->getChecked() ? 'N' : 'Y';
				$item['status'] = 'active';
				$item['created_on'] = BackendModel::getUTCDate(null, $this->record['created_on']);
				$item['edited_on'] = BackendModel::getUTCDate();

				// insert the item
				$item['revision_id'] = BackendContentBlocksModel::update($item);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_edit', array('item' => $item));

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') . '&report=edited&var=' . urlencode($item['title']) . '&highlight=row-' . $item['id']);
			}
		}
	}
}
