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
        $this->addModule('MediaGalleries');
        $this->importLocale(__DIR__ . '/Data/locale.xml');
        $this->createEntityTables();
        $this->configureModuleRights();
        $this->configureBackendNavigation();
    }

    /**
     * Configure backend navigation
     */
    protected function configureBackendNavigation()
    {
        // Navigation for "modules"
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $this->setNavigation(
            $navigationModulesId,
            'MediaGalleries',
            'media_galleries/media_gallery_index',
            [
                'media_galleries/media_gallery_add',
                'media_galleries/media_gallery_edit',
            ]
        );
    }

    /**
     * Configure module rights
     */
    protected function configureModuleRights()
    {
        // Set module rights
        $this->setModuleRights(1, 'MediaGalleries');

        // Media galleries
        $this->setActionRights(1, 'MediaGalleries', 'MediaGalleryIndex');
        $this->setActionRights(1, 'MediaGalleries', 'MediaGalleryAdd');
        $this->setActionRights(1, 'MediaGalleries', 'MediaGalleryDelete');
        $this->setActionRights(1, 'MediaGalleries', 'MediaGalleryEdit');
        $this->setActionRights(1, 'MediaGalleries', 'MediaGalleryEditWidgetAction');
    }

    /**
     * Create entity tables
     */
    private function createEntityTables()
    {
        Model::get('fork.entity.create_schema')->forEntityClasses(
            [
                MediaGallery::class,
            ]
        );
    }
}
