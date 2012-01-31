<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the add-action, it will display a form to create a new item
 *
 * @author Matthias Mullie <matthias@mullie.eu>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 */
class BackendPagesAdd extends BackendBaseActionAdd
{
	/**
	 * The blocks linked to this page
	 *
	 * @var	array
	 */
	private $blocksContent = array();

	/**
	 * Is the current user a god user?
	 *
	 * @var bool
	 */
	private $isGod = false;

	/**
	 * The positions
	 *
	 * @var	array
	 */
	private $positions = array();

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
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// add js
		$this->header->addJS('jstree/jquery.tree.js', null, false);
		$this->header->addJS('jstree/lib/jquery.cookie.js', null, false);
		$this->header->addJS('jstree/plugins/jquery.tree.cookie.js', null, false);

		// add css
		$this->header->addCSS('/backend/modules/pages/js/jstree/themes/fork/style.css', null, true);

		// get the templates
		$this->templates = BackendExtensionsModel::getTemplates();
		$this->isGod = BackendAuthentication::getUser()->isGod();

		// init var
		$defaultTemplateId = BackendModel::getModuleSetting($this->getModule(), 'default_template', false);

		// fallback
		if($defaultTemplateId === false)
		{
			// get first key
			$keys = array_keys($this->templates);

			// set the first items as default if no template was set as default.
			$defaultTemplateId = $this->templates[$keys[0]]['id'];
		}

		// set the default template as checked
		$this->templates[$defaultTemplateId]['checked'] = true;

		// get the extras
		$this->extras = BackendExtensionsModel::getExtras();

		$this->loadForm();
		$this->validateForm();
		$this->parse();
		$this->display();
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		// get default template id
		$defaultTemplateId = BackendModel::getModuleSetting($this->getModule(), 'default_template', 1);

		// create form
		$this->frm = new BackendForm('add');

		// assign in template
		$this->tpl->assign('defaultTemplateId', $defaultTemplateId);

		// create elements
		$this->frm->addText('title', null, null, 'inputText title', 'inputTextError title');
		$this->frm->addEditor('html');
		$this->frm->addHidden('template_id', $defaultTemplateId);
		$this->frm->addRadiobutton('hidden', array(array('label' => BL::lbl('Hidden'), 'value' => 'Y'), array('label' => BL::lbl('Published'), 'value' => 'N')), 'N');

		// a god user should be able to adjust the detailed settings for a page easily
		if($this->isGod)
		{
			// init some vars
			$items = array('move', 'children', 'edit', 'delete');
			$checked = array();
			$values = array();

			foreach($items as $value)
			{
				$values[] = array('label' => BL::msg(SpoonFilter::toCamelCase('allow_' . $value)), 'value' => $value);
				$checked[] = $value;
			}

			$this->frm->addMultiCheckbox('allow', $values, $checked);
		}

		// build prototype block
		$block['index'] = 0;
		$block['formElements']['chkVisible'] = $this->frm->addCheckbox('block_visible_' . $block['index'], true);
		$block['formElements']['hidExtraId'] = $this->frm->addHidden('block_extra_id_' . $block['index'], 0);
		$block['formElements']['hidPosition'] = $this->frm->addHidden('block_position_' . $block['index'], 'fallback');
		$block['formElements']['txtHTML'] = $this->frm->addTextArea('block_html_' . $block['index']); // this is no editor; we'll add the editor in JS

		// add default block to "fallback" position, the only one which we can rest assured to exist
		$this->positions['fallback']['blocks'][] = $block;

		// content has been submitted: re-create submitted content rather than the db-fetched content
		if(isset($_POST['block_html_0']))
		{
			// init vars
			$this->blocksContent = array();
			$hasBlock = false;
			$i = 1;

			// loop submitted blocks
			while(isset($_POST['block_position_' . $i]))
			{
				// init var
				$block = array();

				// save block position
				$block['position'] = $_POST['block_position_' . $i];
				$positions[$block['position']][] = $block;

				// set linked extra
				$block['extra_id'] = $_POST['block_extra_id_' . $i];

				// reset some stuff
				if($block['extra_id'] <= 0) $block['extra_id'] = null;

				// init html
				$block['html'] = null;

				// extra-type is HTML
				if($block['extra_id'] === null)
				{
					// reset vars
					$block['extra_id'] = null;
					$block['html'] = $_POST['block_html_' . $i];
				}

				// not HTML
				else
				{
					// type of block
					if(isset($this->extras[$block['extra_id']]['type']) && $this->extras[$block['extra_id']]['type'] == 'block')
					{
						// set error
						if($hasBlock) $this->frm->addError(BL::err('CantAdd2Blocks'));

						// reset var
						$hasBlock = true;
					}
				}

				// set data
				$block['created_on'] = BackendModel::getUTCDate();
				$block['edited_on'] = $block['created_on'];
				$block['visible'] = isset($_POST['block_visible_' . $i]) && $_POST['block_visible_' . $i] == 'Y' ? 'Y' : 'N';
				$block['sequence'] = count($positions[$block['position']]) - 1;

				// add to blocks
				$this->blocksContent[] = $block;

				// increment counter; go fetch next block
				$i++;
			}
		}

