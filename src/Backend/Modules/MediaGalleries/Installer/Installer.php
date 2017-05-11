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
    public function install(): void
    {
        $this->addModule('MediaGalleries');
        $this->importLocale(__DIR__ . '/Data/locale.xml');
        $this->createEntityTables();
        $this->configureModuleRights();
        $this->configureBackendNavigation();
    }

    protected function configureBackendNavigation(): void
    {
        // Navigation for "modules"
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $this->setNavigation(
            $navigationModulesId,
            $this->getModule(),
            'media_galleries/media_gallery_index',
            [
                'media_galleries/media_gallery_add',
                'media_galleries/media_gallery_edit',
            ]
        );
    }

    protected function configureModuleRights(): void
    {
        // Set module rights
        $this->setModuleRights(1, $this->getModule());

        // Media galleries
        $this->setActionRights(1, $this->getModule(), 'MediaGalleryIndex');
        $this->setActionRights(1, $this->getModule(), 'MediaGalleryAdd');
        $this->setActionRights(1, $this->getModule(), 'MediaGalleryDelete');
        $this->setActionRights(1, $this->getModule(), 'MediaGalleryEdit');
        $this->setActionRights(1, $this->getModule(), 'MediaGalleryEditWidgetAction');
    }

    private function createEntityTables(): void
    {
        Model::get('fork.entity.create_schema')->forEntityClasses(
            [
                MediaGallery::class,
            ]
        );
    }
}
