<?php

namespace Frontend\Modules\Location\Widgets;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Modules\Location\Engine\Model as FrontendLocationModel;

/**
 * This is the location-widget: 1 specific address
 */
class Location extends FrontendBaseWidget
{
    /**
     * @var array
     */
    protected $items = array();

    /**
     * @var array
     */
    protected $settings = array();

    /**
     * @var array
     */
    private $item;

    /**
     * Execute the extra
     */
    public function execute()
    {
        // define Google Maps API key
        $apikey = $this->get('fork.settings')->get('Core', 'google_maps_key');

        // check Google Maps API key, otherwise show error
        if ($apikey == null) {
            trigger_error('Please provide a Google Maps API key.');
        }
        $this->addJS('https://maps.googleapis.com/maps/api/js?key=' . $apikey, true, false);

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
        if (empty($this->settings)) {
            $settings = $this->get('fork.settings')->getForModule('Location');

            $this->settings['width'] = $settings['width_widget'];
            $this->settings['height'] = $settings['height_widget'];
            $this->settings['map_type'] = $settings['map_type_widget'];
            $this->settings['zoom_level'] = $settings['zoom_level_widget'];
            $this->settings['center']['lat'] = $this->item['lat'];
            $this->settings['center']['lng'] = $this->item['lng'];
        }

        // no center point given yet, use the first occurrence
        if (!isset($this->settings['center'])) {
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
