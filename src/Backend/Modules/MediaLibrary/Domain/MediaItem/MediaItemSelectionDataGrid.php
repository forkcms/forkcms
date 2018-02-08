<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem;

use Backend\Core\Engine\DataGridDatabase;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
use App\Component\Model\BackendModel;
use App\Component\Locale\BackendLanguage;

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
                'storageType' => ucfirst(BackendLanguage::lbl('MediaStorageType')),
                'url' => ucfirst(BackendLanguage::lbl('MediaMovieId')),
                'title' => ucfirst(BackendLanguage::lbl('MediaMovieTitle')),
                'directUrl' => '',
            ];
        }

        return [
            'type' => '',
            'url' => ucfirst(BackendLanguage::lbl('Image')),
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
        $this->addDataAttributes($type);
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
                    BackendModel::get('media_library.storage.local')->getWebDir() . '/[shardingFolderName]',
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
                '[directUrl]'
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
    }

    private function addDataAttributes(Type $type): void
    {
        // our JS needs to know an id, so we can highlight it
        $attributes = [
            'id' => 'row-[id]',
            'data-direct-url' => '[directUrl]',
        ];
        $this->setRowAttributes($attributes);
    }

    protected function addButton(string $id, string $type, string $storageType)
    {
        switch ($type) {
            case Type::MOVIE:
                if ($storageType === StorageType::YOUTUBE) {
                    $absoluteUrl = BackendModel::get('media_library.storage.youtube')->getAbsoluteWebPath(
                        BackendModel::get('media_library.repository.item')->find($id)
                    );

                    break;
                }

                if ($storageType === StorageType::VIMEO) {
                    $absoluteUrl =  BackendModel::get('media_library.storage.vimeo')->getAbsoluteWebPath(
                        BackendModel::get('media_library.repository.item')->find($id)
                    );

                    break;
                }

                $absoluteUrl = BackendModel::get('media_library.storage.local')->getWebPath(
                    BackendModel::get('media_library.repository.item')->find($id)
                );

                break;
            default:
                $absoluteUrl = BackendModel::get('media_library.storage.local')->getWebPath(
                    BackendModel::get('media_library.repository.item')->find($id)
                );
        }

        return '<a class="btn btn-success" data-direct-url="' . $absoluteUrl . '">' .
            ucfirst(BackendLanguage::lbl('Select')) . '</a>';
    }
}
