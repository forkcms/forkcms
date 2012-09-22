<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the settings-action, it will display a form to set general location settings
 *
 * @author Matthias Mullie <forkcms@mullie.eu>
 */
class BackendLocationSettings extends BackendBaseActionEdit
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->loadForm();
		$this->validateForm();
		$this->parse();
		$this->display();
	}

	/**
	 * Loads the settings form
	 */
	private function loadForm()
	{
		$this->frm = new BackendForm('settings');

		// add map info (widgets)
		$this->frm->addDropdown('zoom_level_widget', array_combine(array_merge(array('auto'), range(3, 18)), array_merge(array(BL::lbl('Auto', $this->getModule())), range(3, 18))), BackendModel::getModuleSetting($this->URL->getModule(), 'zoom_level_widget', 13));
		$this->frm->addText('width_widget', BackendModel::getModuleSetting($this->URL->getModule(), 'width_widget'));
		$this->frm->addText('height_widget', BackendModel::getModuleSetting($this->URL->getModule(), 'height_widget'));
		$this->frm->addDropdown('map_type_widget', array('ROADMAP' => BL::lbl('Roadmap', $this->getModule()), 'SATELLITE' => BL::lbl('Satellite', $this->getModule()), 'HYBRID' => BL::lbl('Hybrid', $this->getModule()), 'TERRAIN' => BL::lbl('Terrain', $this->getModule())), BackendModel::getModuleSetting($this->URL->getModule(), 'map_type_widget', 'roadmap'));
	}

	/**
	 * Parse the data
	 */
	protected function parse()
	{
		parent::parse();
		$this->tpl->assign('godUser', BackendAuthentication::getUser()->isGod());
	}

	/**
	 * Validates the settings form
	 */
	private function validateForm()
	{
		if($this->frm->isSubmitted())
		{
			$this->frm->cleanupFields();

			if($this->frm->isCorrect())
			{
				// set the base values
				$width = (int) $this->frm->getField('width_widget')->getValue();
				$height = (int) $this->frm->getField('height_widget')->getValue();

				if($width > 800) $width = 800;
				elseif($width < 300) $width = BackendModel::getModuleSetting('location', 'width_widget');
				if($height < 150) $height = BackendModel::getModuleSetting('location', 'height_widget');

				// set our settings (widgets)
				BackendModel::setModuleSetting($this->URL->getModule(), 'zoom_level_widget', (string) $this->frm->getField('zoom_level_widget')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'width_widget', $width);
				BackendModel::setModuleSetting($this->URL->getModule(), 'height_widget', $height);
				BackendModel::setModuleSetting($this->URL->getModule(), 'map_type_widget', (string) $this->frm->getField('map_type_widget')->getValue());

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_saved_settings');

				// redirect to the settings page
				$this->redirect(BackendModel::createURLForAction('settings') . '&report=saved');
			}
		}
	}
}
