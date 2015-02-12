<?php

namespace Backend\Modules\Location\Ajax;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Location\Engine\Model as BackendLocationModel;

/**
 * This is an ajax handler that will set a new position for a certain map
 *
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 * @author Mathias Dewelde <mathias@dewelde.be>
 */
class SaveLiveLocation extends BackendBaseAJAXAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        $generalSettings = BackendModel::getModuleSettings('Location');

        // get parameters
        $locationId = \SpoonFilter::getPostValue('id', null, null, 'int');
        $zoomLevel = trim(\SpoonFilter::getPostValue('zoom', null, 'auto'));
        $mapType = strtoupper(trim(\SpoonFilter::getPostValue('type', array('roadmap', 'satelitte', 'hybrid', 'terrain'), 'roadmap')));
        $centerLat = \SpoonFilter::getPostValue('centerLat', null, 1, 'float');
        $centerLng = \SpoonFilter::getPostValue('centerLng', null, 1, 'float');
        $height = \SpoonFilter::getPostValue('height', null, $generalSettings['height'], 'int');
        $width = \SpoonFilter::getPostValue('width', null, $generalSettings['width'], 'int');
        $showLink = \SpoonFilter::getPostValue('link', array('true', 'false'), 'false', 'string');
        $showDirections = \SpoonFilter::getPostValue('directions', array('true', 'false'), 'false', 'string');
        $showOverview = \SpoonFilter::getPostValue('showOverview', array('true', 'false'), 'true', 'string');

        // reformat
        $center = array('lat' => $centerLat, 'lng' => $centerLng);
        $showLink = ($showLink == 'true');
        $showDirections = ($showDirections == 'true');
        $showOverview = ($showOverview == 'true');

        // standard dimensions
        if ($width > 800) {
            $width = 800;
        }
        if ($width < 300) {
            $width = $generalSettings['width'];
        }
        if ($height < 150) {
            $height = $generalSettings['height'];
        }

        if ($locationId == 0) {
            BackendModel::setModuleSetting($this->module, 'zoom_level', (string) $zoomLevel);
            BackendModel::setModuleSetting($this->module, 'map_type', (string) $mapType);
            BackendModel::setModuleSetting($this->module, 'height', (int) $height);
            BackendModel::setModuleSetting($this->module, 'width', (int) $width);

            $this->output(self::OK, null, BL::msg('Success'));
        } else {
            // get the location
            $location = BackendLocationModel::get($locationId);

            // does the location exists
            if ($location === null) {
                $this->output(self::BAD_REQUEST, null, 'location not found');
            } else {
                // update map settings
                $location
                    ->setShowOverview($showOverview)
                    ->addSetting('zoom_level', (string) $zoomLevel)
                    ->addSetting('map_type', (string) $mapType)
                    ->addSetting('center', (array) $center)
                    ->addSetting('height', (int) $height)
                    ->addSetting('width', (int) $width)
                    ->addSetting('directions', $showDirections)
                    ->addSetting('full_url', $showLink)
                ;

                BackendLocationModel::update($location);

                $this->output(self::OK, null, BL::msg('Success'));
            }
        }
    }
}
