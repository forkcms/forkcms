<?php

/**
 * This is the import-action, it will import groups and their subscribers from CampaignMonitor
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendMailmotorImportGroups extends BackendBaseActionAdd
{
	/**
	 * CampaignMonitor object
	 *
	 * @var	CampaignMonitor
	 */
	private $cm;


	/**
	 * All external groups
	 *
	 * @var	array
	 */
	private $externalGroups = array();


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// store CM object
		$this->cm = BackendMailmotorCMHelper::getCM();

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
		$this->frm = new BackendForm('import');

		// fetch the groups
		$this->externalGroups = $this->cm->getListsByClientId();

		// loop the groups
		foreach($this->externalGroups as &$group)
		{
			// add subscribers + count to the group stack
			$group['subscribers'] = BackendMailmotorCMHelper::getSubscribers($group['id']);
			$group['subscribers_amount'] = count($group['subscribers']);

			// get the custom fields
			$customFields = $this->cm->getCustomFields($group['id']);

			// skip this if no custom fields were found
			if(!empty($customFields))
			{
				// loop the custom fields
				foreach($customFields as &$field)
				{
					// save only field name in a new format
					$field = $field['name'];
				}
			}

			// add custom fields to the group stack
			$group['custom_fields'] = empty($customFields) ? null : serialize($customFields);

		}

		// parse the groups
		$this->tpl->assign('groups', $this->externalGroups);
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

			// no errors?
			if($this->frm->isCorrect())
			{
				// the total amount of subscribers
				$subscribersTotal = 0;

				// loop all groups
				foreach($this->externalGroups as $group)
				{
					// insert them in our database
					$groupID = BackendModel::getDB(true)->insert('mailmotor_groups', array('name' => $group['name'], 'custom_fields' => $group['custom_fields'], 'created_on' => BackendModel::getUTCDate()));

					// insert the CM ID
					BackendMailmotorCMHelper::insertCampaignMonitorID('list', $group['id'], $groupID);

					// continue looping if this group has no subscribers
					if(empty($group['subscribers'])) continue;

					// add this groups subscribers amount to the total
					$subscribersTotal += $group['subscribers_amount'];

					// loop the subscribers for this group, and import them
					foreach($group['subscribers'] as $subscriber)
					{
						// build new subscriber record
						$item = array();
						$item['email'] = $subscriber['email'];
						$item['source'] = 'import';
						$item['created_on'] = $subscriber['date'];

						// add an additional custom field 'name', if it was set in the subscriber record
						if(!empty($subscriber['name'])) $subscriber['custom_fields']['Name'] = $subscriber['name'];

						// save the subscriber in our database, and subscribe it to this group
						BackendMailmotorModel::saveAddress($item, $groupID, (!empty($subscriber['custom_fields']) ? $subscriber['custom_fields'] : null));
					}
				}

				// at this point, groups are set
				BackendModel::setModuleSetting('mailmotor', 'cm_groups_set', true);

				// redirect to the index
				$this->redirect(BackendModel::createURLForAction('index', 'mailmotor') . '&report=groups-imported&var[]=' . count($this->externalGroups) . '&var[]=' . $subscribersTotal);
			}
		}
	}
}

?>
