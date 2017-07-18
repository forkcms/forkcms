<?php

namespace Frontend\Modules\Location\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Modules\Location\Engine\Model as FrontendLocationModel;

/**
 * This is the index-action, it has an overview of locations.
 */
class Index extends FrontendBaseBlock
{
    /**
     * @var array
     */
    protected $items = [];

    /**
     * @var array
     */
    protected $settings = [];

    public function execute(): void
    {
        // define Google Maps API key
        $apikey = $this->get('fork.settings')->get('Core', 'google_maps_key');

        // check Google Maps API key, otherwise show error
        if ($apikey == null) {
            trigger_error('Please provide a Google Maps API key.');
        }
        $this->addJS('https://maps.googleapis.com/maps/api/js?key=' . $apikey, true, false);
        $this->addJS(FrontendLocationModel::getPathToMapStyles(false), true);

        parent::execute();

        $this->loadTemplate();
        $this->loadData();

        $this->parse();
    }

    protected function loadData(): void
    {
        $this->items = FrontendLocationModel::getAll();
        $this->settings = FrontendLocationModel::getMapSettings(0);
        $firstMarker = current($this->items);
        if (empty($this->settings)) {
            $this->settings = $this->get('fork.settings')->getForModule('Location');
            $this->settings['center']['lat'] = $firstMarker['lat'];
            $this->settings['center']['lng'] = $firstMarker['lng'];
        }

        // no center point given yet, use the first occurrence
        if (!isset($this->settings['center'])) {
            $this->settings['center']['lat'] = $firstMarker['lat'];
            $this->settings['center']['lng'] = $firstMarker['lng'];
        }
    }

    private function parse(): void
    {
        $this->addJSData('settings', $this->settings);
        $this->addJSData('items', $this->items);

        $this->template->assign('locationItems', $this->items);
        $this->template->assign('locationSettings', $this->settings);
    }
}
