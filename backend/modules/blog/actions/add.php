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
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Dave Lens <dave.lens@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Matthias Mullie <matthias@mullie.eu>
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 */
class BackendBlogAdd extends BackendBaseActionAdd
{
	/**
	 * Is the image field allowed?
	 *
	 * @var bool
	 */
	protected $imageIsAllowed = true;

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
		$this->imageIsAllowed = BackendModel::getModuleSetting($this->URL->getModule(), 'show_image_form', true);

		$this->frm = new BackendForm('add');

		// set hidden values
		$rbtHiddenValues[] = array('label' => BL::lbl('Hidden', $this->URL->getModule()), 'value' => 'Y');
		$rbtHiddenValues[] = array('label' => BL::lbl('Published'), 'value' => 'N');

		// get categories
		$categories = BackendBlogModel::getCategories();
		$categories['new_category'] = SpoonFilter::ucfirst(BL::getLabel('AddCategory'));

		// create elements
		$this->frm->addText('title', null, null, 'inputText title', 'inputTextError title');
		$this->frm->addEditor('text');
		$this->frm->addEditor('introduction');
		$this->frm->addRadiobutton('hidden', $rbtHiddenValues, 'N');
		$this->frm->addCheckbox('allow_comments', BackendModel::getModuleSetting($this->getModule(), 'allow_comments', false));
		$this->frm->addDropdown('category_id', $categories, SpoonFilter::getGetValue('category', null, null, 'int'));
		if(count($categories) != 2) $this->frm->getField('category_id')->setDefaultElement('');
		$this->frm->addDropdown('user_id', BackendUsersModel::getUsers(), BackendAuthentication::getUser()->getUserId());
		$this->frm->addText('tags', null, null, 'inputText tagBox', 'inputTextError tagBox');
		$this->frm->addDate('publish_on_date');
		$this->frm->addTime('publish_on_time');
		if($this->imageIsAllowed) $this->frm->addImage('image');

		// meta
		$this->meta = new BackendMeta($this->frm, null, 'title', true);
	}

	/**
	 * Parse the form
	 */
	protected function parse()
	{
		parent::parse();
		$this->tpl->assign('imageIsAllowed', $this->imageIsAllowed);

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
		// is the form submitted?
		if($this->frm->isSubmitted())
		{
			// get the status
			$status = SpoonFilter::getPostValue('status', array('active', 'draft'), 'active');

			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// validate fields
			$this->frm->getField('title')->isFilled(BL::err('TitleIsRequired'));
			$this->frm->getField('text')->isFilled(BL::err('FieldIsRequired'));
			$this->frm->getField('publish_on_date')->isValid(BL::err('DateIsInvalid'));
			$this->frm->getField('publish_on_time')->isValid(BL::err('TimeIsInvalid'));
			$this->frm->getField('category_id')->isFilled(BL::err('FieldIsRequired'));
			if($this->frm->getField('category_id')->getValue() == 'new_category') $this->frm->getField('category_id')->addError(BL::err('FieldIsRequired'));

			if($this->imageIsAllowed)
			{
				// validate the image
				if($this->frm->getField('image')->isFilled())
				{
					// image extension and mime type
					$this->frm->getField('image')->isAllowedExtension(array('jpg', 'png', 'gif', 'jpeg'), BL::err('JPGGIFAndPNGOnly'));
					$this->frm->getField('image')->isAllowedMimeType(array('image/jpg', 'image/png', 'image/gif', 'image/jpeg'), BL::err('JPGGIFAndPNGOnly'));
				}
			}

			// validate meta
			$this->meta->validate();

			if($this->frm->isCorrect())
			{
				// build item
				$item['id'] = (int) BackendBlogModel::getMaximumId() + 1;
				$item['meta_id'] = $this->meta->save();
				$item['category_id'] = (int) $this->frm->getField('category_id')->getValue();
				$item['user_id'] = $this->frm->getField('user_id')->getValue();
				$item['language'] = BL::getWorkingLanguage();
				$item['title'] = $this->frm->getField('title')->getValue();
				$item['introduction'] = $this->frm->getField('introduction')->getValue();
				$item['text'] = $this->frm->getField('text')->getValue();
				$item['publish_on'] = BackendModel::getUTCDate(null, BackendModel::getUTCTimestamp($this->frm->getField('publish_on_date'), $this->frm->getField('publish_on_time')));
				$item['created_on'] = BackendModel::getUTCDate();
				$item['edited_on'] = $item['created_on'];
				$item['hidden'] = $this->frm->getField('hidden')->getValue();
				$item['allow_comments'] = $this->frm->getField('allow_comments')->getChecked() ? 'Y' : 'N';
				$item['num_comments'] = 0;
				$item['status'] = $status;

				if($this->imageIsAllowed)
				{
					// the image path
					$imagePath = FRONTEND_FILES_PATH . '/blog/images';

					// validate the image
					if($this->frm->getField('image')->isFilled())
					{
						// build the image name
						$item['image'] = $this->meta->getURL() . '.' . $this->frm->getField('image')->getExtension();

						// upload the image
						$this->frm->getField('image')->moveFile($imagePath . '/source/' . $item['image']);
					}
				}

				// insert the item
				$item['revision_id'] = BackendBlogModel::insert($item);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_add', array('item' => $item));

				// save the tags
				BackendTagsModel::saveTags($item['id'], $this->frm->getField('tags')->getValue(), $this->URL->getModule());

				// active
				if($item['status'] == 'active')
				{
					// add search index
					BackendSearchModel::saveIndex($this->getModule(), $item['id'], array('title' => $item['title'], 'text' => $item['text']));

					// ping
					if(BackendModel::getModuleSetting($this->getModule(), 'ping_services', false)) BackendModel::ping(SITE_URL . BackendModel::getURLForBlock('blog', 'detail') . '/' . $this->meta->getURL());

					// everything is saved, so redirect to the overview
					$this->redirect(BackendModel::createURLForAction('index') . '&report=added&var=' . urlencode($item['title']) . '&highlight=row-' . $item['revision_id']);
				}

				// draft
				elseif($item['status'] == 'draft')
				{
					// everything is saved, so redirect to the edit action
					$this->redirect(BackendModel::createURLForAction('edit') . '&report=saved-as-draft&var=' . urlencode($item['title']) . '&id=' . $item['id'] . '&draft=' . $item['revision_id'] . '&highlight=row-' . $item['revision_id']);
				}
			}
		}
	}
}
