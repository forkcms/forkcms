<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the location-widget: 1 specific address
 *
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class FrontendLocationWidgetLocation extends FrontendBaseWidget
{
	/**
	 * @var array
	 */
	protected $items = array(), $settings = array();

	/**
	 * Execute the extra
	 */
	public function execute()
	{
		$this->addJS('http://maps.google.com/maps/api/js?sensor=true', true, false);

		parent::execute();

		$this->loadTemplate();
		$this->loadData();

		$this->parse();
	}

	/**
	 * Load the data
	 */
	protected function loadData()
	{
		$this->item = FrontendLocationModel::get($this->data['id']);
		$this->settings = FrontendLocationModel::getMapSettings($this->data['id']);
		if(empty($this->settings))
		{
			$settings = FrontendModel::getModuleSettings('location');

			$this->settings['width'] = $settings['width_widget'];
			$this->settings['height'] = $settings['height_widget'];
			$this->settings['map_type'] = $settings['map_type_widget'];
			$this->settings['zoom_level'] = $settings['zoom_level_widget'];
			$this->settings['center']['lat'] = $this->item['lat'];
			$this->settings['center']['lng'] = $this->item['lng'];
		}

		// no center point given yet, use the first occurence
		if(!isset($this->settings['center']))
		{
			$this->settings['center']['lat'] = $this->item['lat'];
			$this->settings['center']['lng'] = $this->item['lng'];
		}

		$this->settings['maps_url'] = FrontendLocationModel::buildUrl($this->settings, array($this->item));
	}

	/**
	 * Parse the data into the template
	 */
	private function parse()
	{

		$this->addJSData('settings_' . $this->item['id'], $this->settings);
		$this->addJSData('items_' . $this->item['id'], array($this->item));

		$this->tpl->assign('widgetLocationItem', $this->item);
		$this->tpl->assign('widgetLocationSettings', $this->settings);
	}
}
