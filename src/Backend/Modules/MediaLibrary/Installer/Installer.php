<?php

namespace Backend\Modules\MediaLibrary\Installer;

use Backend\Modules\MediaLibrary\Domain\MediaFolder\Command\CreateMediaFolder;
use Symfony\Component\Filesystem\Filesystem;
use Backend\Core\Engine\Model;
use Backend\Core\Installer\ModuleInstaller;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Backend\Modules\MediaLibrary\Domain\MediaGroupMediaItem\MediaGroupMediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;

/**
 * Installer for the MediaLibrary module
 */
class Installer extends ModuleInstaller
{
    /**
     * Install the module
     */
    public function install()
    {
        $this->addModule('MediaLibrary');
        $this->importLocale(__DIR__ . '/Data/locale.xml');
        $this->createEntityTables();
        $this->configureModuleRights();
        $this->configureSettings();
        $this->configureBackendNavigation();
        $this->addGitIgnoreFile();
        $this->loadMediaFolders();
    }

    /**
     * Add git ignore file
     */
    protected function addGitIgnoreFile()
    {
        $fs = new Filesystem();
        $fs->dumpFile(
            FRONTEND_FILES_PATH . '/MediaLibrary/.gitignore',
            '# Uploaded files - Do not delete anything in this directory
            *'
        );
        $fs->dumpFile(
            FRONTEND_FILES_PATH . '/Cache/.gitignore',
            '*'
        );
    }

    /**
     * Configure backend navigation
     */
    protected function configureBackendNavigation()
    {
        // Navigation for "modules"
        $this->setNavigation(
            null,
            $this->getModule(),
            'media_library/media_item_index',
            [
                'media_library/media_item_upload',
                'media_library/media_item_edit',
            ],
            3
        );
    }

    /**
     * Configure module rights
     */
    protected function configureModuleRights()
    {
        // Set module rights
        $this->setModuleRights(1, $this->getModule());

        // MediaItem
        $this->setActionRights(1, $this->getModule(), 'MediaItemAddMovie'); // AJAX
        $this->setActionRights(1, $this->getModule(), 'MediaItemCleanup');
        $this->setActionRights(1, $this->getModule(), 'MediaItemDelete');
        $this->setActionRights(1, $this->getModule(), 'MediaItemEdit');
        $this->setActionRights(1, $this->getModule(), 'MediaItemFindAll'); // AJAX
        $this->setActionRights(1, $this->getModule(), 'MediaItemGetAllById'); // AJAX
        $this->setActionRights(1, $this->getModule(), 'MediaItemIndex');
        $this->setActionRights(1, $this->getModule(), 'MediaItemMassAction');
        $this->setActionRights(1, $this->getModule(), 'MediaItemUpload'); // Action and AJAX

        // MediaFolder
        $this->setActionRights(1, $this->getModule(), 'MediaFolderAdd'); // AJAX
        $this->setActionRights(1, $this->getModule(), 'MediaFolderDelete');
        $this->setActionRights(1, $this->getModule(), 'MediaFolderEdit'); // AJAX
        $this->setActionRights(1, $this->getModule(), 'MediaFolderFindAll'); // AJAX
        $this->setActionRights(1, $this->getModule(), 'MediaFolderGetCountsForGroup'); // AJAX
        $this->setActionRights(1, $this->getModule(), 'MediaFolderInfo'); // AJAX
        $this->setActionRights(1, $this->getModule(), 'MediaFolderMovie'); // AJAX
    }

    /**
     * Configure settings
     */
    protected function configureSettings()
    {
        $this->setSetting($this->getModule(), 'upload_number_of_sharding_folders', 15);
    }

    /**
     * Create entity tables
     */
    private function createEntityTables()
    {
        Model::get('fork.entity.create_schema')->forEntityClasses(
            [
                MediaFolder::class,
                MediaGroup::class,
                MediaGroupMediaItem::class,
                MediaItem::class,
            ]
        );
    }

    /**
     * Load Media Folders
     */
    protected function loadMediaFolders()
    {
        // Handle the create MediaFolder
        Model::get('command_bus')->handle(new CreateMediaFolder('default', 1));

        // Delete cache
        Model::get('media_library.cache.media_folder')->delete();
    }
}
