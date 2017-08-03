<?php

namespace Backend\Modules\MediaLibrary\Actions;

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\Exception\MediaFolderNotFound;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroupType;

class MediaItemUpload extends BackendBaseActionAdd
{
    /** @var MediaFolder */
    protected $mediaFolder;

    public function execute(): void
    {
        parent::execute();

        /** @var MediaFolder|null $mediaFolder */
        $this->mediaFolder = $this->getMediaFolder();

        $this->parseJsFiles();
        $this->parseCssFiles();
        $this->parse();
        $this->display();
    }

    protected function getMediaFolder(): ?MediaFolder
    {
        /** @var int $id */
        $id = $this->get('request')->query->get('folder');

        try {
            return $this->get('media_library.repository.folder')->findOneById($id);
        } catch (MediaFolderNotFound $mediaFolderNotFound) {
            return null;
        }
    }

    protected function parse(): void
    {
        // Parse files necessary for the media upload helper
        MediaGroupType::parseFiles();

        /** @var int|null $mediaFolderId */
        $mediaFolderId = ($this->mediaFolder instanceof MediaFolder) ? $this->mediaFolder->getId() : null;

        $this->template->assign('folderId', $mediaFolderId);
        $this->template->assign('tree', $this->get('media_library.manager.tree')->getHTML());
        $this->header->addJsData('MediaLibrary', 'openedFolderId', $mediaFolderId);
    }

    private function parseJsFiles(): void
    {
        $this->header->addJS('/js/vendors/jstree.js', null, false, true);
    }

    private function parseCssFiles(): void
    {
        $this->header->addCSS('/css/vendors/jstree/style.css', null, true, false);
    }
}
