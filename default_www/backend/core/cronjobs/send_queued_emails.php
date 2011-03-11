<?php

/**
 * This is the cronjob to send the queued emails.
 *
 * @package		backend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendCoreCronjobSendQueuedEmails extends BackendBaseCronjob
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// set busy file
		$this->setBusyFile();

		// get all queued mails
		$queuedMailIds = BackendMailer::getQueuedMailIds();

		// any mails to send?
		if(!empty($queuedMailIds))
		{
			// loop mails & send them
			foreach($queuedMailIds as $id) BackendMailer::send($id);
		}

		// remove busy file
		$this->clearBusyFile();
	}
}

?>