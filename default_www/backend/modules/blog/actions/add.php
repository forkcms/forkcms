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
		$this->frm->addDropDown('user_id', $this->users);
		$this->frm->addTextField('tags', null, null, 'inputTextfield tagBox', 'inputTextfieldError tagBox');

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

			// validate fields
			$this->frm->getField('title')->isFilled(BL::getError('TitleIsRequired'));

			// validate meta
			$this->meta->validate();

			// no errors?
			if($this->frm->isCorrect())
			{
//				// set callback for generating an unique url
//				$this->meta->setUrlCallback('BackendBlogModel', 'getURL', array($parentId));
//
//				// build item
//				$item = array();
//				$item['name'] = $this->frm->getField('name')->getValue();
//				$item['language'] = BL::getWorkingLanguage();
//				$item['url'] = BackendBlogModel::getURLForCategory($item['name']);
//
//				// insert the item
//				$id = BackendBlogModel::insert($item);
//
//				// everything is saved, so redirect to the overview
//				$this->redirect(BackendModel::createURLForAction('index') .'&report=added&var='. urlencode($item['name']) .'&highlight=id-'. $id);
			}
		}
	}
}

?>