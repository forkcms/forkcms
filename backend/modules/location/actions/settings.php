<?php

/**
 * This is the settings-action, it will display a form to set general location settings
 *
 * @package		backend
 * @subpackage	location
 *
 * @author		Matthias Mullie <matthias@mullie.eu>
 * @since		2.1
 */
class BackendLocationSettings extends BackendBaseActionEdit
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

		// load form
		$this->loadForm();

		// validates the form
		$this->validateForm();

		// parse
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Loads the settings form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// init settings form
		$this->frm = new BackendForm('settings');

		// add map info (overview map)
		$this->frm->addDropdown('zoom_level', array_combine(array_merge(array('auto'), range(3, 18)), array_merge(array(BL::lbl('Auto', $this->getModule())), range(3, 18))), BackendModel::getModuleSetting($this->URL->getModule(), 'zoom_level', 'auto'));
		$this->frm->addText('width', BackendModel::getModuleSetting($this->URL->getModule(), 'width'));
		$this->frm->addText('height', BackendModel::getModuleSetting($this->URL->getModule(), 'height'));
		$this->frm->addDropdown('map_type', array('ROADMAP' => BL::lbl('Roadmap', $this->getModule()), 'SATELLITE' => BL::lbl('Satellite', $this->getModule()), 'HYBRID' => BL::lbl('Hybrid', $this->getModule()), 'TERRAIN' => BL::lbl('Terrain', $this->getModule())), BackendModel::getModuleSetting($this->URL->getModule(), 'map_type', 'roadmap'));

		// add map info (widgets)
		$this->frm->addDropdown('zoom_level_widget', array_combine(array_merge(array('auto'), range(3, 18)), array_merge(array(BL::lbl('Auto', $this->getModule())), range(3, 18))), BackendModel::getModuleSetting($this->URL->getModule(), 'zoom_level_widget', 13));
		$this->frm->addText('width_widget', BackendModel::getModuleSetting($this->URL->getModule(), 'width_widget'));
		$this->frm->addText('height_widget', BackendModel::getModuleSetting($this->URL->getModule(), 'height_widget'));
		$this->frm->addDropdown('map_type_widget', array('ROADMAP' => BL::lbl('Roadmap', $this->getModule()), 'SATELLITE' => BL::lbl('Satellite', $this->getModule()), 'HYBRID' => BL::lbl('Hybrid', $this->getModule()), 'TERRAIN' => BL::lbl('Terrain', $this->getModule())), BackendModel::getModuleSetting($this->URL->getModule(), 'map_type_widget', 'roadmap'));
	}


	/**
	 * Validates the settings form
	 *
	 * @return	void
	 */
	private function validateForm()
	{
		// form is submitted
		if($this->frm->isSubmitted())
		{
			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// form is validated
			if($this->frm->isCorrect())
			{
				// set our settings (overview map)
				BackendModel::setModuleSetting($this->URL->getModule(), 'zoom_level', (string) $this->frm->getField('zoom_level')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'width', (int) $this->frm->getField('width')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'height', (int) $this->frm->getField('height')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'map_type', (string) $this->frm->getField('map_type')->getValue());

				// set our settings (widgets)
				BackendModel::setModuleSetting($this->URL->getModule(), 'zoom_level_widget', (string) $this->frm->getField('zoom_level_widget')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'width_widget', (int) $this->frm->getField('width_widget')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'height_widget', (int) $this->frm->getField('height_widget')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'map_type_widget', (string) $this->frm->getField('map_type_widget')->getValue());

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_saved_settings');

				// redirect to the settings page
				$this->redirect(BackendModel::createURLForAction('settings') . '&report=saved');
			}
		}
	}
}

?>