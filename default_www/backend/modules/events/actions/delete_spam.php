<?php

/**
 * This action will delete a eventspost
 *
 * @package		backend
 * @subpackage	events
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.0
 */
class BackendEventsDeleteSpam extends BackendBaseActionDelete
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// delete item
		BackendEventsModel::deleteSpamComments($this->id);

		// item was deleted, so redirect
		$this->redirect(BackendModel::createURLForAction('comments') .'&report=deleted-spam#tabSpam');
	}
}

?>