<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the index-action (default), it will display the overview of location items
 *
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 */
class BackendLocationIndex extends BackendBaseActionIndex
{
	/**
	 * The settings form
	 *
	 * @var BackendForm
	 */
	protected $form;

	/**
	 * @var array
	 */
	protected $items = array(), $settings = array();

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		// add js
		$this->header->addJS('http://maps.google.com/maps/api/js?sensor=false', null, false, true, false);

		$this->loadData();

		$this->loadDataGrid();
		$this->loadSettingsForm();

		$this->parse();
		$this->display();
	}

	/**
	 * Load the settings
	 */
	protected function loadData()
	{
		$this->items = BackendLocationModel::getAll();
		$this->settings = BackendLocationModel::getMapSettings(0);
		$firstMarker = current($this->items);

		// if there are no markers we reset it to the birthplace of Fork
		if($firstMarker === false) $firstMarker = array('lat' => '51.052146', 'lng' => '3.720491');

		// load the settings from the general settings
		if(empty($this->settings))
		{
			$settings = BackendModel::getModuleSettings();
			$this->settings = $settings['location'];

			$this->settings['center']['lat'] = $firstMarker['lat'];
			$this->settings['center']['lng'] = $firstMarker['lng'];
		}

		// no center point given yet, use the first occurrence
		if(!isset($this->settings['center']))
		{
			$this->settings['center']['lat'] = $firstMarker['lat'];
			$this->settings['center']['lng'] = $firstMarker['lng'];
		}
	}

	/**
	 * Loads the datagrid
	 */
	private function loadDataGrid()
	{
		$this->dataGrid = new BackendDataGridDB(BackendLocationModel::QRY_DATAGRID_BROWSE, array(BL::getWorkingLanguage()));
		$this->dataGrid->setSortingColumns(array('address', 'title'), 'address');
		$this->dataGrid->setSortParameter('ASC');

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('edit'))
		{
			$this->dataGrid->setColumnURL('title', BackendModel::createURLForAction('edit') . '&amp;id=[id]');
			$this->dataGrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]', BL::lbl('Edit'));
		}
	}

	/**
	 * Load the settings form
	 */
	protected function loadSettingsForm()
	{
		$mapTypes = array(
			'ROADMAP' => BL::lbl('Roadmap', $this->getModule()),
			'SATELLITE' => BL::lbl('Satellite', $this->getModule()),
			'HYBRID' => BL::lbl('Hybrid', $this->getModule()),
			'TERRAIN' => BL::lbl('Terrain', $this->getModule())
		);

		$zoomLevels = array_combine(
			array_merge(array('auto'), range(3, 18)),
			array_merge(array(BL::lbl('Auto', $this->getModule())), range(3, 18))
		);

		$this->form = new BackendForm('settings');

		// add map info (overview map)
		$this->form->addHidden('map_id', 0);
		$this->form->addDropdown('zoom_level', $zoomLevels, $this->settings['zoom_level']);
		$this->form->addText('width', $this->settings['width']);
		$this->form->addText('height', $this->settings['height']);
		$this->form->addDropdown('map_type', $mapTypes, $this->settings['map_type']);
	}

	/**
	 * Parse the datagrid
	 */
	protected function parse()
	{
		parent::parse();

		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);
		$this->tpl->assign('godUser', BackendAuthentication::getUser()->isGod());

		// assign to template
		$this->tpl->assign('items', $this->items);
		$this->tpl->assign('settings', $this->settings);
		$this->form->parse($this->tpl);
	}
}