		// build blocks array
		foreach($this->blocksContent as $i => $block)
		{
			$block['index'] = $i + 1;
			$block['formElements']['chkVisible'] = $this->frm->addCheckbox('block_visible_' . $block['index'], $block['visible'] == 'Y');
			$block['formElements']['hidExtraId'] = $this->frm->addHidden('block_extra_id_' . $block['index'], (int) $block['extra_id']);
			$block['formElements']['hidPosition'] = $this->frm->addHidden('block_position_' . $block['index'], $block['position']);
			$block['formElements']['txtHTML'] = $this->frm->addTextArea('block_html_' . $block['index'], $block['html']); // this is no editor; we'll add the editor in JS

			$this->positions[$block['position']]['blocks'][] = $block;
		}

		// redirect
		$redirectValues = array(
			array('value' => 'none', 'label' => SpoonFilter::ucfirst(BL::lbl('None'))),
			array('value' => 'internal', 'label' => SpoonFilter::ucfirst(BL::lbl('InternalLink')), 'variables' => array('isInternal' => true)),
			array('value' => 'external', 'label' => SpoonFilter::ucfirst(BL::lbl('ExternalLink')), 'variables' => array('isExternal' => true)),
		);
		$this->frm->addRadiobutton('redirect', $redirectValues, 'none');
		$this->frm->addDropdown('internal_redirect', BackendPagesModel::getPagesForDropdown());
		$this->frm->addText('external_redirect', null, null, null, null, true);

		// page info
		$this->frm->addCheckbox('navigation_title_overwrite');
		$this->frm->addText('navigation_title');

		// tags
		$this->frm->addText('tags', null, null, 'inputText tagBox', 'inputTextError tagBox');

		// a specific action
		$this->frm->addCheckbox('is_action', false);

		// extra
		$this->frm->addDropdown('extra_type', BackendPagesModel::getTypes());

		// meta
		$this->meta = new BackendMeta($this->frm, null, 'title', true);

