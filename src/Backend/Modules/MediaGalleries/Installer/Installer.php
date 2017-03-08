<?php

namespace Backend\Modules\MediaGalleries\Installer;

use Backend\Core\Engine\Model;
use Backend\Core\Installer\ModuleInstaller;
use Backend\Modules\MediaGalleries\Domain\MediaGallery\MediaGallery;

/**
 * Installer for the media module
 */
class Installer extends ModuleInstaller
{
    /**
     * Install the module
     */
    public function install()
    {
        // Add 'MediaGalleries' as a module
        $this->addModule('MediaGalleries');

        // Import locale
        $this->importLocale(dirname(__FILE__) . '/Data/locale.xml');

        // add the schema of the entity to the database
        Model::get('entity.create_schema')->forEntityClass(MediaGallery::class);

        // Inserting some other required stuff
        $this->insertRights();
        $this->insertNavigationForModules();
    }

    /**
     * Insert backend navigation for modules
     */
    protected function insertNavigationForModules()
    {
        // Navigation for "modules"
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $this->setNavigation(
            $navigationModulesId,
            'MediaGalleries',
            'media_galleries/index',
            array(
                'media_galleries/add',
                'media_galleries/edit',
            )
        );
    }

    /**
     * Insert rights
     */
    protected function insertRights()
    {
        // Set module rights
        $this->setModuleRights(1, 'MediaGalleries');

        // Media galleries
        $this->setActionRights(1, 'MediaGalleries', 'Index');
        $this->setActionRights(1, 'MediaGalleries', 'Add');
        $this->setActionRights(1, 'MediaGalleries', 'Delete');
        $this->setActionRights(1, 'MediaGalleries', 'Edit');
        $this->setActionRights(1, 'MediaGalleries', 'EditWidgetAction');
    }
}
