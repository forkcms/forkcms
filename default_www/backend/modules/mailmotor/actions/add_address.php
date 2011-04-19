<?php

/**
 * This is the add-action, it will display a form to create a new subscriber
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendMailmotorAddAddress extends BackendBaseActionAdd
{
	/**
	 * The given group ID
	 *
	 * @var	int
	 */
	private $groupId;


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// fetch group ID
		$this->groupId = $this->getParameter('group_id', 'int');

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
		// create form
		$this->frm = new BackendForm('add');

		// create elements
		$this->frm->addText('email');

		// fetch groups
		$groups = BackendMailmotorModel::getGroupsForCheckboxes();

		// if no groups are found, redirect to overview
		if(empty($groups)) $this->redirect(BackendModel::createURLForAction('addresses') . '&error=no_groups');

		// add checkboxes for groups
		$this->frm->addMultiCheckbox('groups', $groups, $this->groupId);
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
			$this->frm->getField('email')->isFilled(BL::err('EmailIsRequired'));

			// get addresses
			$addresses = (array) explode(',', $this->frm->getField('email')->getValue());

			// loop addresses
			foreach($addresses as $email)
			{
				// validate email
				if(!SpoonFilter::isEmail(trim($email)))
				{
					// add error if needed
					$this->frm->getField('email')->addError(BL::err('ContainsInvalidEmail'));

					// stop looking
					break;
				}
			}

			$this->frm->getField('groups')->isFilled(BL::err('ChooseAtLeastOneGroup'));

			// no errors?
			if($this->frm->isCorrect())
			{
				// build item
				$item = $this->frm->getValues();
				$item['source'] = BL::lbl('Manual');
				$item['created_on'] = BackendModel::getUTCDate('Y-m-d H:i:s');

				// loop the groups
				foreach($item['groups'] as $group)
				{
					foreach($addresses as $email)
					{
						BackendMailmotorCMHelper::subscribe(trim($email), $group);
					}
				}

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('addresses') . (!empty($this->groupId) ? '&group_id=' . $this->groupId : '') . '&report=added');
			}
		}
	}
}

?>