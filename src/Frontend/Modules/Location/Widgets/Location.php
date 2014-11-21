<?php

namespace Frontend\Modules\Location\Widgets;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Modules\Location\Engine\Model as FrontendLocationModel;

/**
 * This is the location-widget: 1 specific address
 *
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Mathias Dewelde <mathias@dewelde.be>
 */
class Location extends FrontendBaseWidget
{
    /**
     * @var Location
     */
    protected $item = array();

    /**
     * @var array
     */
    protected $settings = array();

    /**
     * Execute the extra
     */
    public function execute()
    {
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
        $this->settings = $this->item->getSettings();

        if (empty($this->settings)) {
            $settings = FrontendModel::getModuleSettings('Location');

            $this->settings['width'] = $settings['width_widget'];
            $this->settings['height'] = $settings['height_widget'];
            $this->settings['map_type'] = $settings['map_type_widget'];
            $this->settings['zoom_level'] = $settings['zoom_level_widget'];
            $this->settings['center']['lat'] = $this->item->getLat();
            $this->settings['center']['lng'] = $this->item->getLng();
        }

        // no center point given yet, use the first occurrence
        if (!isset($this->settings['center'])) {
            $this->settings['center']['lat'] = $this->item->getLat();
            $this->settings['center']['lng'] = $this->item->getLng();
        }

        $this->settings['maps_url'] = FrontendLocationModel::buildUrl($this->settings, array($this->item));
    }

    /**
     * Parse the data into the template
     */
    private function parse()
    {
        $this->addJS('http://maps.google.com/maps/api/js?sensor=true', true, false);

        $this->addJSData('settings_' . $this->item->getId(), $this->settings);
        $this->addJSData('items_' . $this->item->getId(), array($this->item));

        $this->tpl->assign('widgetLocationItem', $this->item);
        $this->tpl->assign('widgetLocationSettings', $this->settings);
    }
}
