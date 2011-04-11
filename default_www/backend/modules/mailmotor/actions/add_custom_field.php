<?php

/**
 * This is the add-action, it will display a form to create a new custom field
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendMailmotorAddCustomField extends BackendBaseActionAdd
{
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
		$this->frm->addText('name');
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
				if(in_array($txtName->getValue(), $this->group['custom_fields'])) $txtName->addError(BL::err('CustomFieldExists'));
			}

			// no errors?
			if($this->frm->isCorrect())
			{
				try
				{
					// add the new item to the custom fields list
					$this->group['custom_fields'][] = $txtName->getValue();

					// set the group fields by flipping the custom fields array for this group
					$groupFields = array_flip($this->group['custom_fields']);

					// group custom fields found
					if(!empty($groupFields))
					{
						// loop the group fields and empty every value
						foreach($groupFields as &$field) $field = '';
					}

					// addresses found and custom field delete with CM
					BackendMailmotorCMHelper::createCustomField($txtName->getValue(), $this->group['id']);

					// update custom fields for this group
					BackendMailmotorModel::updateCustomFields($groupFields, $this->group['id']);
				}

				// exception was triggered
				catch(Exception $e)
				{
					// redirect with a custom error
					$this->redirect(BackendModel::createURLForAction('custom_fields') . '&group_id=' . $this->group['id'] . '&error=campaign-monitor-error&var=' . urlencode($e->getMessage()));
				}

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('custom_fields') . '&group_id=' . $this->group['id'] . '&report=added&var=' . urlencode($txtName->getValue()) . '&highlight=id-' . $this->group['id']);
			}
		}
	}
}

?>