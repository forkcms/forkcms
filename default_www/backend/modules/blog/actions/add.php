<?php

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

		// set hidden values
		$rbtHiddenValues[] = array('label' => BL::getLabel('Hidden'), 'value' => 'Y');
		$rbtHiddenValues[] = array('label' => BL::getLabel('Published'), 'value' => 'N');

		// create elements
		$this->frm->addTextField('title');
		$this->frm->addEditorField('text');
		$this->frm->addEditorField('introduction');
		$this->frm->addRadioButton('hidden', $rbtHiddenValues, 'N');
		$this->frm->addCheckBox('allow_comments', BackendModel::getSetting('blog', 'allow_comments', false));
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
	 * parse the form
	 *
	 * @return	void
	 */
	protected function parse()
	{
		// call parent
		parent::parse();

		// parse additional variables
		$this->tpl->assign('blogUrl', SITE_URL); // @todo tijs - need FrontendModel::createURLForAction() for this
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
			$txtTitle = $this->frm->getField('title');
			$txtIntroduction = $this->frm->getField('introduction');
			$txtText = $this->frm->getField('text');
			$txtPublishDate = $this->frm->getField('publish_on_date');
			$txtPublishTime = $this->frm->getField('publish_on_time');
			$ddmUserId = $this->frm->getField('user_id');
			$ddmCategoryId = $this->frm->getField('category_id');
			$rbtHidden = $this->frm->getField('hidden');
			$chkAllowComments = $this->frm->getField('allow_comments');

			// validate fields
			$this->frm->getField('title')->isFilled(BL::getError('TitleIsRequired'));
			$this->frm->getField('text')->isFilled(BL::getError('TextIsRequired'));

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
				$item['publish_on'] = $formattedDate .' '. $formattedTime; // @todo davy - dit moet nog correct naar de UTC tijd vertaald worden.
				$item['created_on'] = BackendModel::getUTCDate();
				$item['edited_on'] = BackendModel::getUTCDate();
				$item['hidden'] = $rbtHidden->getValue();
				$item['allow_comments'] = $chkAllowComments->getChecked() ? 'Y' : 'N';
				$item['num_comments'] = 0;

				// insert the item
				$id = BackendBlogModel::insert($item);

				// save the tags
				BackendTagsModel::saveTags($id, $this->frm->getField('tags')->getValue(), $this->url->getModule());

				// ping
				BackendModel::ping(SITE_URL . BackendModel::getURLForBlock('blog', 'detail') .'/'. $this->meta->getURL());

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') .'&report=added&var='. urlencode($item['title']) .'&highlight=id-'. $id);
			}
		}
	}
}

?>