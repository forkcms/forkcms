<?php

namespace Backend\Modules\MediaLibrary\Installer;

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
        $this->importLocale(dirname(__FILE__) . '/Data/locale.xml');
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

        // Save Dropdowns
        $fs->dumpFile(
            FRONTEND_FILES_PATH . '/MediaLibrary/.gitignore',
            '# Uploaded files - Do not delete anything in this directory
            /Source/*
            
            # Generated images needed for backend - Do not delete anything in this directory
            /Backend/*
            
            # Automagically generated images needed for frontend - All directories and images in this directory can safely be removed.
            /Frontend/*'
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
            'MediaLibrary',
            'media_library/media_item_index',
            array(
                'media_library/media_item_upload',
                'media_library/media_item_edit',
            )
        );
    }

    /**
     * Configure module rights
     */
    protected function configureModuleRights()
    {
        // Set module rights
        $this->setModuleRights(1, 'MediaLibrary');

        // Media index
        $this->setActionRights(1, 'MediaLibrary', 'MediaItemIndex');
        $this->setActionRights(1, 'MediaLibrary', 'MediaItemMassAction');
        $this->setActionRights(1, 'MediaLibrary', 'MediaItemUpload'); // Action and AJAX
        $this->setActionRights(1, 'MediaLibrary', 'MediaItemDelete');
        $this->setActionRights(1, 'MediaLibrary', 'MediaItemEdit');
        $this->setActionRights(1, 'MediaLibrary', 'MediaItemFindAll'); // AJAX
        $this->setActionRights(1, 'MediaLibrary', 'MediaItemGetAllById'); // AJAX
        $this->setActionRights(1, 'MediaLibrary', 'MediaItemAddMovie'); // AJAX

        // MediaFolder
        $this->setActionRights(1, 'MediaLibrary', 'MediaFolderAdd'); // AJAX
        $this->setActionRights(1, 'MediaLibrary', 'MediaFolderDelete');
        $this->setActionRights(1, 'MediaLibrary', 'MediaFolderEdit'); // AJAX
        $this->setActionRights(1, 'MediaLibrary', 'MediaFolderGetCountsForGroup'); // AJAX
        $this->setActionRights(1, 'MediaLibrary', 'MediaFolderInfo'); // AJAX
        $this->setActionRights(1, 'MediaLibrary', 'MediaFolderFindAll'); // AJAX
        $this->setActionRights(1, 'MediaLibrary', 'MovieMediaFolder'); // AJAX
    }

    /**
     * Configure settings
     */
    protected function configureSettings()
    {
        $this->setSetting('MediaLibrary', 'backend_thumbnail_height', 90);
        $this->setSetting('MediaLibrary', 'backend_thumbnail_width', 140);
        $this->setSetting('MediaLibrary', 'backend_thumbnail_quality', 95);
        $this->setSetting('MediaLibrary', 'upload_number_of_sharding_folders', 15);
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
        Model::get('database')->insert(
            'MediaFolder',
            array(
                'userId' => 1,
                'name' => 'default',
                'createdOn' => new \DateTime(),
                'editedOn' => new \DateTime(),
                'parentMediaFolderId' => null,
            )
        );

        // Delete cache
        Model::get('media_library.cache_builder')->deleteCache();
    }
}
