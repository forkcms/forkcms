<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem;

use Backend\Core\Engine\DataGridDatabase;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
use Backend\Core\Engine\Model;
use Backend\Core\Language\Language;
use SpoonFilter;

/**
 * @TODO replace with a doctrine implementation of the data grid
 */
class MediaItemSelectionDataGrid extends DataGridDatabase
{
    public function __construct(Type $type, int $folderId = null)
    {
        parent::__construct(
            'SELECT i.url AS directUrl, i.id, i.storageType, i.type, i.url, i.title, i.shardingFolderName,
                COUNT(gi.mediaItemId) AS num_connected, i.mime, UNIX_TIMESTAMP(i.createdOn) AS createdOn
             FROM MediaItem AS i
             LEFT OUTER JOIN MediaGroupMediaItem AS gi ON gi.mediaItemId = i.id
             WHERE i.type = ?' . $this->getWhere($folderId) . ' GROUP BY i.id',
            $this->getParameters($type, $folderId)
        );

        // filter on folder?
        if ($folderId !== null) {
            $this->setURL('&folder=' . $folderId, true);
        }

        $this->setExtras($type);
    }

    private function getColumnHeaderLabels(Type $type): array
    {
        if ($type->isMovie()) {
            return [
                'storageType' => SpoonFilter::ucfirst(Language::lbl('MediaStorageType')),
                'url' => SpoonFilter::ucfirst(Language::lbl('MediaMovieId')),
                'title' => SpoonFilter::ucfirst(Language::lbl('MediaMovieTitle')),
                'directUrl' => '',
            ];
        }

        return [
            'type' => '',
            'url' => SpoonFilter::ucfirst(Language::lbl('Image')),
            'directUrl' => '',
        ];
    }

    private function getColumnsThatNeedToBeHidden(Type $type): array
    {
        if ($type->isImage()) {
            return ['storageType', 'shardingFolderName', 'type', 'mime'];
        }

        if ($type->isMovie()) {
            return ['shardingFolderName', 'type', 'mime'];
        }

        return ['storageType', 'shardingFolderName', 'type', 'mime', 'url'];
    }

    public static function getDataGrid(Type $type, int $folderId = null): DataGridDatabase
    {
        return new self($type, $folderId);
    }

    public static function getHtml(Type $type, int $folderId = null): string
    {
        return (string) (new self($type, $folderId))->getContent();
    }

    private function getParameters(Type $type, int $folderId = null): array
    {
        $parameters = [(string) $type];

        if ($folderId !== null) {
            $parameters[] = $folderId;
        }

        return $parameters;
    }

    private function getWhere(int $folderId = null): string
    {
        return ($folderId !== null) ? ' AND i.mediaFolderId = ?' : '';
    }

    private function setExtras(Type $type): void
    {
        $this->addDataAttributes();
        $this->setHeaderLabels($this->getColumnHeaderLabels($type));
        $this->setColumnsHidden($this->getColumnsThatNeedToBeHidden($type));
        $this->setSortingColumns(
            [
                'createdOn',
                'url',
                'title',
                'num_connected',
                'mime',
            ],
            'title'
        );
        $this->setSortParameter('asc');

        // If we have an image, show the image
        if ($type->isImage()) {
            // Add image url
            $this->setColumnFunction(
                [
                    new BackendDataGridFunctions(),
                    'showImage',
                ],
                [
                    Model::get('media_library.storage.local')->getWebDir() . '/[shardingFolderName]',
                    '[url]',
                    '[url]',
                    null,
                    null,
                    null,
                    'media_library_backend_thumbnail',
                ],
                'url',
                true
            );
        }

        // Add a button to select an item
        $this->setColumnFunction(
            [
                MediaItemSelectionDataGrid::class,
                'addButton',
            ],
            [
                '[id]',
                $type,
                '[storageType]',
                '[directUrl]',
            ],
            'directUrl',
            true
        );

        // set column functions
        $this->setColumnFunction(
            [new BackendDataGridFunctions(), 'getLongDate'],
            ['[createdOn]'],
            'createdOn',
            true
        );
        $this->setColumnFunction('htmlspecialchars', ['[title]'], 'title', false);
    }

    private function addDataAttributes(): void
    {
        // our JS needs to know an id, so we can highlight it
        $attributes = [
            'id' => 'row-[id]',
        ];
        $this->setRowAttributes($attributes);
    }

    protected function addButton(string $id, string $type, string $storageType)
    {
        switch ($type) {
            case Type::MOVIE:
                if ($storageType === StorageType::YOUTUBE) {
                    $absoluteUrl = Model::get('media_library.storage.youtube')->getAbsoluteWebPath(
                        Model::get(MediaItemRepository::class)->find($id)
                    );

                    break;
                }

                if ($storageType === StorageType::VIMEO) {
                    $absoluteUrl = Model::get('media_library.storage.vimeo')->getAbsoluteWebPath(
                        Model::get(MediaItemRepository::class)->find($id)
                    );

                    break;
                }

                $absoluteUrl = Model::get('media_library.storage.local')->getWebPath(
                    Model::get(MediaItemRepository::class)->find($id)
                );

                break;
            default:
                $absoluteUrl = Model::get('media_library.storage.local')->getWebPath(
                    Model::get(MediaItemRepository::class)->find($id)
                );
        }

        return '<button class="btn btn-primary btn-sm" data-direct-url="' . $absoluteUrl . '">' .
               SpoonFilter::ucfirst(Language::lbl('Select')) . '</button>';
    }
}
