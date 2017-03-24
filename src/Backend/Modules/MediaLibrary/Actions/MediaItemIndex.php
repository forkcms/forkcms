<?php

namespace Backend\Modules\MediaLibrary\Actions;

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\DataGridDB;
use Backend\Core\Language\Language;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Type;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItemDataGrid;

class MediaItemIndex extends BackendBaseActionIndex
{
    public function execute()
    {
        // call parent, this will probably add some general CSS/JS or other required files
        parent::execute();

        // Parse JS files
        $this->parseFiles();

        /** @var MediaFolder|null $mediaFolder */
        $mediaFolder = $this->getMediaFolder();

        /** @var array $dataGrids */
        $dataGrids = $this->getDataGrids($mediaFolder);

        // Assign variables
        $this->tpl->assign('tree', $this->get('media_library.manager.tree')->getHTML());
        $this->tpl->assign('mediaFolder', $mediaFolder);
        $this->tpl->assign('mediaFolders', $this->getMediaFoldersForDropdown($mediaFolder));
        $this->tpl->assign('dataGrids', $dataGrids);
        $this->tpl->assign('hasResults', ($this->getTotalFolderCount($dataGrids)) > 0);
        $this->header->addJsData('MediaLibrary', 'openedFolderId', ($mediaFolder !== null) ? $mediaFolder->getId() : null);

        $this->display();
    }

    /**
     * @param MediaFolder|null $mediaFolder
     * @return array
     */
    private function getDataGrids(MediaFolder $mediaFolder = null): array
    {
        $dataGrids = [];

        /** @var string $type */
        foreach (Type::POSSIBLE_VALUES as $type) {
            /** @var DataGridDB $dataGrid */
            $dataGrid = MediaItemDataGrid::getDataGrid(
                Type::fromString($type),
                ($mediaFolder !== null) ? $mediaFolder->getId() : null
            );

            // create datagrid
            $dataGrids[$type] = [
                'label' => Language::lbl('MediaMultiple' . ucfirst($type)),
                'tabName' => 'tab' . ucfirst($type),
                'mediaType' => $type,
                'html' => $dataGrid->getContent(),
                'numberOfResults' => $dataGrid->getNumResults(),
            ];
        }

        return $dataGrids;
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
     * @param MediaFolder|null $currentMediaFolder
     * @return array
     */
    private function getMediaFoldersForDropdown(MediaFolder $currentMediaFolder = null): array
    {
        // get folders
        $folders = $this->get('media_library.cache_builder')->getFoldersForDropdown();

        if ($currentMediaFolder !== null && isset($folders[$currentMediaFolder->getId()])) {
            unset($folders[$currentMediaFolder->getId()]);
        }

        // define parent folders
        $foldersForDropdown = [0 => ''];

        // loop all folders
        foreach ($folders as $value => $label) {
            // add to parent folders
            $foldersForDropdown[$value] = [
                'value' => $value,
                'label' => $label,
            ];
        }

        return $foldersForDropdown;
    }

    /**
     * @param array $dataGrids
     * @return int
     */
    private function getTotalFolderCount(array $dataGrids): int
    {
        $count = 0;

        /** @var array $dataGrid */
        foreach ($dataGrids as $dataGrid) {
            $count += $dataGrid['numberOfResults'];
        }

        return $count;
    }

    /**
     * Parse JS files
     */
    private function parseFiles()
    {
        $this->header->addJS('jstree/jquery.tree.js', 'Pages');
        $this->header->addJS('jstree/lib/jquery.cookie.js', 'Pages');
        $this->header->addJS('jstree/plugins/jquery.tree.cookie.js', 'Pages');
        $this->header->addJS('MediaLibraryAddFolder.js', 'MediaLibrary', true);
    }
}
