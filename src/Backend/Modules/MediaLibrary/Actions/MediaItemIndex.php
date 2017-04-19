<?php

namespace Backend\Modules\MediaLibrary\Actions;

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\DataGridDB;
use Backend\Core\Language\Language;
use Backend\Modules\MediaLibrary\Builder\MediaFolder\MediaFolderCacheItem;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaItem\StorageType;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Type;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItemDataGrid;

class MediaItemIndex extends BackendBaseActionIndex
{
    public function execute()
    {
        parent::execute();
        $this->parse();
        $this->parseJSFiles();
        $this->display();
    }

    /**
     * @param MediaFolder|null $mediaFolder
     * @return array
     */
    private function getDataGrids(MediaFolder $mediaFolder = null): array
    {
        return array_map(function ($type) use ($mediaFolder) {
            /** @var DataGridDB $dataGrid */
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
        }, Type::POSSIBLE_VALUES);
    }

    /**
     * @return MediaFolder|null
     */
    private function getMediaFolder()
    {
        // Define folder id
        $id = $this->getParameter('folder', 'int', 0);

        try {
            /** @var MediaFolder mediaFolder */
            return $this->get('media_library.repository.folder')->findOneById($id);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param MediaFolder|null $mediaFolder
     * @return array
     */
    private function getMediaFolders(MediaFolder $mediaFolder = null)
    {
        /** @var array $mediaFolders */
        $mediaFolders = $this->getMediaFoldersForDropdown($this->get('media_library.cache.media_folder')->get());

        // Unset mediaFolder
        if ($mediaFolder !== null) {
            unset($mediaFolders[$mediaFolder->getId()]);
        }

        return $mediaFolders;
    }

    /**
     * @param array $navigationItems
     * @param array $dropdownItems
     * @return array
     */
    private function getMediaFoldersForDropdown(array $navigationItems, array &$dropdownItems = []): array
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

    /**
     * @param array $dataGrids
     * @return bool
     */
    private function hasResults(array $dataGrids): bool
    {
        return array_sum(array_map(function ($dataGrid) {
            return $dataGrid['numberOfResults'];
        }, $dataGrids)) > 0;
    }

    protected function parse()
    {
        parent::parse();

        /** @var MediaFolder|null $mediaFolder */
        $mediaFolder = $this->getMediaFolder();

        // Assign variables
        $this->tpl->assign('tree', $this->get('media_library.manager.tree')->getHTML());

        $this->parseDataGrids($mediaFolder);
        $this->parseMediaFolders($mediaFolder);
    }

    /**
     * @param MediaFolder|null $mediaFolder
     */
    private function parseDataGrids(MediaFolder $mediaFolder = null)
    {
        /** @var array $dataGrids */
        $dataGrids = $this->getDataGrids($mediaFolder);

        $this->tpl->assign('dataGrids', $dataGrids);
        $this->tpl->assign('hasResults', $this->hasResults($dataGrids));
    }

    private function parseJSFiles()
    {
        $this->header->addJS('jstree/jquery.tree.js', 'Pages');
        $this->header->addJS('jstree/lib/jquery.cookie.js', 'Pages');
        $this->header->addJS('jstree/plugins/jquery.tree.cookie.js', 'Pages');
        $this->header->addJS('MediaLibraryFolders.js', 'MediaLibrary', true);
    }

    /**
     * @param MediaFolder|null $mediaFolder
     */
    private function parseMediaFolders(MediaFolder $mediaFolder = null)
    {
        $this->tpl->assign('mediaFolder', $mediaFolder);
        $this->tpl->assign('mediaFolders', $this->getMediaFolders($mediaFolder));
        $this->header->addJsData('MediaLibrary', 'openedFolderId', ($mediaFolder !== null) ? $mediaFolder->getId() : null);
    }
}
