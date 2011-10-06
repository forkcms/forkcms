<?php

/**
 * This is the add-action, it will display a form to create a new item
 *
 * @package		backend
 * @subpackage	events
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.0
 */
class BackendEventsAdd extends BackendBaseActionAdd
{

	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

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
		// get default category id
		$defaultCategoryId = BackendModel::getModuleSetting('events', 'default_category_' . BL::getWorkingLanguage());

		// create form
		$this->frm = new BackendForm('add');

		// set hidden values
		$rbtHiddenValues[] = array('label' => BL::lbl('Hidden', $this->URL->getModule()), 'value' => 'Y');
		$rbtHiddenValues[] = array('label' => BL::lbl('Published'), 'value' => 'N');

		// create elements
		$this->frm->addText('title', null, null, 'inputText title', 'inputTextError title');
		$this->frm->addDate('starts_on_date', null);
		$this->frm->addTime('starts_on_time', null);
		$this->frm->addDate('ends_on_date', '');
		$this->frm->addTime('ends_on_time', '');
		$this->frm->addEditor('text');
		$this->frm->addImage('image');
		$this->frm->addEditor('introduction');
		$this->frm->addRadiobutton('hidden', $rbtHiddenValues, 'N');
		$this->frm->addCheckbox('allow_subscriptions', false);
		$this->frm->addText('max_subscriptions');
		$this->frm->addCheckbox('allow_comments', BackendModel::getModuleSetting('events', 'allow_comments', false));
		$this->frm->addDropdown('category_id', BackendEventsModel::getCategories(), $defaultCategoryId);
		$this->frm->addDropdown('user_id', BackendUsersModel::getUsers(), BackendAuthentication::getUser()->getUserId());
		$this->frm->addText('tags', null, null, 'inputText tagBox', 'inputTextError tagBox');
		$this->frm->addDate('publish_on_date');
		$this->frm->addTime('publish_on_time');
		$this->frm->addCheckbox('in_the_picture');

		// meta
		$this->meta = new BackendMeta($this->frm, null, 'title', true);
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

			$image = $this->frm->getField('image');
			if($image->isFilled())
			{
				if($image->isAllowedExtension(array('jpg', 'png', 'gif'), sprintf(BL::err('ExtensionNotAllowed'), 'jpg, png, gif')))
				{
					$filename = time() . '.' . $image->getExtension();
					$image->moveFile(FRONTEND_FILES_PATH . '/userfiles/images/events/' . $filename);
				}
			}

			// validate meta
			$this->meta->validate();

			// no errors?
			if($this->frm->isCorrect())
			{
				// build item
				$item['id'] = (int) BackendEventsModel::getMaximumId() + 1;
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
				$item['created_on'] = BackendModel::getUTCDate();
				$item['edited_on'] = $item['created_on'];
				$item['hidden'] = $this->frm->getField('hidden')->getValue();
				$item['allow_comments'] = $this->frm->getField('allow_comments')->getChecked() ? 'Y' : 'N';
				$item['allow_subscriptions'] = $this->frm->getField('allow_subscriptions')->getChecked() ? 'Y' : 'N';
				$item['max_subscriptions'] = ($item['allow_subscriptions'] == 'Y' && $this->frm->getField('max_subscriptions')->isFilled()) ? (int) $this->frm->getField('max_subscriptions')->getValue() : null;
				$item['num_comments'] = 0;
				$item['status'] = $status;
				$item['image'] = $image->isFilled()? $filename : null;

				// insert the item
				$item['revision_id'] = BackendEventsModel::insert($item);

				// save the tags
				BackendTagsModel::saveTags($item['revision_id'], $this->frm->getField('tags')->getValue(), $this->URL->getModule());

				// active
				if($item['status'] == 'active')
				{
					// add search index
					if(method_exists('BackendSearchModel', 'addIndex')) BackendSearchModel::addIndex('events', $item['id'], array('title' => $item['title'], 'text' => $item['text']));

					// ping
					if(BackendModel::getModuleSetting('events', 'ping_services', false)) BackendModel::ping(SITE_URL . BackendModel::getURLForBlock('events', 'detail') . '/' . $this->meta->getURL());

					// everything is saved, so redirect to the overview
					$this->redirect(BackendModel::createURLForAction('index') . '&report=added&var=' . urlencode($item['title']) . '&highlight=row-' . $item['revision_id']);
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