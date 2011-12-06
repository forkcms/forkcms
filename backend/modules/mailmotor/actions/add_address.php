<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the add-action, it will display a form to create a new subscriber
 *
 * @author Dave Lens <dave.lens@netlash.com>
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
	 */
	public function execute()
	{
		parent::execute();
		$this->groupId = $this->getParameter('group_id', 'int');
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
		$this->frm = new BackendForm('add');
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

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_add_address', array('item' => $item));

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('addresses') . (!empty($this->groupId) ? '&group_id=' . $this->groupId : '') . '&report=added');
			}
		}
	}
}
