<?php

namespace Backend\Modules\MediaLibrary\Actions;

use Backend\Core\Language\Language;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroupType;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItemSelectionDataGrid;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Type;

class MediaBrowserVideos extends MediaBrowser
{
    public function display(string $template = null): void
    {
        parent::display($template ?? '/' . $this->getModule() . '/Layout/Templates/MediaBrowser.html.twig');
    }

    protected function parse(): void
    {
        // Parse files necessary for the media upload helper
        MediaGroupType::parseFiles();

        parent::parseDataGrids($this->mediaFolder);

        /** @var int|null $mediaFolderId */
        $mediaFolderId = ($this->mediaFolder instanceof MediaFolder) ? $this->mediaFolder->getId() : null;

        $this->template->assign('folderId', $mediaFolderId);
        $this->template->assign('mediaType', 'video');
        $this->template->assign('tree', $this->get('media_library.manager.tree_media_browser_videos')->getHTML());
        $this->header->addJsData('MediaLibrary', 'openedFolderId', $mediaFolderId);
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
            [Type::MOVIE]
        );
    }
}
