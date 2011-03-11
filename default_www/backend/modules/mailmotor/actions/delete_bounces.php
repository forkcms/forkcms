<?php

/**
 * BackendMailmotorDeleteBounces
 * This action will delete all bounces for a specified mailing
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendMailmotorDeleteBounces extends BackendBaseActionDelete
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('mailing_id', 'int');

		// does the item exist
		if(BackendMailmotorModel::existsMailing($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// fetch the mailing
			$mailing = BackendMailmotorModel::getMailing($this->id);

			// get all data for the user we want to edit
			$records = (array) BackendMailmotorCMHelper::getCM()->getCampaignBounces($mailing['cm_id']);

			// reset some data
			if(!empty($records))
			{
				// loop the records
				foreach($records as $record)
				{
					// only remove the hard bounces
					if($record['bounce_type'] == 'Hard')
					{
						// remove the address
						BackendMailmotorModel::deleteAddresses($record['email']);
					}
				}
			}

			// user was deleted, so redirect
			$this->redirect(BackendModel::createURLForAction('statistics') . '&id=' . $mailing['id'] . '&report=deleted-bounces');
		}

		// something went wrong
		else $this->redirect(BackendModel::createURLForAction('statistics') . '&error=no-bounces');
	}
}

?>