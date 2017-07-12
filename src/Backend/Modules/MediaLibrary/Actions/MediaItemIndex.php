<?php

namespace Backend\Modules\MediaLibrary\Actions;

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\DataGridDatabase;
use Backend\Core\Language\Language;
use Backend\Modules\MediaLibrary\Builder\MediaFolder\MediaFolderCacheItem;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\Exception\MediaFolderNotFound;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Type;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItemDataGrid;

class MediaItemIndex extends BackendBaseActionIndex
{
    public function execute(): void
    {
        parent::execute();
        $this->parse();
        $this->parseJSFiles();
        $this->display();
    }

    private function getDataGrids(MediaFolder $mediaFolder = null): array
    {
        return array_map(
            function ($type) use ($mediaFolder) {
                /** @var DataGridDatabase $dataGrid */
                $dataGrid = MediaItemDataGrid::getDataGrid(
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

    private function getMediaFolder(): ?MediaFolder
    {
        // Define folder id
        $id = $this->getRequest()->query->getInt('folder');

        try {
            /** @var MediaFolder mediaFolder */
            return $this->get('media_library.repository.folder')->findOneById($id);
        } catch (MediaFolderNotFound $mediaFolderNotFound) {
            return null;
        }
    }

    private function getMediaFolders(MediaFolder $mediaFolder = null): array
    {
        /** @var array $mediaFolders */
        $mediaFolders = $this->getMediaFoldersForDropdown($this->get('media_library.cache.media_folder')->get());

        // Unset mediaFolder
        if ($mediaFolder !== null) {
            unset($mediaFolders[$mediaFolder->getId()]);
        }

        return $mediaFolders;
    }

    private function getMediaFoldersForDropdown(array $navigationItems, array $dropdownItems = []): array
    {
        /** @var MediaFolderCacheItem $cacheItem */
        foreach ($navigationItems as $cacheItem) {
            $dropdownItems[$cacheItem->id] = $cacheItem;

            if ($cacheItem->numberOfChildren > 0) {
                $dropdownItems = $this->getMediaFoldersForDropdown($cacheItem->children, $dropdownItems);
            }
        }

        return $dropdownItems;
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

    protected function parse(): void
    {
        parent::parse();

        /** @var MediaFolder|null $mediaFolder */
        $mediaFolder = $this->getMediaFolder();

        // Assign variables
        $this->template->assign('tree', $this->get('media_library.manager.tree')->getHTML());

        $this->parseDataGrids($mediaFolder);
        $this->parseMediaFolders($mediaFolder);
    }

    private function parseDataGrids(MediaFolder $mediaFolder = null): void
    {
        /** @var array $dataGrids */
        $dataGrids = $this->getDataGrids($mediaFolder);

        $this->template->assign('dataGrids', $dataGrids);
        $this->template->assign('hasResults', $this->hasResults($dataGrids));
    }

    private function parseJSFiles(): void
    {
        $this->header->addJS('jstree/jquery.tree.js', 'Pages');
        $this->header->addJS('jstree/lib/jquery.cookie.js', 'Pages');
        $this->header->addJS('jstree/plugins/jquery.tree.cookie.js', 'Pages');
        $this->header->addJS('MediaLibraryFolders.js', 'MediaLibrary', true);
    }

    private function parseMediaFolders(MediaFolder $mediaFolder = null): void
    {
        $this->template->assign('mediaFolder', $mediaFolder);
        $this->template->assign('mediaFolders', $this->getMediaFolders($mediaFolder));
        $this->header->addJsData(
            'MediaLibrary',
            'openedFolderId',
            ($mediaFolder !== null) ? $mediaFolder->getId() : null
        );
    }
}
