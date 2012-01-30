<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the edit-action, it will display a form to edit a subscriber
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class BackendMailmotorEditAddress extends BackendBaseActionEdit
{
	/**
	 * The custom fields
	 *
	 * @var	array
	 */
	private $customFields = array();

	/**
	 * The e-mail parameter
	 *
	 * @var	string
	 */
	private $email;

	/**
	 * The passed group record
	 *
	 * @var	array
	 */
	private $group;

	/**
	 * The subscriptions this e-mail is part of
	 *
	 * @var	array
	 */
	private $subscriptions;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		$this->email = $this->getParameter('email');

		// does the item exist
		if(BackendMailmotorModel::existsAddress($this->email))
		{
			parent::execute();
			$this->getData();
			$this->loadForm();
			$this->loadCustomFields();
			$this->validateForm();
			$this->parse();
			$this->display();
		}

		// no item found, throw an exceptions, because somebody is fucking with our URL
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}

	/**
	 * Get the data
	 */
	private function getData()
	{
		// get the record
		$this->record = (array) BackendMailmotorModel::getAddress($this->email);

		// no item found, throw an exceptions, because somebody is fucking with our URL
		if(empty($this->record)) $this->redirect(BackendModel::createURLForAction('addresses') . '&error=non-existing');

		// get subscriptions (key/pair values)
		$this->subscriptions = BackendMailmotorModel::getGroupsByEmailAsPairs($this->email);

		// the allowed groups
		$allowedGroups = array_keys($this->subscriptions);

		// set the passed group ID
		$this->id = SpoonFilter::getGetValue('group_id', $allowedGroups, key($this->subscriptions), 'int');

		// get group record
		$this->group = BackendMailmotorModel::getGroup($this->id);
	}

	/**
	 * Load the custom fields
	 */
	private function loadCustomFields()
	{
		// no groups or subscriptions at this point
		if(empty($this->group)) return false;

		// reserve counter
		$i = 0;

		// if no custom fields were set, we fetch the ones from the groups ourselves
		$this->group['custom_fields'] = BackendMailmotorModel::getCustomFields($this->id);

		// no custom fields for this group
		if(empty($this->group['custom_fields'])) return false;

		// loop the custom fields
		foreach($this->group['custom_fields'] as $name)
		{
			// set value
			$value = isset($this->record['custom_fields'][$this->id][$name]) ? $this->record['custom_fields'][$this->id][$name] : '';

			// store textfield value
			$this->customFields[$i]['label'] = $name;
			$this->customFields[$i]['name'] = SpoonFilter::toCamelCase($name, array('-', '_', ' '));
			$this->customFields[$i]['formElements']['txtField'] = $this->frm->addText($this->customFields[$i]['name'], $value);
			$i++;

			// unset this field
			unset($this->customFields[$name]);
		}

		// add textfields to form
		$this->tpl->assign('fields', $this->customFields);
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('edit');

		// create elements
		$this->frm->addText('email', $this->email);
		$this->frm->getField('email')->setAttribute('disabled', 'disabled');

		// fetch groups for checkbox format
		$checkboxGroups = BackendMailmotorModel::getGroupsForCheckboxes();

		// if no groups are found, redirect to overview
		if(empty($checkboxGroups)) $this->redirect(BackendModel::createURLForAction('addresses') . '&error=no-groups');

		// add checkboxes for groups
		$this->frm->addMultiCheckbox('groups', $checkboxGroups, $this->record['groups']);

		// add dropdown for subscriptions
		if(!empty($this->subscriptions)) $this->frm->addDropdown('subscriptions', $this->subscriptions, $this->id);
	}

	/**
	 * Parse the form
	 */
	protected function parse()
	{
		parent::parse();

		// assign the active record and additional variables
		$this->tpl->assign('address', $this->record);

		// assign the group record
		$this->tpl->assign('group', $this->group);
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

			// get subscriptions
			if(!empty($this->subscriptions)) $ddmGroups = $this->frm->getField('subscriptions');

			// no errors?
			if($this->frm->isCorrect())
			{
				// build item
				$item['email'] = $this->email;
				$item['groups'] = isset($_POST['groups']) ? $_POST['groups'] : array();

				// loop the fields
				foreach($this->customFields as $field)
				{
					// shorten the field
					$txtField = $field['formElements']['txtField'];

					// add the value to the custom fields to store
					$this->record['custom_fields'][$this->group['id']][$field['label']] = $txtField->getValue();
				}

				/*
				 * This is, in fact, an unsubscribe of the subscriber's current groups, and a re-subscribe of the
				 * groups he requested. This is done because the CM API supports no updateSubscriber function, and it
				 * overwrites the values of custom fields if you do an update for 1 list and don't provide values for another.
				 *
				 * NOTE: A user will still be in the suppression list if he is resubscribed, but he will receive e-mails.
				 * 		 (see: http://www.campaignmonitor.com/forums/viewtopic.php?id=1761)
				 */

				// the groups the user is currently subscribed to
				if(!empty($this->record['groups']))
				{
					// loop the groups
					foreach($this->record['groups'] as $group)
					{
						// Check if this group is in the allowed list. If it is, it should not be unsubscribed
						if(!empty($item['groups']) && in_array($group, $item['groups'])) continue;

						// unsubscribe the user
						BackendMailmotorCMHelper::unsubscribe($this->email, $group);
					}
				}

				// the groups the user wants to keep
				if(!empty($item['groups']))
				{
					// loop the groups
					foreach($item['groups'] as $group)
					{
						// continue looping if this group has no custom fields
						$customFields = !empty($this->record['custom_fields'][$group]) ? $this->record['custom_fields'][$group] : null;

						// resubscribe for this group
						BackendMailmotorCMHelper::subscribe($this->email, $group, $customFields);
					}
				}

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_edit_address', array('item' => $this->record));

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('addresses') . (!empty($this->subscriptions) ? '&group_id=' . $ddmGroups->getValue() : '') . '&report=edited&var=' . urlencode($item['email']) . '&highlight=email-' . $item['email']);
			}
		}
	}
}
