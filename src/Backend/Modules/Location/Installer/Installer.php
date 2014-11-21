<?php

namespace Backend\Modules\Location\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleInstaller;
use Backend\Modules\Location\Engine\Model as BackendLocationModel;

/**
 * Installer for the location module
 *
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Mathias Dewelde <mathias@dewelde.be>
 */
class Installer extends ModuleInstaller
{
    /**
     * Install the module
     */
    public function install()
    {
        $this->addEntitiesInDatabase(array(
            BackendLocationModel::LOCATION_ENTITY_CLASS,
        ));

        // add 'location' as a module
        $this->addModule('Location');

        // import locale
        $this->importLocale(dirname(__FILE__) . '/Data/locale.xml');

        // general settings
        $this->setSetting('Location', 'zoom_level', 'auto');
        $this->setSetting('Location', 'width', 400);
        $this->setSetting('Location', 'height', 300);
        $this->setSetting('Location', 'map_type', 'ROADMAP');

        $this->setSetting('Location', 'zoom_level_widget', 13);
        $this->setSetting('Location', 'width_widget', 400);
        $this->setSetting('Location', 'height_widget', 300);
        $this->setSetting('Location', 'map_type_widget', 'ROADMAP');

        $this->setRights();

        $this->insertExtra('Location', 'block', 'Location', null, 'a:1:{s:3:"url";s:34:"/private/location/index?token=true";}', 'N');

       // set navigation
       $this->setBackendNavigation();
    }

    private function setRights()
    {
        $this->setModuleRights(1, 'Location');

        $this->setActionRights(1, 'Location', 'Index');
        $this->setActionRights(1, 'Location', 'Add');
        $this->setActionRights(1, 'Location', 'Edit');
        $this->setActionRights(1, 'Location', 'Delete');
        $this->setActionRights(1, 'Location', 'SaveLiveLocation');
        $this->setActionRights(1, 'Location', 'UpdateMarker');
    }

    private function setBackendNavigation()
    {
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $this->setNavigation($navigationModulesId, 'Location', 'location/index', array('location/add', 'location/edit'));
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation($navigationModulesId, 'Location', 'location/settings');
    }
}
