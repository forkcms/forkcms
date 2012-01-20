<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the classic fork mailmotor widget
 *
 * @author Dave Lens <dave.lens@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendMailmotorWidgetStatistics extends BackendBaseWidget
{
	const PAGING_LIMIT = 10;

	/**
	 * the default group ID
	 *
	 * @var	int
	 */
	private $groupId;

	/**
	 * Execute the widget
	 */
	public function execute()
	{
		$this->header->addCSS('widgets.css', 'mailmotor');
		$this->setColumn('right');
		$this->setPosition(1);
		$this->groupId = BackendMailmotorModel::getDefaultGroupID();
		$this->parse();
		$this->display();
	}

	/**
	 * Load the datagrid for statistics
	 */
	private function loadStatistics()
	{
		// fetch the latest mailing
		$mailing = BackendMailmotorModel::getSentMailings(1);

		// check if a mailing was found
		if(empty($mailing)) return false;

		// check if a mailing was set
		if(!isset($mailing[0])) return false;

		// show the sent mailings block
		$this->tpl->assign('oSentMailings', true);

		// require the helper class
		require_once BACKEND_MODULES_PATH . '/mailmotor/engine/helper.php';

		// fetch the statistics for this mailing
		$stats = BackendMailmotorCMHelper::getStatistics($mailing[0]['id'], true);

		// reformat the send date
		$mailing[0]['sent'] = SpoonDate::getDate('d-m-Y', $mailing[0]['sent']) . ' ' . BL::lbl('At') . ' ' . SpoonDate::getDate('H:i', $mailing);

		// get results
		$results[] = array('label' => BL::lbl('MailmotorLatestMailing'), 'value' => $mailing[0]['name']);
		$results[] = array('label' => BL::lbl('MailmotorSendDate'), 'value' => $mailing[0]['sent']);
		$results[] = array('label' => BL::lbl('MailmotorSent'), 'value' => $stats['recipients'] . ' (' . $stats['recipients_percentage'] . ')');
		$results[] = array('label' => BL::lbl('MailmotorOpened'), 'value' => $stats['unique_opens'] . ' (' . $stats['unique_opens_percentage'] . ')');
		$results[] = array('label' => BL::lbl('MailmotorClicks'), 'value' => $stats['clicks_total'] . ' (' . $stats['clicks_percentage'] . ')');

		// there are some results
		if(!empty($results))
		{
			// get the datagrid
			$dataGrid = new BackendDataGridArray($results);

			// no pagination
			$dataGrid->setPaging(false);

			// parse the datagrid
			$this->tpl->assign('dgMailmotorStatistics', $dataGrid->getContent());
		}
	}

	/**
	 * Load the datagrid for subscriptions
	 */
	private function loadSubscriptions()
	{
		// get results
		$results = BackendMailmotorModel::getAddressesByGroupID($this->groupId, false, self::PAGING_LIMIT);

		// there are some results
		if(!empty($results))
		{
			// get the datagrid
			$dataGrid = new BackendDataGridArray($results);

			// no pagination
			$dataGrid->setPaging(false);

			// set column functions
			$dataGrid->setColumnFunction(array('BackendDataGridFunctions', 'getTimeAgo'), array('[created_on]'), 'created_on', true);

			// check if this action is allowed
			if(BackendAuthentication::isAllowedAction('edit_address', 'mailmotor'))
			{
				// set edit link
				$dataGrid->setColumnURL('email', BackendModel::createURLForAction('edit_address', 'mailmotor') . '&amp;email=[email]');
			}

			// parse the datagrid
			$this->tpl->assign('dgMailmotorSubscriptions', $dataGrid->getContent());
		}
	}

	/**
	 * Load the datagrid for unsubscriptions
	 */
	private function loadUnsubscriptions()
	{
		// get results
		$results = BackendMailmotorModel::getUnsubscribedAddressesByGroupID($this->groupId, self::PAGING_LIMIT);

		// there are some results
		if(!empty($results))
		{
			$dataGrid = new BackendDataGridArray($results);
			$dataGrid->setPaging(false);
			$dataGrid->setColumnFunction(array('BackendDataGridFunctions', 'getTimeAgo'), array('[created_on]'), 'created_on', true);

			// check if this action is allowed
			if(BackendAuthentication::isAllowedAction('edit_address'))
			{
				$dataGrid->setColumnURL('email', BackendModel::createURLForAction('edit_address', 'mailmotor') . '&amp;email=[email]');
			}

			// parse the datagrid
			$this->tpl->assign('dgMailmotorUnsubscriptions', $dataGrid->getContent());
		}
	}

	/**
	 * Parse stuff into the template
	 */
	private function parse()
	{
		$this->loadStatistics();
		$this->loadSubscriptions();
		$this->loadUnsubscriptions();
	}
}
