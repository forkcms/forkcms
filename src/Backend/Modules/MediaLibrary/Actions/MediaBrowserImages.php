<?php

namespace Backend\Modules\MediaLibrary\Actions;

use Backend\Core\Language\Language;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItemSelectionDataGrid;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Type;

class MediaBrowserImages extends MediaBrowser
{
    public function execute(): void
    {
        $this->mediaFolder = $this->getMediaFolder();

        parent::parseJsFiles();
        parent::parse();
        $this->display('/' . $this->getModule() . '/Layout/Templates/MediaBrowser.html.twig');
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
            [Type::IMAGE]
        );
    }
}
