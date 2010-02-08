<?php

/**
 * BackendPagesAdd
 *
 * This is the add-action, it will display a form to create a new pages item
 *
 * @package		backend
 * @subpackage	pages
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
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
		$this->header->addJavascript('jstree/jquery.tree.js');
		$this->header->addJavascript('jstree/lib/jquery.cookie.js');
		$this->header->addJavascript('jstree/plugins/jquery.tree.cookie.js');

		// add css
		$this->header->addCSS('/backend/modules/pages/js/jstree/themes/fork/style.css', null, true);

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
		$defaultTemplateId = BackendModel::getSetting('core', 'default_template', 1);

		// init var
		$templatesForDropdown = array();

		// build values
		foreach($this->templates as $templateId => $row)
		{
			// set value
			$templatesForDropdown[$templateId] = $row['label'];

			// set checked
			if($templateId == $defaultTemplateId) $this->templates[$templateId]['checked'] = true;
		}

		// create form
		$this->frm = new BackendForm('add');

		// assign in template
		$this->tpl->assign('defaultTemplateId', $defaultTemplateId);

		// create elements
		$this->frm->addTextField('title');
		$this->frm->addDropDown('template_id', $templatesForDropdown, $defaultTemplateId);
		$this->frm->addRadioButton('hidden', array(array('label' => BL::getLabel('Hidden'), 'value' => 'Y'), array('label' => BL::getLabel('Published'), 'value' => 'N')), 'N');
		$this->frm->addCheckBox('no_follow');

		// get maximum number of blocks
		$maximumNumberOfBlocks = BackendModel::getSetting('core', 'template_max_blocks', 5);

		// build blocks array
		for($i = 0; $i < $maximumNumberOfBlocks; $i++)
		{
			$this->blocks[$i]['formElements']['ddmExtraId'] = $this->frm->addDropDown('block_extra_id_'. $i, $this->extras['dropdown']);
			$this->blocks[$i]['formElements']['txtHTML'] = $this->frm->addEditorField('block_html_'. $i, '');

			// set types
			foreach($this->extras['types'] as $value => $type) $this->frm->getField('block_extra_id_'. $i)->setOptionAttributes($value, array('rel' => $type));
		}

		// page info
		$this->frm->addCheckBox('navigation_title_overwrite');
		$this->frm->addTextField('navigation_title');

		// tags
		$this->frm->addTextField('tags', null, null, 'inputTextfield tagBox', 'inputTextfieldError tagBox');

		// meta
		$this->meta = new BackendMeta($this->frm, null, 'title', true);

		// add button
		$this->frm->addButton('add', ucfirst(BL::getLabel('Add')), 'submit', 'inputButton button mainButton');
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
		$this->tpl->assign('extras', $this->extras['data']);

		// get default template id
		$defaultTemplateId = BackendModel::getSetting('core', 'default_template', 1);

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
			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// validate fields
			$this->frm->getField('title')->isFilled(BL::getError('TitleIsRequired'));

			// validate meta
			$this->meta->validate();

			// no errors?
			if($this->frm->isCorrect())
			{
				// init var
				$parentId = 0;

				// set callback for generating an unique URL
				$this->meta->setUrlCallback('BackendPagesModel', 'getURL', array($parentId));

				// build page record
				$page = array();
				$page['id'] = BackendPagesModel::getMaximumMenuId() + 1;
				$page['user_id'] = BackendAuthentication::getUser()->getUserId();
				$page['parent_id'] = $parentId;
				$page['template_id'] = (int) $this->frm->getField('template_id')->getValue();
				$page['meta_id'] = (int) $this->meta->save();
				$page['language'] = BackendLanguage::getWorkingLanguage();
				$page['type'] = 'root';
				$page['title'] = $this->frm->getField('title')->getValue();
				$page['navigation_title'] = $this->frm->getField('navigation_title')->getValue();
				$page['navigation_title_overwrite'] = ($this->frm->getField('navigation_title_overwrite')->isChecked()) ? 'Y' : 'N';
				$page['hidden'] = $this->frm->getField('hidden')->getValue();
				$page['status'] = 'active';
				$page['publish_on'] = BackendModel::getUTCDate();
				$page['created_on'] = BackendModel::getUTCDate();
				$page['edited_on'] = BackendModel::getUTCDate();
				$page['allow_move'] = 'Y';
				$page['allow_children'] = 'Y';
				$page['allow_edit'] = 'Y';
				$page['allow_delete'] = 'Y';
				$page['no_follow'] = ($this->frm->getField('no_follow')->isChecked()) ? 'Y' : 'N';
				$page['sequence'] = BackendPagesModel::getMaximumSequence($parentId) + 1;

				if($page['navigation_title'] == '') $page['navigation_title'] = $page['title'];

				// insert page, store the id, we need it when building the blocks
				$revisionId = BackendPagesModel::insert($page);

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
					$block['id'] = BackendPagesModel::getMaximumBlockId() + ($i + 1);
					$block['revision_id'] = $revisionId;
					$block['extra_id'] = $extraId;
					$block['html'] = $html;
					$block['status'] = 'active';
					$block['created_on'] = BackendModel::getUTCDate();
					$block['edited_on'] = BackendModel::getUTCDate();

					// add block
					$blocks[] = $block;
				}

				// insert the blocks
				BackendPagesModel::insertBlocks($blocks, $hasBlock);

				// save tags
				BackendTagsModel::saveTags($page['id'], $this->frm->getField('tags')->getValue(), $this->URL->getModule());

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('edit') .'&id='. $page['id'] .'&report=added&var='. urlencode($page['title']) .'&highlight=id-'. $page['id']);
			}
		}
	}
}

?>