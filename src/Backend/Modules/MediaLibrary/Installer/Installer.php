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
        // Add the schema of the entity to the database
        Model::get('entity.create_schema')->forEntityClass(MediaFolder::class);
        Model::get('entity.create_schema')->forEntityClass(MediaGroup::class);
        Model::get('entity.create_schema')->forEntityClass(MediaGroupMediaItem::class);
        Model::get('entity.create_schema')->forEntityClass(MediaItem::class);

        // Add 'MediaLibrary' as a module
        $this->addModule('MediaLibrary');

        // Import locale
        $this->importLocale(dirname(__FILE__) . '/Data/locale.xml');

        // Inserting some other required stuff
        $this->insertRights();
        $this->insertSettings();
        $this->insertNavigationForModules();

        // Add a git ignore file
        $this->addGitIgnoreFile();

        // Load folders
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
'# Generated files
/Backend/*
/Frontend/*
/Source/*'
        );
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
            'MediaLibrary',
            'media_library/index',
            array(
                'media_library/upload',
                'media_library/edit_media_item',
            )
        );
    }

    /**
     * Insert rights
     */
    protected function insertRights()
    {
        // Set module rights
        $this->setModuleRights(1, 'MediaLibrary');

        // Media index
        $this->setActionRights(1, 'MediaLibrary', 'Index');
        $this->setActionRights(1, 'MediaLibrary', 'MassAction');

        // MediaItem
        $this->setActionRights(1, 'MediaLibrary', 'Upload');
        $this->setActionRights(1, 'MediaLibrary', 'UploadMediaItem'); // AJAX
        $this->setActionRights(1, 'MediaLibrary', 'DeleteMediaItem');
        $this->setActionRights(1, 'MediaLibrary', 'EditMediaItem');
        $this->setActionRights(1, 'MediaLibrary', 'GetMediaItems'); // AJAX
        $this->setActionRights(1, 'MediaLibrary', 'InsertMediaItemMovie'); // AJAX

        // MediaFolder
        $this->setActionRights(1, 'MediaLibrary', 'AddMediaFolder'); // AJAX
        $this->setActionRights(1, 'MediaLibrary', 'DeleteMediaFolder');
        $this->setActionRights(1, 'MediaLibrary', 'EditMediaFolder'); // AJAX
        $this->setActionRights(1, 'MediaLibrary', 'GetMediaFolderCountsForGroup'); // AJAX
        $this->setActionRights(1, 'MediaLibrary', 'GetMediaFolderInfo'); // AJAX
        $this->setActionRights(1, 'MediaLibrary', 'GetMediaFolders'); // AJAX
        $this->setActionRights(1, 'MediaLibrary', 'MovieMediaFolder'); // AJAX
    }

    /**
     * Insert settings
     */
    protected function insertSettings()
    {
        $this->setSetting('MediaLibrary', 'upload_auto_increment', 0);
        $this->setSetting('MediaLibrary', 'upload_number_of_sharding_folders', 15);
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
