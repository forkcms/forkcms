<?php

/**
 * This is the classic fork mailmotor widget
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.0
 */
class BackendMailmotorWidgetStatistics extends BackendBaseWidget
{
	// max number of items to show per page
	const PAGING_LIMIT = 10;


	/**
	 * the default group ID
	 *
	 * @var	int
	 */
	private $groupId;


	/**
	 * Execute the widget
	 *
	 * @return	void
	 */
	public function execute()
	{
		// add css
		$this->header->addCSS('widgets.css', 'mailmotor');

		// set column
		$this->setColumn('right');

		// set position
		$this->setPosition(1);

		// fetch the default group ID
		$this->groupId = BackendMailmotorModel::getDefaultGroupID();

		// parse
		$this->parse();

		// display
		$this->display();
	}


	/**
	 * Load the datagrid for statistics
	 *
	 * @return	void
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
	 *
	 * @return	void
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

			// set edit link
			$dataGrid->setColumnURL('email', BackendModel::createURLForAction('edit_address', 'mailmotor') . '&amp;email=[email]');

			// set column functions
			$dataGrid->setColumnFunction(array('BackendDataGridFunctions', 'getTimeAgo'), array('[created_on]'), 'created_on', true);

			// parse the datagrid
			$this->tpl->assign('dgMailmotorSubscriptions', $dataGrid->getContent());
		}
	}


	/**
	 * Load the datagrid for unsubscriptions
	 *
	 * @return	void
	 */
	private function loadUnsubscriptions()
	{
		// get results
		$results = BackendMailmotorModel::getUnsubscribedAddressesByGroupID($this->groupId, self::PAGING_LIMIT);

		// there are some results
		if(!empty($results))
		{
			// get the datagrid
			$dataGrid = new BackendDataGridArray($results);

			// no pagination
			$dataGrid->setPaging(false);

			// set edit link
			$dataGrid->setColumnURL('email', BackendModel::createURLForAction('edit_address', 'mailmotor') . '&amp;email=[email]');

			// set column functions
			$dataGrid->setColumnFunction(array('BackendDataGridFunctions', 'getTimeAgo'), array('[created_on]'), 'created_on', true);

			// parse the datagrid
			$this->tpl->assign('dgMailmotorUnsubscriptions', $dataGrid->getContent());
		}
	}


	/**
	 * Parse stuff into the template
	 *
	 * @return	void
	 */
	private function parse()
	{
		$this->loadStatistics();
		$this->loadSubscriptions();
		$this->loadUnsubscriptions();
	}
}

?>