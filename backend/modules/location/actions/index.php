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
 * @author Matthias Mullie <matthias@mullie.eu>
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
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		// add js
		$this->header->addJS('http://maps.google.com/maps/api/js?sensor=false', null, null, true, false);

		$this->loadDataGrid();

		$this->loadSettingsForm();
		$this->validateSettingsForm();

		$this->parse();
		$this->display();
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
		$this->form = new BackendForm('settings');

		// add map info (overview map)
		$this->form->addDropdown('zoom_level', array_combine(array_merge(array('auto'), range(3, 18)), array_merge(array(BL::lbl('Auto', $this->getModule())), range(3, 18))), BackendModel::getModuleSetting($this->URL->getModule(), 'zoom_level', 'auto'));
		$this->form->addText('width', BackendModel::getModuleSetting($this->URL->getModule(), 'width'));
		$this->form->addText('height', BackendModel::getModuleSetting($this->URL->getModule(), 'height'));
		$this->form->addDropdown('map_type', array('ROADMAP' => BL::lbl('Roadmap', $this->getModule()), 'SATELLITE' => BL::lbl('Satellite', $this->getModule()), 'HYBRID' => BL::lbl('Hybrid', $this->getModule()), 'TERRAIN' => BL::lbl('Terrain', $this->getModule())), BackendModel::getModuleSetting($this->URL->getModule(), 'map_type', 'roadmap'));
	}

	/**
	 * Parse the datagrid
	 */
	protected function parse()
	{
		parent::parse();
		$this->form->parse($this->tpl);

		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);

		// get settings
		$settings = BackendModel::getModuleSettings();

		// assign to template
		$this->tpl->assign('items', BackendLocationModel::getAll());
		$this->tpl->assign('settings', $settings['location']);
	}

	/**
	 * Validate the settings form
	 */
	protected function validateSettingsForm()
	{
		if($this->form->isSubmitted())
		{
			$this->form->cleanupFields();

			if($this->form->isCorrect())
			{
				// set our settings (overview map)
				BackendModel::setModuleSetting($this->URL->getModule(), 'zoom_level', (string) $this->form->getField('zoom_level')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'width', (int) $this->form->getField('width')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'height', (int) $this->form->getField('height')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'map_type', (string) $this->form->getField('map_type')->getValue());
			}
		}
	}
}
