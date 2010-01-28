<?php

// @todo publish box? does this need anything still?
// @todo tags don't save yet for some reason
// @todo fix URL below title and in SEO tab
// @todo javascript show/hide for summary
// @todo permissions tab with allow_comments checkbox

/**
 * BackendBlogAddCategory
 *
 * This is the add-action, it will display a form to create a new post
 *
 * @package		backend
 * @subpackage	blog
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendBlogAdd extends BackendBaseActionAdd
{
	/**
	 * The categories
	 *
	 * @var	array
	 */
	private $categories = array();


	/**
	 * The users
	 *
	 * @var	array
	 */
	private $users = array();


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// get categories
		$this->categories = BackendBlogModel::getCategories();

		// get users
		$this->users = BackendUsersModel::getUsers();

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
		$defaultCategoryId = BackendModel::getSetting('blog', 'default_category', 1);

		// create form
		$this->frm = new BackendForm('add');

		// create elements
		$this->frm->addTextField('title');
		$this->frm->addEditorField('text');
		$this->frm->addEditorField('introduction');
		$this->frm->addRadioButton('hidden', array(array('label' => BL::getLabel('Hidden'), 'value' => 'Y'), array('label' => BL::getLabel('Published'), 'value' => 'N')), 'N');
		$this->frm->addDropDown('category_id', $this->categories, $defaultCategoryId);
		$this->frm->addDropDown('user_id', $this->users, BackendAuthentication::getUser()->getUserId());
		$this->frm->addTextField('tags', null, null, 'inputTextfield tagBox', 'inputTextfieldError tagBox');
		$this->frm->addDateField('publish_on_date');
		$this->frm->addTimeField('publish_on_time');

		// meta
		$this->meta = new BackendMeta($this->frm, null, 'title', true);

		// add button
		$this->frm->addButton('add', ucfirst(BL::getLabel('Add')), 'submit', 'inputButton button mainButton');
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

			// shorten fields
			/* @var $txtTitle SpoonTextField */				$txtTitle = $this->frm->getField('title');
			/* @var $txtIntroduction SpoonTextArea */		$txtIntroduction = $this->frm->getField('introduction');
			/* @var $txtText SpoonTextArea */				$txtText = $this->frm->getField('introduction');
			/* @var $txtPublishDate SpoonDateField */		$txtPublishDate = $this->frm->getField('publish_on_date');
			/* @var $txtPublishTime SpoonTimeField */		$txtPublishTime = $this->frm->getField('publish_on_time');
			/* @var $ddmUserId SpoonDropDown */				$ddmUserId = $this->frm->getField('user_id');
			/* @var $ddmCategoryId SpoonDropDown */			$ddmCategoryId = $this->frm->getField('category_id');
			/* @var $rbtHidden SpoonRadioButton */			$rbtHidden = $this->frm->getField('hidden');

			// validate fields
			$this->frm->getField('title')->isFilled(BL::getError('TitleIsRequired'));

			// validate meta
			$this->meta->validate();

			// no errors?
			if($this->frm->isCorrect())
			{
				// set callback for generating an unique url
				$this->meta->setURLCallback('BackendBlogModel', 'getURL', array($txtTitle->getValue()));

				// set formatted date and time
				$formattedDate = SpoonDate::getDate('Y-m-d', $txtPublishDate->getTimestamp());
				$formattedTime = SpoonDate::getDate('H:i:s', strtotime($txtPublishTime->getValue())); // @todo switch this to $txtPublishTime->getTimestamp whenever it is available

				// build item
				$item['meta_id'] = $this->meta->save();
				$item['category_id'] = $ddmCategoryId->getValue();
				$item['user_id'] = $ddmUserId->getValue();
				$item['language'] = BL::getWorkingLanguage();
				$item['title'] = $txtTitle->getValue();
				$item['introduction'] = $txtIntroduction->getValue();
				$item['text'] = $txtText->getValue();
				$item['status'] = 'active';
				$item['publish_on'] = $formattedDate.' '.$formattedTime;
				$item['created_on'] = date('Y-m-d H:i:s');
				$item['edited_on'] = date('Y-m-d H:i:s');
				$item['hidden'] = $rbtHidden->getValue();
				$item['allow_comments'] = 'Y'; // @todo needs value from inputfield
				$item['num_comments'] = 0;

				// insert the item
				$id = BackendBlogModel::insert($item);

				// save the tags
				BackendTagsModel::saveTags($id, $this->frm->getField('tags')->getValue(), $this->url->getModule());

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') .'&report=added&var='. urlencode($item['title']) .'&highlight=id-'. $id);
			}
		}
	}
}

?>