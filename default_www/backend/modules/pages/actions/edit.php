<?php

/**
 * This is the edit-action, it will display a form to update an item
 *
 * @package		backend
 * @subpackage	pages
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendPagesEdit extends BackendBaseActionEdit
{
	/**
	 * The blocks
	 *
	 * @var	array
	 */
	private $blocks = array(), $blocksContent = array();


	/**
	 * DataGrid for the drafts
	 *
	 * @var	BackendDataGrid
	 */
	private $dgDrafts;


	/**
	 * The extras
	 *
	 * @var	array
	 */
	private $extras = array();


	/**
	 * The template data
	 *
	 * @var	array
	 */
	private $templates = array();


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably edit some general CSS/JS or other required files
		parent::execute();

		// load record
		$this->loadData();

		// add js
		$this->header->addJS('jstree/jquery.tree.js');
		$this->header->addJS('jstree/lib/jquery.cookie.js');
		$this->header->addJS('jstree/plugins/jquery.tree.cookie.js');

		// add css
		$this->header->addCSS('/backend/modules/pages/js/jstree/themes/fork/style.css', null, true);

		// get the templates
		$this->templates = BackendPagesModel::getTemplates();

		// set the default template as checked
		$this->templates[$this->record['template_id']]['checked'] = true;

		// homepage?
		if($this->id == 1)
		{
			// loop and set disabled state
			foreach($this->templates as &$row) $row['disabled'] = ($row['has_block']);
		}

		// get the extras
		$this->extras = BackendPagesModel::getExtras();

		// get maximum number of blocks
		$maxNumBlocks = BackendModel::getModuleSetting('pages', 'template_max_blocks', 5);

		// build blocks array
		for($i = 0; $i < $maxNumBlocks; $i++) $this->blocks[$i] = array('index' => $i, 'name' => 'name ' . $i);

		// load the form
		$this->loadForm();

		// load drafts
		$this->loadDrafts();

		// load the datagrid with the versions
		$this->loadRevisions();

		// validate the form
		$this->validateForm();

		// parse
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Load the record
	 *
	 * @return	void
	 */
	private function loadData()
	{
		// get record
		$this->id = $this->getParameter('id', 'int');

		// check if something went wrong
		if($this->id === null || !BackendPagesModel::exists($this->id)) $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');

		// get the record
		$this->record = BackendPagesModel::get($this->id);

		// load blocks
		$this->blocksContent = BackendPagesModel::getBlocks($this->id);

		// is there a revision specified?
		$revisionToLoad = $this->getParameter('revision', 'int');

		// if this is a valid revision
		if($revisionToLoad !== null)
		{
			// save current template
			$templateId = $this->record['template_id'];

			// overwrite the current record
			$this->record = (array) BackendPagesModel::getRevision($this->id, $revisionToLoad);

			// template is not part of revision; only data
			$this->record['template_id'] = $templateId;

			// load blocks
			$this->blocksContent = BackendPagesModel::getBlocksRevision($this->id, $revisionToLoad);

			// show warning
			$this->tpl->assign('appendRevision', true);
		}

		// is there a revision specified?
		$draftToLoad = $this->getParameter('draft', 'int');

		// if this is a valid revision
		if($draftToLoad !== null)
		{
			// save current template
			$templateId = $this->record['template_id'];

			// overwrite the current record
			$this->record = (array) BackendPagesModel::getRevision($this->id, $draftToLoad);

			// template is not part of draft; only data
			$this->record['template_id'] = $templateId;

			// load blocks
			$this->blocksContent = BackendPagesModel::getBlocksRevision($this->id, $draftToLoad);

			// show warning
			$this->tpl->assign('appendRevision', true);
		}

		// reset some vars
		$this->record['full_url'] = BackendPagesModel::getFullURL($this->record['id']);
		$this->record['is_hidden'] = ($this->record['hidden'] == 'Y');
	}


	/**
	 * Load the datagrid with drafts
	 *
	 * @return	void
	 */
	private function loadDrafts()
	{
		// create datagrid
		$this->dgDrafts = new BackendDataGridDB(BackendPagesModel::QRY_DATAGRID_BROWSE_SPECIFIC_DRAFTS, array($this->record['id'], 'draft', BL::getWorkingLanguage()));

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
		// get default template id
		$defaultTemplateId = BackendModel::getModuleSetting('pages', 'default_template', 1);

		// create form
		$this->frm = new BackendForm('edit');

		// assign in template
		$this->tpl->assign('defaultTemplateId', $defaultTemplateId);

		// create elements
		$this->frm->addText('title', $this->record['title'], null, 'inputText title', 'inputTextError title');
		$this->frm->addHidden('template_id', $this->record['template_id']);
		$this->frm->addRadiobutton('hidden', array(array('label' => BL::lbl('Hidden'), 'value' => 'Y'), array('label' => BL::lbl('Published'), 'value' => 'N')), $this->record['hidden']);
		$this->frm->addCheckbox('no_follow', ($this->record['no_follow'] == 'Y'));

		// get maximum number of blocks
		$maxNumBlocks = BackendModel::getModuleSetting('pages', 'template_max_blocks', 5);

		// build blocks array
		for($i = 0; $i < $maxNumBlocks; $i++)
		{
			// init var
			$html = null;
			$selectedExtra = null;

			// reset data, if it is available
			if(isset($this->blocksContent[$i]))
			{
				$html = $this->blocksContent[$i]['html'];
				$selectedExtra = $this->blocksContent[$i]['extra_id'];
			}

			// create elements
			$this->blocks[$i]['formElements']['hidExtraId'] = $this->frm->addHidden('block_extra_id_' . $i, $selectedExtra);
			$this->blocks[$i]['formElements']['txtHTML'] = $this->frm->addEditor('block_html_' . $i, $html);

			// add class
			$this->frm->getField('block_extra_id_' . $i)->setAttribute('class', 'block_extra_id');
		}

		// redirect
		$redirectValue = 'none';
		if(isset($this->record['data']['internal_redirect']['page_id'])) $redirectValue = 'internal';
		if(isset($this->record['data']['external_redirect']['url'])) $redirectValue = 'external';
		$redirectValues = array(
			array('value' => 'none', 'label' => ucfirst(BL::lbl('None'))),
			array('value' => 'internal', 'label' => ucfirst(BL::lbl('InternalLink')), 'variables' => array('isInternal' => true)),
			array('value' => 'external', 'label' => ucfirst(BL::lbl('ExternalLink')), 'variables' => array('isExternal' => true)),
		);
		$this->frm->addRadiobutton('redirect', $redirectValues, $redirectValue);
		$this->frm->addDropdown('internal_redirect', BackendPagesModel::getPagesForDropdown(), ($redirectValue == 'internal') ? $this->record['data']['internal_redirect']['page_id'] : null);
		$this->frm->addText('external_redirect', ($redirectValue == 'external') ? $this->record['data']['external_redirect']['url'] : null, null, null, null, true);

		// page info
		$this->frm->addCheckbox('navigation_title_overwrite', ($this->record['navigation_title_overwrite'] == 'Y'));
		$this->frm->addText('navigation_title', $this->record['navigation_title']);

		// tags
		$this->frm->addText('tags', BackendTagsModel::getTags($this->URL->getModule(), $this->id), null, 'inputText tagBox', 'inputTextError tagBox');

		// extra
		$this->frm->addDropdown('extra_type', BackendPagesModel::getTypes());

		// a specific action
		$isAction = (isset($this->record['data']['is_action']) && $this->record['data']['is_action'] == true) ? true : false;
		$this->frm->addCheckbox('is_action', $isAction);

		// meta
		$this->meta = new BackendMeta($this->frm, $this->record['meta_id'], 'title', true);
	}


	/**
	 * Load the datagrid
	 *
	 * @return	void
	 */
	private function loadRevisions()
	{
		// create datagrid
		$this->dgRevisions = new BackendDataGridDB(BackendPagesModel::QRY_BROWSE_REVISIONS, array($this->id, 'archive', BL::getWorkingLanguage()));

		// hide columns
		$this->dgRevisions->setColumnsHidden(array('id', 'revision_id'));

		// disable paging
		$this->dgRevisions->setPaging(false);

		// set headers
		$this->dgRevisions->setHeaderLabels(array('user_id' => ucfirst(BL::lbl('By')), 'edited_on' => ucfirst(BL::lbl('LastEditedOn'))));

		// set colum URLs
		$this->dgRevisions->setColumnURL('title', BackendModel::createURLForAction('edit') . '&amp;id=[id]&amp;revision=[revision_id]');

		// set functions
		$this->dgRevisions->setColumnFunction(array('BackendDataGridFunctions', 'getUser'), array('[user_id]'), 'user_id');
		$this->dgRevisions->setColumnFunction(array('BackendDataGridFunctions', 'getTimeAgo'), array('[edited_on]'), 'edited_on');

		// add use column
		$this->dgRevisions->addColumn('use_revision', null, ucfirst(BL::lbl('UseThisVersion')), BackendModel::createURLForAction('edit') . '&amp;id=[id]&amp;revision=[revision_id]', BL::lbl('UseThisVersion'));
	}


	/**
	 * Parse
	 *
	 * @return	void
	 */
	protected function parse()
	{
		// set
		$this->record['url'] = $this->meta->getURL();
		if($this->id == 1) $this->record['url'] = '';

		// parse some variables
		$this->tpl->assign('item', $this->record);
		$this->tpl->assign('templates', $this->templates);
		$this->tpl->assign('blocks', $this->blocks);
		$this->tpl->assign('extrasData', json_encode(BackendPagesModel::getExtrasData()));
		$this->tpl->assign('extrasById', json_encode(BackendPagesModel::getExtras()));
		$this->tpl->assign('prefixURL', rtrim(BackendPagesModel::getFullURL($this->record['parent_id']), '/'));

		// init var
		$showDelete = true;

		// has children?
		if(BackendPagesModel::getFirstChildId($this->record['id']) !== false) $showDelete = false;
		if(!$this->record['delete_allowed']) $showDelete = false;

		// show deletebutton
		$this->tpl->assign('showDelete', $showDelete);

		// assign template
		$this->tpl->assignArray($this->templates[$this->record['template_id']], 'template');

		// parse the form
		$this->frm->parse($this->tpl);

		// parse datagrids
		$this->tpl->assign('revisions', ($this->dgRevisions->getNumResults() != 0) ? $this->dgRevisions->getContent() : false);
		$this->tpl->assign('drafts', ($this->dgDrafts->getNumResults() != 0) ? $this->dgDrafts->getContent() : false);

		// parse the tree
		$this->tpl->assign('tree', BackendPagesModel::getTreeHTML());
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
			// get the status
			$status = SpoonFilter::getPostValue('status', array('active', 'draft'), 'active');

			// validate redirect
			$redirectValue = $this->frm->getField('redirect')->getValue();
			if($redirectValue == 'internal') $this->frm->getField('internal_redirect')->isFilled(BL::err('FieldIsRequired'));
			if($redirectValue == 'external') $this->frm->getField('external_redirect')->isURL(BL::err('InvalidURL'));

			// init var
			$templateId = (int) $this->frm->getField('template_id')->getValue();

			// loop blocks in template
			for($i = 0; $i < $this->templates[$templateId]['num_blocks']; $i++)
			{
				// get the extra id
				$extraId = (int) $this->frm->getField('block_extra_id_' . $i)->getValue();

				// reset some stuff
				if($extraId > 0)
				{
					// type of block
					if(isset($this->extras[$extraId]['type']) && $this->extras[$extraId]['type'] == 'block')
					{
						// home can't have blocks
						if($this->record['id'] == 1)
						{
							$this->frm->getField('block_html_' . $i)->addError(BL::err('HomeCantHaveBlocks'));
							$this->frm->addError(BL::err('HomeCantHaveBlocks'));
						}
					}
				}
			}

			// set callback for generating an unique URL
			$this->meta->setURLCallback('BackendPagesModel', 'getURL', array($this->record['id'], $this->record['parent_id'], $this->frm->getField('is_action')->getChecked()));

			// cleanup the submitted fields, ignore fields that were edited by hackers
			$this->frm->cleanupFields();

			// validate fields
			$this->frm->getField('title')->isFilled(BL::err('TitleIsRequired'));

			// validate meta
			$this->meta->validate();

			// no errors?
			if($this->frm->isCorrect())
			{
				// init var
				$data = null;

				// build data
				if($this->frm->getField('is_action')->isChecked()) $data['is_action'] = true;
				if($redirectValue == 'internal') $data['internal_redirect'] = array('page_id' => $this->frm->getField('internal_redirect')->getValue(), 'code' => '301');
				if($redirectValue == 'external') $data['external_redirect'] = array('url' => $this->frm->getField('external_redirect')->getValue(), 'code' => '301');

				// build page record
				$page['id'] = $this->record['id'];
				$page['user_id'] = BackendAuthentication::getUser()->getUserId();
				$page['parent_id'] = $this->record['parent_id'];
				$page['template_id'] = (int) $this->frm->getField('template_id')->getValue();
				$page['meta_id'] = (int) $this->meta->save();
				$page['language'] = BackendLanguage::getWorkingLanguage();
				$page['type'] = $this->record['type'];
				$page['title'] = $this->frm->getField('title')->getValue();
				$page['navigation_title'] = ($this->frm->getField('navigation_title')->getValue() != '') ? $this->frm->getField('navigation_title')->getValue() : $this->frm->getField('title')->getValue();
				$page['navigation_title_overwrite'] = ($this->frm->getField('navigation_title_overwrite')->isChecked()) ? 'Y' : 'N';
				$page['hidden'] = $this->frm->getField('hidden')->getValue();
				$page['status'] = $status;
				$page['publish_on'] = BackendModel::getUTCDate(null, $this->record['publish_on']);
				$page['created_on'] = BackendModel::getUTCDate(null, $this->record['created_on']);
				$page['edited_on'] = BackendModel::getUTCDate();
				$page['allow_move'] = $this->record['allow_move'];
				$page['allow_children'] = $this->record['allow_children'];
				$page['allow_edit'] = $this->record['allow_edit'];
				$page['allow_delete'] = $this->record['allow_delete'];
				$page['no_follow'] = ($this->frm->getField('no_follow')->isChecked()) ? 'Y' : 'N';
				$page['sequence'] = $this->record['sequence'];
				$page['data'] = ($data !== null) ? serialize($data) : null;

				// set navigation title
				if($page['navigation_title'] == '') $page['navigation_title'] = $page['title'];

				// insert page, store the id, we need it when building the blocks
				$page['revision_id'] = BackendPagesModel::update($page);

				// init var
				$hasBlock = false;

				// build blocks
				$blocks = array();

				// no blocks should go to waste; even if the new template has fewer blocks, retain existing content
				$maxNumBlocks = max(count($this->blocksContent), $this->templates[$page['template_id']]['num_blocks']);

				// loop blocks in template
				for($i = 0; $i < $maxNumBlocks; $i++)
				{
					// check if this block has been submitted
					if(isset($_POST['block_extra_id_' . $i]))
					{
						// get the extra id
						$extraId = (int) $this->frm->getField('block_extra_id_' . $i)->getValue();

						// reset some stuff
						if($extraId <= 0) $extraId = null;

						// init var
						$html = null;

						// extra-type is HTML
						if($extraId === null)
						{
							// reset vars
							$extraId = null;
							$html = (string) $this->frm->getField('block_html_' . $i)->getValue();
						}

						// not HTML
						else
						{
							// type of block
							if(isset($this->extras[$extraId]['type']) && $this->extras[$extraId]['type'] == 'block')
							{
								// home can't have blocks
								if($this->record['id'] == 1) throw new BackendException('Home can\'t have any blocks.');

								// set error
								if($hasBlock) throw new BackendException('Can\'t add 2 blocks');

								// reset var
								$hasBlock = true;
							}
						}

						// build block
						$block = array();
						$block['id'] = (isset($this->blocksContent[$i]['id'])) ? $this->blocksContent[$i]['id'] : BackendPagesModel::getMaximumBlockId() + ($i + 1);
						$block['revision_id'] = $page['revision_id'];
						$block['extra_id'] = $extraId;
						$block['html'] = $html;
						$block['status'] = 'active';
						$block['created_on'] = (isset($this->blocksContent[$i]['created_on'])) ? BackendModel::getUTCDate(null, $this->blocksContent[$i]['created_on']) : BackendModel::getUTCDate();
						$block['edited_on'] = BackendModel::getUTCDate();
					}

					// block was not submitted: use existing data
					else
					{
						$block = $this->blocksContent[$i];
						$block['revision_id'] = $page['revision_id'];
					}

					// add block
					$blocks[] = $block;
				}

				// update the blocks
				BackendPagesModel::updateBlocks($blocks, $hasBlock);

				// save tags
				BackendTagsModel::saveTags($page['id'], $this->frm->getField('tags')->getValue(), $this->URL->getModule());

				// build cache
				BackendPagesModel::buildCache(BL::getWorkingLanguage());

				// active
				if($page['status'] == 'active')
				{
					// edit search index
					if(is_callable(array('BackendSearchModel', 'editIndex')))
					{
						// init var
						$text = '';

						// build search-text
						foreach($blocks as $block) $text .= ' ' . $block['html'];

						// add
						BackendSearchModel::editIndex('pages', $page['id'], array('title' => $page['title'], 'text' => $text));
					}

					// build URL
					$redirectUrl = BackendModel::createURLForAction('edit') . '&id=' . $page['id'] . '&report=edited&var=' . urlencode($page['title']) . '&highlight=row-' . $page['id'];
				}

				// draft
				elseif($page['status'] == 'draft')
				{
					// everything is saved, so redirect to the edit action
					$redirectUrl = BackendModel::createURLForAction('edit') . '&id=' . $page['id'] . '&report=saved_as_draft&var=' . urlencode($page['title']) . '&highlight=row-' . $page['id'] . '&draft=' . $page['revision_id'];
				}

				// everything is saved, so redirect to the overview
				$this->redirect($redirectUrl);
			}
		}
	}
}

?>