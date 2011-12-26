<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This action will delete a subscription spam
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendEventsDeleteSubscriptionSpam extends BackendBaseActionDelete
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		BackendEventsModel::deleteSpamSubscriptions();

		// item was deleted, so redirect
		$this->redirect(BackendModel::createURLForAction('subscriptions') . '&report=deleted-spam#tabSpam');
	}
}
