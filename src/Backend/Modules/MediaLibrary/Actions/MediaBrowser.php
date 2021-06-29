<?php

namespace Backend\Modules\MediaLibrary\Actions;

use Backend\Core\Engine\Base\Action as BackendBaseAction;
use Backend\Core\Language\Language;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\Exception\MediaFolderNotFound;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolderRepository;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroupType;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItemSelectionDataGrid;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Type;

class MediaBrowser extends BackendBaseAction
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
        $id = $this->getRequest()->query->getInt('folder');

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

        $this->parseDataGrids($this->mediaFolder);

        /** @var int|null $mediaFolderId */
        $mediaFolderId = ($this->mediaFolder instanceof MediaFolder) ? $this->mediaFolder->getId() : null;

        $this->template->assign('folderId', $mediaFolderId);
        $this->template->assign('tree', $this->get('media_library.manager.tree_media_browser')->getHTML());
        $this->header->addJsData('MediaLibrary', 'openedFolderId', $mediaFolderId);
    }

    protected function parseDataGrids(MediaFolder $mediaFolder = null): void
    {
        $dataGrids = $this->getDataGrids($mediaFolder);

        $this->template->assign('dataGrids', $dataGrids);
        $this->template->assign('hasResults', $this->hasResults($dataGrids));
    }

    protected function getDataGrids(MediaFolder $mediaFolder = null): array
    {
        return array_map(
            function ($type) use ($mediaFolder) {
                $dataGrid = MediaItemSelectionDataGrid::getDataGrid(
                    Type::fromString($type),
                    ($mediaFolder !== null) ? $mediaFolder->getId() : null
                );

                return [
                    'label' => Language::lbl('MediaMultiple' . ucfirst($type)),
                    'tabName' => 'tab' . ucfirst($type),
                    'mediaType' => $type,
                    'html' => $dataGrid->getContent(),
                    'numberOfResults' => $dataGrid->getNumResults(),
                ];
            },
            Type::POSSIBLE_VALUES
        );
    }

    private function hasResults(array $dataGrids): bool
    {
        $totalResultCount = array_sum(
            array_map(
                function ($dataGrid) {
                    return $dataGrid['numberOfResults'];
                },
                $dataGrids
            )
        );

        return $totalResultCount > 0;
    }
}
