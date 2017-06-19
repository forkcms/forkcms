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
        $this->configureEntities();
        $this->configureBackendNavigation();
        $this->configureBackendRights();
    }

    private function configureEntities(): void
    {
        Model::get('fork.entity.create_schema')->forEntityClass(MediaGallery::class);
    }

    protected function configureBackendNavigation(): void
    {
        // Set navigation for "Modules"
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

    protected function configureBackendRights(): void
    {
        $this->setModuleRights(1, $this->getModule());

        $this->setActionRights(1, $this->getModule(), 'MediaGalleryAdd');
        $this->setActionRights(1, $this->getModule(), 'MediaGalleryDelete');
        $this->setActionRights(1, $this->getModule(), 'MediaGalleryEdit');
        $this->setActionRights(1, $this->getModule(), 'MediaGalleryEditWidgetAction');
        $this->setActionRights(1, $this->getModule(), 'MediaGalleryIndex');
    }
}
