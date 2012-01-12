<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the edit-action, it will display a form to edit a mailing's campaign
 *
 * @author Dave Lens <dave.lens@netlash.com>
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
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the item exist
		if(BackendMailmotorModel::existsMailing($this->id))
		{
			parent::execute();
			$this->getData();
			$this->loadForm();
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
		$this->record = (array) BackendMailmotorModel::getMailing($this->id);

		// get the campagins
		$this->campaigns = (array) BackendMailmotorModel::getCampaignsAsPairs();

		// no item found, throw an exceptions, because somebody is fucking with our URL
		if(empty($this->record)) $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}

	/**
	 * Load the form
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
	 */
	protected function parse()
	{
		parent::parse();

		// assign the active record and additional variables
		$this->tpl->assign('group', $this->record);
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

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_edit_mailing', array('item' => $item));

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') . '&report=edited&var=' . urlencode($this->record['name']) . '&highlight=id-' . $item['id']);
			}
		}
	}
}
