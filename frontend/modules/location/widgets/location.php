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
 * @author Matthias Mullie <matthias@mullie.eu>
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
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
		parent::execute();

		// add the css file
		$this->addCSS('location.css');

		$this->loadTemplate();
		$this->loadData();

		$this->parse();
	}

	/**
	 * Load the data
	 */
	protected function loadData()
	{
		$this->items = FrontendLocationModel::get($this->data['id']);
		$this->settings = FrontendLocationModel::getMapSettings($this->data['id']);
		if(empty($this->settings))
		{
			$settings = FrontendModel::getModuleSettings('location');

			$this->settings['width'] = $settings['width_widget'];
			$this->settings['height'] = $settings['height_widget'];
			$this->settings['map_type'] = $settings['map_type_widget'];
			$this->settings['zoom_level'] = $settings['zoom_level_widget'];
			$this->settings['center']['lat'] = $this->items['lat'];
			$this->settings['center']['lng'] = $this->items['lng'];
		}

		// no center point given yet, use the first occurance
		if(!isset($this->settings['center']))
		{
			$this->settings['center']['lat'] = $this->items['lat'];
			$this->settings['center']['lng'] = $this->items['lng'];
		}

		$this->settings['maps_url'] = FrontendLocationModel::buildUrl($this->settings, array($this->items));
	}

	/**
	 * Parse the data into the template
	 */
	private function parse()
	{
		// show message
		$this->tpl->assign('widgetLocationItem', $this->items);

		// hide form
		$this->tpl->assign('widgetLocationSettings', $this->settings);
	}
}