		// set callback for generating an unique URL
		$this->meta->setURLCallback('BackendPagesModel', 'getURL', array(0, null, false));
	}

	/**
	 * Parse
	 */
	protected function parse()
	{
		parent::parse();

		// parse some variables
		$this->tpl->assign('templates', $this->templates);
		$this->tpl->assign('isGod', $this->isGod);
		$this->tpl->assign('positions', $this->positions);
		$this->tpl->assign('extrasData', json_encode(BackendExtensionsModel::getExtrasData()));
		$this->tpl->assign('extrasById', json_encode(BackendExtensionsModel::getExtras()));
		$this->tpl->assign('prefixURL', rtrim(BackendPagesModel::getFullURL(1), '/'));
		$this->tpl->assign('formErrors', (string) $this->frm->getErrors());

		// get default template id
		$defaultTemplateId = BackendModel::getModuleSetting($this->getModule(), 'default_template', 1);

		// assign template
		$this->tpl->assignArray($this->templates[$defaultTemplateId], 'template');

		// parse the form
		$this->frm->parse($this->tpl);

		// parse the tree
		$this->tpl->assign('tree', BackendPagesModel::getTreeHTML());
	}

	/**
	 * Validate the form
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

			// set callback for generating an unique URL
			$this->meta->setURLCallback('BackendPagesModel', 'getURL', array(0, null, $this->frm->getField('is_action')->getChecked()));

			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// validate fields
			$this->frm->getField('title')->isFilled(BL::err('TitleIsRequired'));

			// validate meta
			$this->meta->validate();

			// no errors?
			if($this->frm->isCorrect())
			{
				// init var
				$parentId = 0;
				$data = null;

				// build data
				if($this->frm->getField('is_action')->isChecked()) $data['is_action'] = true;
				if($redirectValue == 'internal') $data['internal_redirect'] = array('page_id' => $this->frm->getField('internal_redirect')->getValue(), 'code' => '301');
				if($redirectValue == 'external') $data['external_redirect'] = array('url' => $this->frm->getField('external_redirect')->getValue(), 'code' => '301');

				// build page record
				$page['id'] = BackendPagesModel::getMaximumPageId() + 1;
				$page['user_id'] = BackendAuthentication::getUser()->getUserId();
				$page['parent_id'] = $parentId;
				$page['template_id'] = (int) $this->frm->getField('template_id')->getValue();
				$page['meta_id'] = (int) $this->meta->save();
				$page['language'] = BackendLanguage::getWorkingLanguage();
				$page['type'] = 'root';
				$page['title'] = $this->frm->getField('title')->getValue();
				$page['navigation_title'] = ($this->frm->getField('navigation_title')->getValue() != '') ? $this->frm->getField('navigation_title')->getValue() : $this->frm->getField('title')->getValue();
				$page['navigation_title_overwrite'] = ($this->frm->getField('navigation_title_overwrite')->isChecked()) ? 'Y' : 'N';
				$page['hidden'] = $this->frm->getField('hidden')->getValue();
				$page['status'] = $status;
				$page['publish_on'] = BackendModel::getUTCDate();
				$page['created_on'] = BackendModel::getUTCDate();
				$page['edited_on'] = BackendModel::getUTCDate();
				$page['allow_move'] = 'Y';
				$page['allow_children'] = 'Y';
				$page['allow_edit'] = 'Y';
				$page['allow_delete'] = 'Y';
				$page['sequence'] = BackendPagesModel::getMaximumSequence($parentId) + 1;
				$page['data'] = ($data !== null) ? serialize($data) : null;

				if($this->isGod)
				{
					$page['allow_move'] = (in_array('move', (array) $this->frm->getField('allow')->getValue())) ? 'Y' : 'N';
					$page['allow_children'] = (in_array('children', (array) $this->frm->getField('allow')->getValue())) ? 'Y' : 'N';
					$page['allow_edit'] = (in_array('edit', (array) $this->frm->getField('allow')->getValue())) ? 'Y' : 'N';
					$page['allow_delete'] = (in_array('delete', (array) $this->frm->getField('allow')->getValue())) ? 'Y' : 'N';
				}

				// set navigation title
				if($page['navigation_title'] == '') $page['navigation_title'] = $page['title'];

				// insert page, store the id, we need it when building the blocks
				$page['revision_id'] = BackendPagesModel::insert($page);

				// loop blocks
				foreach($this->blocksContent as $i => $block)
				{
					// add page revision id to blocks
					$this->blocksContent[$i]['revision_id'] = $page['revision_id'];

					// validate blocks, only save blocks for valid positions
					if(!in_array($block['position'], $this->templates[$this->frm->getField('template_id')->getValue()]['data']['names'])) unset($this->blocksContent[$i]);
				}

				// insert the blocks
				BackendPagesModel::insertBlocks($this->blocksContent);

				// trigger an event
				BackendModel::triggerEvent($this->getModule(), 'after_add', $page);

				// save tags
				BackendTagsModel::saveTags($page['id'], $this->frm->getField('tags')->getValue(), $this->URL->getModule());

				// build the cache
				BackendPagesModel::buildCache(BL::getWorkingLanguage());

				// active
				if($page['status'] == 'active')
				{
					// init var
					$text = '';

					// build search-text
					foreach($this->blocksContent as $block) $text .= ' ' . $block['html'];

					// add to search index
					BackendSearchModel::saveIndex($this->getModule(), $page['id'], array('title' => $page['title'], 'text' => $text));

					// everything is saved, so redirect to the overview
					$this->redirect(BackendModel::createURLForAction('edit') . '&id=' . $page['id'] . '&report=added&var=' . urlencode($page['title']) . '&highlight=row-' . $page['id']);
				}

				// draft
				elseif($page['status'] == 'draft')
				{
					// everything is saved, so redirect to the edit action
					$this->redirect(BackendModel::createURLForAction('edit') . '&id=' . $page['id'] . '&report=saved-as-draft&var=' . urlencode($page['title']) . '&highlight=row-' . $page['revision_id'] . '&draft=' . $page['revision_id']);
				}
			}
		}
	}
}
