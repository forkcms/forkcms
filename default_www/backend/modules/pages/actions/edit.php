<?php

/**
 * BackendPagesEdit
 *
 * This is the edit-action, it will display a form to create a new pages item
 *
 * @package		backend
 * @subpackage	pages
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendPagesEdit extends BackendBaseActionEdit
{
	/**
	 * The blocks
	 *
	 * @var	array
	 */
	private $blocks = array(),
			$blocksContent = array();


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

		// is there a report to show?
		if($this->getParameter('report') !== null)
		{
			// show the report
			$this->tpl->assign('report', true);

			// camelcase the string
			$messageName = SpoonFilter::toCamelCase($this->getParameter('report'));

			// if we have data to use it will be passed as the var-parameter, if so assign it
			if($this->getParameter('var') !== null) $this->tpl->assign('reportMessage', sprintf(BackendLanguage::getMessage($messageName), $this->getParameter('var')));
			else $this->tpl->assign('reportMessage', $messageName);

			// hightlight an element with the given id if needed
			if($this->getParameter('highlight')) $this->tpl->assign('highlight', $this->getParameter('highlight'));
		}

		// is there an error to show?
		if($this->getParameter('error') !== null)
		{
			// show the error and the errormessage
			$this->tpl->assign('errorMessage', BackendLanguage::getError(SpoonFilter::toCamelCase($this->getParameter('error'), '-')));
		}

		// add js
		$this->header->addJavascript('jstree/jquery.tree.js');
		$this->header->addJavascript('jstree/lib/jquery.cookie.js');
		$this->header->addJavascript('jstree/plugins/jquery.tree.cookie.js');

		// add css
		$this->header->addCSS('/backend/modules/pages/js/jstree/themes/fork/style.css', null, true);

		// load record
		$this->loadData();

		// get data
		$this->templates = BackendPagesModel::getTemplates();

		// get extras
		$this->extras = BackendPagesModel::getExtrasData();

		// get maximum number of blocks
		$maximumNumberOfBlocks = BackendModel::getSetting('core', 'template_max_blocks', 5);

		// build blocks array
		for($i = 0; $i < $maximumNumberOfBlocks; $i++) $this->blocks[$i] = array('index' => $i, 'name' => 'name '. $i,);

		// load the form
		$this->loadForm();

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
	 * Load the datagrid
	 *
	 * @return	void
	 */
	private function loadRevisions()
	{
		// create datagrid
		$this->dgRevisions = new BackendDataGridDB(BackendPagesModel::QRY_BROWSE_REVISIONS, array($this->id, 'archive', BL::getWorkingLanguage()));

		// disable paging
		$this->dgRevisions->setPaging(false);

		// hide columns
		$this->dgRevisions->setColumnsHidden(array('id', 'revision_id'));

		// set functions
		$this->dgRevisions->setColumnFunction(array('BackendDataGridFunctions', 'getUser'), array('[user_id]'), 'user_id');
		$this->dgRevisions->setColumnFunction(array('BackendDataGridFunctions', 'getTimeAgo'), array('[edited_on]'), 'edited_on');

		// add edit column
		$this->dgRevisions->addColumn('use_revision', null, ucfirst(BL::getLabel('UseThisVersion')), BackendModel::createURLForAction('edit') .'&id=[id]&revision=[revision_id]', BL::getLabel('UseThisVersion'));

		// set headers
		$this->dgRevisions->setHeaderLabels(array(	'user_id' => ucfirst(BL::getLabel('By')),
												'edited_on' => ucfirst(BL::getLabel('Date'))));

	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// get default template id
		$defaultTemplateId = BackendModel::getSetting('core', 'default_template', 1);

		// init var
		$templatesForDropdown = array();

		// build values
		foreach($this->templates as $templateId => $row)
		{
			// set value
			$templatesForDropdown[$templateId] = $row['label'];

			// set checked
			if($templateId == $this->record['template_id']) $this->templates[$templateId]['checked'] = true;
		}

		// create form
		$this->frm = new BackendForm('edit');

		// assign in template
		$this->tpl->assign('defaultTemplateId', $defaultTemplateId);

		// create elements
		$this->frm->addTextField('title', $this->record['title']);
		$this->frm->addDropDown('template_id', $templatesForDropdown, $this->record['template_id']);
		$this->frm->addRadioButton('hidden', array(array('label' => BL::getLabel('Hidden'), 'value' => 'Y'), array('label' => BL::getLabel('Published'), 'value' => 'N')), $this->record['hidden']);
		$this->frm->addCheckBox('no_follow', ($this->record['no_follow'] == 'Y'));

		// get maximum number of blocks
		$maximumNumberOfBlocks = BackendModel::getSetting('core', 'template_max_blocks', 5);

		// build blocks array
		for($i = 0; $i < $maximumNumberOfBlocks; $i++)
		{
			// init var
			$selectedExtra = null;
			$html = null;

			// reset data, if it is available
			if(isset($this->blocksContent[$i]))
			{
				$selectedExtra = $this->blocksContent[$i]['extra_id'];
				$html = $this->blocksContent[$i]['html'];
			}

			// create elements
			$this->blocks[$i]['formElements']['ddmExtraId'] = $this->frm->addDropDown('block_extra_id_'. $i, $this->extras['dropdown'], $selectedExtra);
			$this->blocks[$i]['formElements']['txtHTML'] = $this->frm->addEditorField('block_html_'. $i, $html);

			// set types
			foreach($this->extras['types'] as $value => $type) $this->frm->getField('block_extra_id_'. $i)->setOptionAttributes($value, array('rel' => $type));
		}

		// page info
		$this->frm->addCheckBox('navigation_title_overwrite', ($this->record['navigation_title_overwrite'] == 'Y'));
		$this->frm->addTextField('navigation_title', $this->record['navigation_title']);

		// tags
		$this->frm->addTextField('tags', BackendTagsModel::getTags($this->URL->getModule(), $this->id), null, 'inputTextfield tagBox', 'inputTextfieldError tagBox');

		// meta
		$this->meta = new BackendMeta($this->frm, $this->record['meta_id'], 'title', true);
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

		// validate id
		if($this->id == 0 || !BackendPagesModel::exists($this->id)) $this->redirect(BackendModel::createURLForAction('index') .'&error=non-existing');

		// get the record
		$this->record = BackendPagesModel::get($this->id);

		// load blocks
		$this->blocksContent = BackendPagesModel::getBlocks($this->id);

		// is there a revision specified?
		$revisionToLoad = $this->getParameter('revision', 'int');

		// if this is a valid revision
		if($revisionToLoad !== null)
		{
			// overwrite the current record
			$this->record = (array) BackendPagesModel::getRevision($this->id, $revisionToLoad);

			// load blocks
			$this->blocksContent = BackendPagesModel::getBlocksRevision($this->id, $revisionToLoad);

			// show warning
			if($this->record['status'] == 'archive') $this->tpl->assign('usingRevision', true);
			elseif($this->record['status'] == 'draft') $this->tpl->assign('usingDraft', true);
		}
	}


	/**
	 * Parse
	 *
	 * @return	void
	 */
	protected function parse()
	{
		// parse some variables
		$this->tpl->assignArray($this->record, 'record');
		$this->tpl->assign('templates', $this->templates);
		$this->tpl->assign('blocks', $this->blocks);
		$this->tpl->assign('extras', $this->extras['data']);

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

		// get full URL
		$URL = BackendPagesModel::getFullURL($this->record['id']);

		// assign full URL
		$this->tpl->assign('pageUrl', $URL);
		$this->tpl->assign('seoPageUrl', str_replace($this->meta->getURL(), '', $URL));

		// parse datagrid
		$this->tpl->assign('revisions', ($this->dgRevisions->getNumResults() != 0) ? $this->dgRevisions->getContent() : false);

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
			// set callback for generating an unique URL
			$this->meta->setUrlCallback('BackendPagesModel', 'getURL', array($this->record['id'], $this->record['parent_id']));

			// cleanup the submitted fields, ignore fields that were edited by hackers
			$this->frm->cleanupFields();

			// validate fields
			$this->frm->getField('title')->isFilled(BL::getError('TitleIsRequired'));

			// validate meta
			$this->meta->validate();

			// no errors?
			if($this->frm->isCorrect())
			{
				// build page record
				$page = array();
				$page['id'] = $this->record['id'];
				$page['user_id'] = BackendAuthentication::getUser()->getUserId();
				$page['parent_id'] = $this->record['parent_id'];
				$page['template_id'] = (int) $this->frm->getField('template_id')->getValue();
				$page['meta_id'] = (int) $this->meta->save();
				$page['language'] = BackendLanguage::getWorkingLanguage();
				$page['type'] = $this->record['type'];
				$page['title'] = $this->frm->getField('title')->getValue();
				$page['navigation_title'] = $this->frm->getField('navigation_title')->getValue();
				$page['navigation_title_overwrite'] = ($this->frm->getField('navigation_title_overwrite')->isChecked()) ? 'Y' : 'N';
				$page['hidden'] = $this->frm->getField('hidden')->getValue();
				$page['status'] = 'active';
				$page['publish_on'] = BackendModel::getUTCDate(null, $this->record['publish_on']);
				$page['created_on'] = BackendModel::getUTCDate(null, $this->record['created_on']);
				$page['edited_on'] = BackendModel::getUTCDate();
				$page['allow_move'] = $this->record['allow_move'];
				$page['allow_children'] = $this->record['allow_children'];
				$page['allow_edit'] = $this->record['allow_edit'];
				$page['allow_delete'] = $this->record['allow_delete'];
				$page['no_follow'] = ($this->frm->getField('no_follow')->isChecked()) ? 'Y' : 'N';
				$page['sequence'] = $this->record['sequence'];

				// insert page, store the id, we need it when building the blocks
				$revisionId = BackendPagesModel::update($page);

				// init var
				$hasBlock = false;

				// build blocks
				$blocks = array();

				// loop blocks in template
				for($i = 0; $i < $this->templates[$page['template_id']]['num_blocks']; $i++)
				{
					// get the extra id
					$extraId = $this->frm->getField('block_extra_id_'. $i)->getValue();

					// init var
					$html = null;

					// extra-type is HTML
					if($extraId == 'html')
					{
						// reset vars
						$extraId = null;
						$html = $this->frm->getField('block_html_'. $i)->getValue();
					}

					// not HTML
					else
					{
						// type of block
						if($this->extras['types'][$extraId] == 'block')
						{
							// set error
							if($hasBlock) $this->frm->getField('block_extra_id_'. $i)->addError('Can\'t add 2 blocks');

							// reset var
							$hasBlock = true;
						}
					}

					// build block
					$block = array();
					$block['id'] = (isset($this->blocksContent[$i]['id'])) ? $this->blocksContent[$i]['id'] : BackendPagesModel::getMaximumBlockId() + ($i + 1);
					$block['revision_id'] = $revisionId;
					$block['extra_id'] = $extraId;
					$block['html'] = $html;
					$block['status'] = 'active';
					$block['created_on'] = BackendModel::getUTCDate();
					$block['edited_on'] = BackendModel::getUTCDate();

					// edit block
					$blocks[] = $block;
				}

				// insert the blocks
				BackendPagesModel::updateBlocks($blocks, $hasBlock);

				// save tags
				BackendTagsModel::saveTags($page['id'], $this->frm->getField('tags')->getValue(), $this->URL->getModule());

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('edit') .'&id='. $page['id'] .'&report=edited&var='. urlencode($page['title']) .'&hilight=id-'. $page['id']);
			}
		}
	}
}

?>