<?php

/**
 * This is the subscriptions-action , it will display the overview of events subscriptions
 *
 * @package		backend
 * @subpackage	subscriptions
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.0
 */
class BackendEventsSubscriptions extends BackendBaseActionIndex
{
	/**
	 * Datagrids
	 *
	 * @var	BackendDataGridDB
	 */
	private $dgPublished, $dgModeration, $dgSpam;


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load datagrids
		$this->loadDataGrids();

		// parse page
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Loads the datagrids
	 *
	 * @return	void
	 */
	private function loadDataGrids()
	{
		/*
		 * Datagrid for the published subscriptions.
		 */
		$this->dgPublished = new BackendDataGridDB(BackendEventsModel::QRY_DATAGRID_BROWSE_SUBSCRIPTIONS, array('published', BL::getWorkingLanguage()));

		// active tab
		$this->dgPublished->setActiveTab('tabPublished');

		// num items per page
		$this->dgPublished->setPagingLimit(30);

		// header labels
		$this->dgPublished->setHeaderLabels(array('created_on' => ucfirst(BL::lbl('Date'))));

		// add the multicheckbox column
		$this->dgPublished->setMassActionCheckboxes('checkbox', '[id]');

		// assign column functions
		$this->dgPublished->setColumnFunction(array('BackendDataGridFunctions', 'getTimeAgo'), '[created_on]', 'created_on', true);

		// sorting
		$this->dgPublished->setSortingColumns(array('created_on', 'author'), 'created_on');
		$this->dgPublished->setSortParameter('desc');

		// add column
		$this->dgPublished->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit_subscription') . '&amp;id=[id]', BL::lbl('Edit'));
		$this->dgPublished->addColumn('mark_as_spam', null, BL::lbl('MarkAsSpam'), BackendModel::createURLForAction('mass_subscription_action') . '&amp;id=[id]&amp;from=published&amp;action=spam', BL::lbl('MarkAsSpam'));

		// hide columns
		$this->dgPublished->setColumnsHidden('event_id', 'event_title', 'event_url');

		// add mass action dropdown
		$ddmMassAction = new SpoonFormDropdown('action', array('moderation' => BL::lbl('MoveToModeration'), 'spam' => BL::lbl('MoveToSpam'), 'delete' => BL::lbl('Delete')), 'spam');
		$ddmMassAction->setAttribute('id', 'actionPublished');
		$ddmMassAction->setOptionAttributes('delete', array('data-message-id' => 'confirmDelete'));
		$ddmMassAction->setOptionAttributes('spam', array('data-message-id' => 'confirmSpam'));
		$this->dgPublished->setMassAction($ddmMassAction);

		// datagrid for the subscriptions that are awaiting moderation
		$this->dgModeration = new BackendDataGridDB(BackendEventsModel::QRY_DATAGRID_BROWSE_SUBSCRIPTIONS, array('moderation', BL::getWorkingLanguage()));

		// active tab
		$this->dgModeration->setActiveTab('tabModeration');

		// num items per page
		$this->dgModeration->setPagingLimit(30);

		// header labels
		$this->dgModeration->setHeaderLabels(array('created_on' => ucfirst(BL::lbl('Date'))));

		// add the multicheckbox column
		$this->dgModeration->setMassActionCheckboxes('checkbox', '[id]');

		// assign column functions
		$this->dgModeration->setColumnFunction(array('BackendDataGridFunctions', 'getTimeAgo'), '[created_on]', 'created_on', true);

		// sorting
		$this->dgModeration->setSortingColumns(array('created_on', 'author'), 'created_on');
		$this->dgModeration->setSortParameter('desc');

		// add column
		$this->dgModeration->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit_subscription') . '&amp;id=[id]', BL::lbl('Edit'));
		$this->dgModeration->addColumn('approve', null, BL::lbl('Approve'), BackendModel::createURLForAction('mass_subscription_action') . '&amp;id=[id]&amp;from=published&amp;action=published', BL::lbl('Approve'));

		// hide columns
		$this->dgModeration->setColumnsHidden('event_id', 'event_title', 'event_url');

		// add mass action dropdown
		$ddmMassAction = new SpoonFormDropdown('action', array('published' => BL::lbl('MoveToPublished'), 'spam' => BL::lbl('MoveToSpam'), 'delete' => BL::lbl('Delete')), 'published');
		$ddmMassAction->setAttribute('id', 'actionModeration');
		$ddmMassAction->setOptionAttributes('delete', array('data-message-id' => 'confirmDelete'));
		$ddmMassAction->setOptionAttributes('spam', array('data-message-id' => 'confirmSpam'));
		$this->dgModeration->setMassAction($ddmMassAction);

		/*
		 * Datagrid for the subscriptions that are marked as spam
		 */
		$this->dgSpam = new BackendDataGridDB(BackendEventsModel::QRY_DATAGRID_BROWSE_SUBSCRIPTIONS, array('spam', BL::getWorkingLanguage()));

		// active tab
		$this->dgSpam->setActiveTab('tabSpam');

		// num items per page
		$this->dgSpam->setPagingLimit(30);

		// header labels
		$this->dgSpam->setHeaderLabels(array('created_on' => ucfirst(BL::lbl('Date'))));

		// add the multicheckbox column
		$this->dgSpam->setMassActionCheckboxes('checkbox', '[id]');

		// assign column functions
		$this->dgSpam->setColumnFunction(array('BackendDataGridFunctions', 'getTimeAgo'), '[created_on]', 'created_on', true);

		// sorting
		$this->dgSpam->setSortingColumns(array('created_on', 'author'), 'created_on');
		$this->dgSpam->setSortParameter('desc');

		// add column
		$this->dgSpam->addColumn('approve', null, BL::lbl('Approve'), BackendModel::createURLForAction('mass_subscription_action') . '&amp;id=[id]&amp;from=spam&amp;action=published', BL::lbl('Approve'));

		// hide columns
		$this->dgSpam->setColumnsHidden('event_id', 'event_title', 'event_url');

		// add mass action dropdown
		$ddmMassAction = new SpoonFormDropdown('action', array('published' => BL::lbl('MoveToPublished'), 'moderation' => BL::lbl('MoveToModeration'), 'delete' => BL::lbl('Delete')), 'published');
		$ddmMassAction->setAttribute('id', 'actionSpam');
		$ddmMassAction->setOptionAttributes('delete', array('data-message-id' => 'confirmDelete'));
		$ddmMassAction->setOptionAttributes('spam', array('data-message-id' => 'confirmSpam'));
		$this->dgSpam->setMassAction($ddmMassAction);
	}


	/**
	 * Parse & display the page
	 *
	 * @return	void
	 */
	private function parse()
	{
		// published datagrid and num results
		$this->tpl->assign('dgPublished', ($this->dgPublished->getNumResults() != 0) ? $this->dgPublished->getContent() : false);
		$this->tpl->assign('numPublished', $this->dgPublished->getNumResults());

		// moderaton datagrid and num results
		$this->tpl->assign('dgModeration', ($this->dgModeration->getNumResults() != 0) ? $this->dgModeration->getContent() : false);
		$this->tpl->assign('numModeration', $this->dgModeration->getNumResults());

		// spam datagrid and num results
		$this->tpl->assign('dgSpam', ($this->dgSpam->getNumResults() != 0) ? $this->dgSpam->getContent() : false);
		$this->tpl->assign('numSpam', $this->dgSpam->getNumResults());
	}
}

?>