<?php

namespace Backend\Modules\MediaLibrary\Actions;

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroupType;
use Backend\Modules\MediaLibrary\Domain\MediaGroupMediaItem\MediaGroupMediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\StorageType;

class MediaItemUpload extends BackendBaseActionAdd
{
    /** @var MediaFolder */
    protected $mediaFolder;

    public function execute()
    {
        parent::execute();

        /** @var MediaFolder|null $mediaFolder */
        $this->mediaFolder = $this->getMediaFolder();

        // Parse JS files
        $this->parseFiles();
        $this->parse();
        $this->display();
    }

    /**
     * @return MediaFolder|null
     */
    protected function getMediaFolder()
    {
        /** @var int $id */
        $id = $this->get('request')->query->get('folder');

        try {
            return $this->get('media_library.repository.folder')->findOneById($id);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Parse
     */
    protected function parse()
    {
        // Parse files necessary for the media upload helper
        MediaGroupType::parseFiles();

        /** @var int|null $mediaFolderId */
        $mediaFolderId = ($this->mediaFolder instanceof MediaFolder) ? $this->mediaFolder->getId() : null;

        $this->tpl->assign('folderId', $mediaFolderId);
        $this->tpl->assign('tree', $this->get('media_library.manager.tree')->getHTML());
        $this->header->addJsData('MediaLibrary', 'openedFolderId', $mediaFolderId);
    }

    /**
     * Parse JS files
     */
    private function parseFiles()
    {
        $this->header->addJS('jstree/jquery.tree.js', 'Pages');
        $this->header->addJS('jstree/lib/jquery.cookie.js', 'Pages');
        $this->header->addJS('jstree/plugins/jquery.tree.cookie.js', 'Pages');
    }
}
