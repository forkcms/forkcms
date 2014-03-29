<?php

namespace Frontend\Modules\Location\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Location\Engine\Model as FrontendLocationModel;

/**
 * This is the index-action, it has an overview of locations.
 *
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Index extends FrontendBaseBlock
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
        $this->items = FrontendLocationModel::getAll();
        $this->settings = FrontendLocationModel::getMapSettings(0);
        $firstMarker = current($this->items);
        if (empty($this->settings)) {
            $this->settings = FrontendModel::getModuleSettings('Location');
            $this->settings['center']['lat'] = $firstMarker['lat'];
            $this->settings['center']['lng'] = $firstMarker['lng'];
        }

        // no center point given yet, use the first occurrence
        if (!isset($this->settings['center'])) {
            $this->settings['center']['lat'] = $firstMarker['lat'];
            $this->settings['center']['lng'] = $firstMarker['lng'];
        }
    }

    /**
     * Parse the data into the template
     */
    private function parse()
    {
        $this->addJSData('settings', $this->settings);
        $this->addJSData('items', $this->items);

        $this->tpl->assign('locationItems', $this->items);
        $this->tpl->assign('locationSettings', $this->settings);
    }
}
