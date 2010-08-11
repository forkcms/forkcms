<?php

/**
 * BackendAnalyticsWidgetTrafficSources
 *
 * @package		backend
 * @subpackage	analytics
 *
 * @author 		Annelies Van Extergem <annelies@netlash.com>
 * @since		2.0
 */
class BackendAnalyticsWidgetTrafficSources extends BackendBaseWidget
{
	/**
	 * Execute the widget
	 *
	 * @return	void
	 */
	public function execute()
	{
		// check analytics session token and analytics table id
		if(BackendModel::getModuleSetting('analytics', 'session_token', null) == '') return;
		if(BackendModel::getModuleSetting('analytics', 'table_id', null) == '') return;

		// settings are ok, set option
		$this->tpl->assign('analyticsValidSettings', true);

		// set column
		$this->setColumn('left');

		// add css	@todo @tijs: header should be available in BackendBaseWidget
		$header = Spoon::getObjectReference('header');
		$header->addCSS('widgets.css', 'analytics');

		// add highchart javascript
		$header->addJavascript('dashboard.js', 'analytics');

		// parse
		$this->parse();

		// display
		$this->display();
	}


	/**
	 * Parse into template
	 *
	 * @return	void
	 */
	private function parse()
	{
		// parse redirect link
		$this->tpl->assign('settingsUrl', BackendModel::createURLForAction('settings', 'analytics'));

		// parse keywords
		$this->parseKeywords();

		// parse referrers
		$this->parseReferrers();
	}


	/**
	 * Parse the keywords datagrid
	 *
	 * @return	void
	 */
	private function parseKeywords()
	{
		// get results
		$results = BackendAnalyticsModel::getRecentKeywords();

		// there are some results
		if(!empty($results))
		{
			// get the datagrid
			$datagrid = new BackendDataGridArray($results);

			// no pagination
			$datagrid->setPaging(false);

			// hide columns
			$datagrid->setColumnsHidden('id', 'date');

			// parse the datagrid
			$this->tpl->assign('dgAnalyticsKeywords', $datagrid->getContent());
		}

		// get date
		$date = (isset($results[0]['date']) ? substr($results[0]['date'], 0, 10) : date('Y-m-d'));
		$timestamp = mktime(0, 0, 0, substr($date, 5, 2), substr($date, 8, 2), substr($date, 0, 4));

		// assign date label
		$this->tpl->assign('analyticsTrafficSourcesDate', ($date != date('Y-m-d') ? BackendModel::getUTCDate('d/m/Y', $timestamp) : BL::getLabel('Today')));
	}


	/**
	 * Parse the referrers datagrid
	 *
	 * @return	void
	 */
	private function parseReferrers()
	{
		// get results
		$results = BackendAnalyticsModel::getRecentReferrers();

		// there are some results
		if(!empty($results))
		{
			// get the datagrid
			$datagrid = new BackendDataGridArray($results);

			// no pagination
			$datagrid->setPaging(false);

			// hide columns
			$datagrid->setColumnsHidden('id', 'date');

			// set url
			$datagrid->setColumnURL('referrer', 'http://[referrer]');

			// parse the datagrid
			$this->tpl->assign('dgAnalyticsReferrers', $datagrid->getContent());
		}
	}
}

?>