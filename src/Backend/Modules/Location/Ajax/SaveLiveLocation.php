<?php

namespace Backend\Modules\Location\Ajax;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Language\Language as BL;
use Backend\Modules\Location\Engine\Model as BackendLocationModel;
use Symfony\Component\HttpFoundation\Response;

/**
 * This is an ajax handler that will set a new position for a certain map
 */
class SaveLiveLocation extends BackendBaseAJAXAction
{
    public function execute(): void
    {
        parent::execute();

        $generalSettings = $this->get('fork.settings')->getForModule('Location');

        // get parameters
        $itemId = $this->getRequest()->request->getInt('id');
        $zoomLevel = trim($this->getRequest()->request->get('zoom', 'auto'));
        $mapType = strtoupper(trim($this->getRequest()->request->get('type')));
        if (in_array($mapType, ['roadmap', 'satellite', 'hybrid', 'terrain', 'street_view'])) {
            $mapType = 'roadmap';
        }
        $mapStyle = trim($this->getRequest()->request->get('style'));
        if (!in_array($mapStyle, ['standard', 'custom', 'gray', 'blue'])) {
            $mapStyle = 'standard';
        }
        $centerLat = (float) $this->getRequest()->request->get('centerLat', 1);
        $centerLng = (float) $this->getRequest()->request->get('centerLng', 1);
        $height = $this->getRequest()->request->getInt('height', $generalSettings['height']);
        $width = $this->getRequest()->request->getInt('width', $generalSettings['width']);
        $showLink = $this->getRequest()->request->get('link');
        if (!in_array($showLink, ['true', 'false'])) {
            $showLink = 'false';
        }
        $showDirections = $this->getRequest()->request->get('directions');
        if (!in_array($showDirections, ['true', 'false'])) {
            $showDirections = 'false';
        }
        $showOverview = $this->getRequest()->request->get('showOverview');
        if (!in_array($showOverview, ['true', 'false'])) {
            $showOverview = 'true';
        }

        // reformat
        $center = ['lat' => $centerLat, 'lng' => $centerLng];
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

        // no id given, this means we should update the main map
        BackendLocationModel::setMapSetting($itemId, 'zoom_level', (string) $zoomLevel);
        BackendLocationModel::setMapSetting($itemId, 'map_type', (string) $mapType);
        BackendLocationModel::setMapSetting($itemId, 'map_style', (string) $mapStyle);
        BackendLocationModel::setMapSetting($itemId, 'center', (array) $center);
        BackendLocationModel::setMapSetting($itemId, 'height', (int) $height);
        BackendLocationModel::setMapSetting($itemId, 'width', (int) $width);
        BackendLocationModel::setMapSetting($itemId, 'directions', $showDirections);
        BackendLocationModel::setMapSetting($itemId, 'full_url', $showLink);

        $item = [
            'id' => $itemId,
            'language' => BL::getWorkingLanguage(),
            'show_overview' => $showOverview,
        ];
        BackendLocationModel::update($item);

        // output
        $this->output(Response::HTTP_OK, null, BL::msg('Success'));
    }
}
