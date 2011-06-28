<?php

/**
 * This is the add-action, it will display a form to create a new item
 *
 * @package		backend
 * @subpackage	pages
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendPagesAdd extends BackendBaseActionAdd
{
	/**
	 * The blocks
	 *
	 * @var	array
	 */
	private $blocks = array();


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
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// add js
		$this->header->addJS('jstree/jquery.tree.js');
		$this->header->addJS('jstree/lib/jquery.cookie.js');
		$this->header->addJS('jstree/plugins/jquery.tree.cookie.js');

		// add css
		$this->header->addCSS('/backend/modules/pages/js/jstree/themes/fork/style.css', null, true);

		// get the templates
		$this->templates = BackendPagesModel::getTemplates();

		// init var
		$defaultTemplateId = BackendModel::getModuleSetting('pages', 'default_template', false);

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
		$this->extras = BackendPagesModel::getExtras();

		// get maximum number of blocks
		$maxNumBlocks = BackendModel::getModuleSetting('pages', 'template_max_blocks', 5);

		// build blocks array
		for($i = 0; $i < $maxNumBlocks; $i++) $this->blocks[$i] = array('index' => $i, 'name' => 'name ' . $i,);

		// load the form
		$this->loadForm();

		// validate the form
		$this->validateForm();

		// parse
		$this->parse();

		// display the page
		$this->display();
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
		$this->frm = new BackendForm('add');

		// assign in template
		$this->tpl->assign('defaultTemplateId', $defaultTemplateId);

		// create elements
		$this->frm->addText('title', null, null, 'inputText title', 'inputTextError title');
		$this->frm->addHidden('template_id', $defaultTemplateId);
		$this->frm->addRadiobutton('hidden', array(array('label' => BL::lbl('Hidden'), 'value' => 'Y'), array('label' => BL::lbl('Published'), 'value' => 'N')), 'N');
		$this->frm->addCheckbox('no_follow');

		// get maximum number of blocks
		$maxNumBlocks = BackendModel::getModuleSetting('pages', 'template_max_blocks', 5);

		// build blocks array
		for($i = 0; $i < $maxNumBlocks; $i++)
		{
			$this->blocks[$i]['formElements']['hidExtraId'] = $this->frm->addHidden('block_extra_id_' . $i);
			$this->blocks[$i]['formElements']['txtHTML'] = $this->frm->addEditor('block_html_' . $i, '');
		}

		// redirect
		$redirectValues = array(
			array('value' => 'none', 'label' => ucfirst(BL::lbl('None'))),
			array('value' => 'internal', 'label' => ucfirst(BL::lbl('InternalLink')), 'variables' => array('isInternal' => true)),
			array('value' => 'external', 'label' => ucfirst(BL::lbl('ExternalLink')), 'variables' => array('isExternal' => true)),
		);
		$this->frm->addRadiobutton('redirect', $redirectValues, 'none');
		$this->frm->addDropdown('internal_redirect', BackendPagesModel::getPagesForDropdown());
		$this->frm->addText('external_redirect', null, null, null, null, true);

		// page info
		$this->frm->addCheckbox('navigation_title_overwrite');
		$this->frm->addText('navigation_title');

		// tags
		$this->frm->addText('tags', null, null, 'inputText tagBox', 'inputTextError tagBox');

		// meta
		$this->meta = new BackendMeta($this->frm, null, 'title', true);

		// a specific action
		$this->frm->addCheckbox('is_action', false);

		// extra
		$this->frm->addDropdown('extra_type', BackendPagesModel::getTypes());
	}


	/**
	 * Parse
	 *
	 * @return	void
	 */
	protected function parse()
	{
		// parse some variables
		$this->tpl->assign('templates', $this->templates);
		$this->tpl->assign('blocks', $this->blocks);
		$this->tpl->assign('extrasData', json_encode(BackendPagesModel::getExtrasData()));
		$this->tpl->assign('extrasById', json_encode(BackendPagesModel::getExtras()));
		$this->tpl->assign('prefixURL', rtrim(BackendPagesModel::getFullURL(1), '/'));

		// get default template id
		$defaultTemplateId = BackendModel::getModuleSetting('pages', 'default_template', 1);

		// assign template
		$this->tpl->assignArray($this->templates[$defaultTemplateId], 'template');

		// parse the form
		$this->frm->parse($this->tpl);

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
				$page['no_follow'] = ($this->frm->getField('no_follow')->isChecked()) ? 'Y' : 'N';
				$page['sequence'] = BackendPagesModel::getMaximumSequence($parentId) + 1;
				$page['data'] = ($data !== null) ? serialize($data) : null;

				// set navigation title
				if($page['navigation_title'] == '') $page['navigation_title'] = $page['title'];

				// insert page, store the id, we need it when building the blocks
				$page['revision_id'] = BackendPagesModel::insert($page);

				// init var
				$hasBlock = false;

				// build blocks
				$blocks = array();

				// loop blocks in template
				for($i = 0; $i < $this->templates[$page['template_id']]['num_blocks']; $i++)
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
						$html = $this->frm->getField('block_html_' . $i)->getValue();
					}

					// not HTML
					else
					{
						// type of block
						if(isset($this->extras[$extraId]['type']) && $this->extras[$extraId]['type'] == 'block')
						{
							// set error
							if($hasBlock) $this->frm->getField('block_extra_id_' . $i)->addError('Can\'t add 2 blocks');

							// reset var
							$hasBlock = true;
						}
					}

					// build block
					$block = array();
					$block['id'] = BackendPagesModel::getMaximumBlockId() + ($i + 1);
					$block['revision_id'] = $page['revision_id'];
					$block['extra_id'] = $extraId;
					$block['html'] = $html;
					$block['status'] = 'active';
					$block['created_on'] = BackendModel::getUTCDate();
					$block['edited_on'] = $block['created_on'];

					// add block
					$blocks[] = $block;
				}

				// insert the blocks
				BackendPagesModel::insertBlocks($blocks, $hasBlock);

				// save tags
				BackendTagsModel::saveTags($page['id'], $this->frm->getField('tags')->getValue(), $this->URL->getModule());

				// build the cache
				BackendPagesModel::buildCache(BL::getWorkingLanguage());

				// active
				if($page['status'] == 'active')
				{
					// add search index
					if(is_callable(array('BackendSearchModel', 'addIndex')))
					{
						// init var
						$text = '';

						// build search-text
						foreach($blocks as $block) $text .= ' ' . $block['html'];

						// add
						BackendSearchModel::addIndex('pages', $page['id'], array('title' => $page['title'], 'text' => $text));
					}

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

?>