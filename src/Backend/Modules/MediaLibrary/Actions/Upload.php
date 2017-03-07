<?php

namespace Backend\Modules\MediaLibrary\Actions;

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroupType;
use Backend\Modules\MediaLibrary\Domain\MediaItem\StorageType;

/**
 * This is the class to Upload MediaItem items
 */
class Upload extends BackendBaseActionAdd
{
    /**
     * The id of the folder where is filtered on
     *
     * @var int
     */
    protected $folderId;

    /**
     * Execute the action
     *
     * @return void
     */
    public function execute()
    {
        // Call parent, this will probably add some general CSS/JS or other required files
        parent::execute();
        $this->getData();
        $this->parse();
        $this->display();
    }

    /**
     * Get data
     */
    protected function getData()
    {
        // Define folder id
        $this->folderId = $this->getParameter('folder', 'int', 0);

        // We need to select a folder
        if ($this->folderId !== 0) {
            /** @var MediaFolder $mediaFolder */
            $mediaFolder = $this->get('media_library.repository.folder')->getOneById(
                $this->folderId
            );

            // MediaFolder not found
            if ($mediaFolder === null) {
                $this->redirect(
                    BackendModel::createURLForAction('Index')
                    . '&error=non-existing-media-folder'
                );
            }
        }
    }

    /**
     * Parse
     */
    protected function parse()
    {
        // Parse files necessary for the media upload helper
        MediaGroupType::parseFiles();

        // Parse allowed movie sources
        $this->tpl->assign('mediaAllowedMovieSources', StorageType::getPossibleMovieStorageTypeValues());

        // Assign folder
        if ($this->folderId) {
            $this->tpl->assign('folder', $this->folderId);
        }
    }
}
