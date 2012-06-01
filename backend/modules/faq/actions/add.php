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
 * @author Lester Lievens <lester.lievens@netlash.com>
 * @author Matthias Mullie <matthias@mullie.eu>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 */
class BackendFaqAdd extends BackendBaseActionAdd
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

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
		// create form
		$this->frm = new BackendForm('add');

		// set hidden values
		$rbtHiddenValues[] = array('label' => BL::lbl('Hidden', $this->URL->getModule()), 'value' => 'Y');
		$rbtHiddenValues[] = array('label' => BL::lbl('Published'), 'value' => 'N');

		// get categories
		$categories = BackendFaqModel::getCategories();

		// create elements
		$this->frm->addText('title', null, null, 'inputText title', 'inputTextError title');
		$this->frm->addEditor('answer');
		$this->frm->addRadiobutton('hidden', $rbtHiddenValues, 'N');
		$this->frm->addDropdown('category_id', $categories);
		$this->frm->addText('tags', null, null, 'inputText tagBox', 'inputTextError tagBox');

		// meta
		$this->meta = new BackendMeta($this->frm, null, 'title', true);
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		parent::parse();

		// get url
		$url = BackendModel::getURLForBlock($this->URL->getModule(), 'detail');
		$url404 = BackendModel::getURL(404);

		// parse additional variables
		if($url404 != $url) $this->tpl->assign('detailURL', SITE_URL . $url);
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
			$this->frm->getField('title')->isFilled(BL::err('QuestionIsRequired'));
			$this->frm->getField('answer')->isFilled(BL::err('AnswerIsRequired'));
			$this->frm->getField('category_id')->isFilled(BL::err('CategoryIsRequired'));
			$this->meta->validate();

			if($this->frm->isCorrect())
			{
				// build item
				$item['meta_id'] = $this->meta->save();
				$item['category_id'] = $this->frm->getField('category_id')->getValue();
				$item['user_id'] = BackendAuthentication::getUser()->getUserId();
				$item['language'] = BL::getWorkingLanguage();
				$item['question'] = $this->frm->getField('title')->getValue();
				$item['answer'] = $this->frm->getField('answer')->getValue(true);
				$item['created_on'] = BackendModel::getUTCDate();
				$item['hidden'] = $this->frm->getField('hidden')->getValue();
				$item['sequence'] = BackendFaqModel::getMaximumSequence($this->frm->getField('category_id')->getValue()) + 1;

				// save the data
				$item['id'] = BackendFaqModel::insert($item);
				BackendTagsModel::saveTags($item['id'], $this->frm->getField('tags')->getValue(), $this->URL->getModule());
				BackendModel::triggerEvent($this->getModule(), 'after_add', array('item' => $item));

				// add search index
				BackendSearchModel::saveIndex('faq', $item['id'], array('title' => $item['question'], 'text' => $item['answer']));
				$this->redirect(BackendModel::createURLForAction('index') . '&report=added&var=' . urlencode($item['question']) . '&highlight=row-' . $item['id']);
			}
		}
	}
}
