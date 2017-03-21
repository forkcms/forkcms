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
class MediaItemUpload extends BackendBaseActionAdd
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

        // Parse JS files
        $this->parseFiles();
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
                    BackendModel::createURLForAction('MediaItemIndex')
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
        $this->tpl->assign('folderId', $this->folderId);
        $this->tpl->assign('tree', $this->get('media_library.manager.tree')->getHTML());
        $this->header->addJsData('MediaLibrary', 'openedFolderId', ($this->folderId !== null) ? $this->folderId : null);
    }

    /**
     * Parse JS files
     */
    private function parseFiles()
    {
        $this->header->addJS('/src/Backend/Modules/Pages/Js/jstree/jquery.tree.js', null, false, true);
        $this->header->addJS('/src/Backend/Modules/Pages/Js/jstree/lib/jquery.cookie.js', null, false, true);
        $this->header->addJS('/src/Backend/Modules/Pages/Js/jstree/plugins/jquery.tree.cookie.js', null, false, true);
    }
}
