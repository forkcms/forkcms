<?php

namespace Backend\Modules\MediaLibrary\Actions;

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\Exception\MediaFolderNotFound;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolderRepository;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroupType;

class MediaItemUpload extends BackendBaseActionAdd
{
    /** @var MediaFolder|null */
    protected $mediaFolder;

    public function execute(): void
    {
        parent::execute();

        $this->mediaFolder = $this->getMediaFolder();

        $this->parse();
        $this->display();
    }

    protected function getMediaFolder(): ?MediaFolder
    {
        /** @var int $id */
        $id = $this->getRequest()->query->get('folder');

        try {
            return $this->get(MediaFolderRepository::class)->findOneById($id);
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
        $this->header->appendDetailToBreadcrumbs((string) $this->mediaFolder);
    }
}
