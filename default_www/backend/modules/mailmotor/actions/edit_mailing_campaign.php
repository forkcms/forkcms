<?php

/**
 * This is the edit-action, it will display a form to edit a mailing's campaign
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendMailmotorEditMailingCampaign extends BackendBaseActionEdit
{
	/**
	 * Campaign ID
	 *
	 * @var	int
	 */
	private $campaigns;


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the item exist
		if(BackendMailmotorModel::existsMailing($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get all data for the item we want to edit
			$this->getData();

			// load the form
			$this->loadForm();

			// validate the form
			$this->validateForm();

			// parse the
			$this->parse();

			// display the page
			$this->display();
		}

		// no item found, throw an exceptions, because somebody is fucking with our URL
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}


	/**
	 * Get the data
	 *
	 * @return	void
	 */
	private function getData()
	{
		// get the record
		$this->record = (array) BackendMailmotorModel::getMailing($this->id);

		// get the campagins
		$this->campaigns = (array) BackendMailmotorModel::getCampaignsAsPairs();

		// no item found, throw an exceptions, because somebody is fucking with our URL
		if(empty($this->record)) $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
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
		$this->frm->addDropdown('campaigns', $this->campaigns, $this->record['campaign_id']);
	}


	/**
	 * Parse the form
	 *
	 * @return	void
	 */
	protected function parse()
	{
		// call parent
		parent::parse();

		// assign the active record and additional variables
		$this->tpl->assign('group', $this->record);
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
			$ddmCampaigns = $this->frm->getField('campaigns');

			// no errors?
			if($this->frm->isCorrect())
			{
				// build item
				$item['id'] = $this->id;
				$item['campaign_id'] = $ddmCampaigns->getValue();

				// update the item
				BackendMailmotorModel::updateMailing($item);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') . '&report=edited&var=' . urlencode($this->record['name']) . '&highlight=id-' . $item['id']);
			}
		}
	}
}

?>