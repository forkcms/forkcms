<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the landing-pages-action, it will display the overview of analytics posts
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 */
class BackendAnalyticsLandingPages extends BackendAnalyticsBase
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->parse();
		$this->display();
	}

	/**
	 * Parse this page
	 */
	protected function parse()
	{
		parent::parse();

		$results = BackendAnalyticsModel::getLandingPages($this->startTimestamp, $this->endTimestamp);
		if(!empty($results))
		{
			$dataGrid = new BackendDataGridArray($results);
			$dataGrid->setColumnsHidden('start_date', 'end_date', 'updated_on', 'page_encoded');
			$dataGrid->setMassActionCheckboxes('checkbox', '[id]');

			// check if this action is allowed
			if(BackendAuthentication::isAllowedAction('detail_page', $this->getModule()))
			{
				$dataGrid->setColumnURL('page_path', BackendModel::createURLForAction('detail_page') . '&amp;page=[page_encoded]');
			}

			// set headers
			$dataGrid->setHeaderLabels(
				array('page_path' => SpoonFilter::ucfirst(BL::lbl('Page')))
			);

			// add mass action dropdown
			$ddmMassAction = new SpoonFormDropdown('action', array('delete_landing_page' => BL::lbl('Delete')), 'delete');
			$dataGrid->setMassAction($ddmMassAction);

			// parse the datagrid
			$this->tpl->assign('dgPages', $dataGrid->getContent());
		}
	}
}
