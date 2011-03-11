<?php

/**
 * BackendMailmotorEditCustomField
 * This is the edit-action, it will display a form to edit an existing custom field
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendMailmotorEditCustomField extends BackendBaseActionAdd
{
	/**
	 * The field name
	 *
	 * @var	array
	 */
	private $field;


	/**
	 * The group record
	 *
	 * @var	array
	 */
	private $group;


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// get data related to custom fields
		$this->getData();

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
	 * Gets data related to custom fields
	 *
	 * @return	void
	 */
	private function getData()
	{
		// get passed group ID
		$id = SpoonFilter::getGetValue('group_id', null, 0, 'int');

		// fetch group record
		$this->group = BackendMailmotorModel::getGroup($id);

		// group doesn't exist
		if(empty($this->group)) $this->redirect(BackendModel::createURLForAction('groups') . '&error=non-existing');

		// get passed field name
		$this->field = SpoonFilter::getGetValue('field', $this->group['custom_fields'], '');

		// field does not exist for this group
		if(empty($this->field)) $this->redirect(BackendModel::createURLForAction('groups') . '&error=non-existing');
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('edit');

		// create elements
		$this->frm->addText('name', $this->field);
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
			$txtName = $this->frm->getField('name');

			// validate fields
			if($txtName->isFilled(BL::err('NameIsRequired')))
			{
				if(in_array($txtName->getValue(), $this->group['custom_fields']) && $txtName->getValue() != $this->field) $txtName->addError(BL::err('CustomFieldExists'));
			}

			// no errors?
			if($this->frm->isCorrect())
			{
				/*
					Since CampaignMonitor has no updateCustomField function, we have to find a way around that
				*/

				try
				{
					// fetch key by value
					$key = array_search($this->field, $this->group['custom_fields']);

					// overwrite the name
					$this->group['custom_fields'][$key] = $txtName->getValue();

					// fetch all addresses for this group
					$addresses = BackendMailmotorModel::getAddressesByGroupID($this->group['id']);

					// addresses found and custom field delete with CM
					if(!empty($addresses) && BackendMailmotorCMHelper::deleteCustomField($this->field, $this->group['id']))
					{
						// loop addresses
						foreach($addresses as $address)
						{
							// fetch custom fields for this address
							$fields = BackendMailmotorModel::getCustomFieldsByAddress($address['email']);

							// add the new field with the old value to the stack
							$fields[$this->group['id']][$txtName->getValue()] = $fields[$this->group['id']][$this->field];

							// remove the old field
							unset($fields[$this->group['id']][$this->field]);

							// update the user with the new custom fields
							BackendMailmotorCMHelper::subscribe($address['email'], $this->group['id'], $fields[$this->group['id']]);
						}
					}

					// no adresses present in this group
					elseif(BackendMailmotorCMHelper::deleteCustomField($this->field, $this->group['id']))
					{
						// fetch custom fields for this address
						$fields = BackendMailmotorModel::getCustomFields($this->group['id']);

						// add the new field to the stack
						$fields[array_search($this->field, $fields)] = $txtName->getValue();

						// update custom fields in Fork for the active group
						BackendMailmotorModel::updateCustomFields($fields, $this->group['id']);

						// re-insert the field in CampaignMonitor
						BackendMailmotorCMHelper::createCustomField($txtName->getValue(), $this->group['id']);
					}

					// no field created
					else throw new SpoonException('Custom field not edited, please try again.');
				}

				// exception was triggered
				catch(Exception $e)
				{
					// redirect with a custom error
					$this->redirect(BackendModel::createURLForAction('custom_fields') . '&group_id=' . $this->group['id'] . '&error=campaign-monitor-error&var=' . urlencode($e->getMessage()));
				}

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('custom_fields') . '&group_id=' . $this->group['id'] . '&report=edited&var=' . urlencode($txtName->getValue()) . '&highlight=id-' . $this->group['id']);
			}
		}
	}
}

?>