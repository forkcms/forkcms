<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem;

use Backend\Core\Engine\DataGridDatabase;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
use Backend\Core\Engine\Model;
use Backend\Core\Language\Language;
use SpoonFormDropdown;

/**
 * @TODO replace with a doctrine implementation of the data grid
 */
class MediaItemDataGrid extends DataGridDatabase
{
    public function __construct(Type $type, int $folderId = null)
    {
        parent::__construct(
            'SELECT i.id, i.storageType, i.type, i.url, i.title, i.shardingFolderName,
                COUNT(gi.mediaItemId) as num_connected, i.mime, UNIX_TIMESTAMP(i.createdOn) AS createdOn
             FROM MediaItem AS i
             LEFT OUTER JOIN MediaGroupMediaItem as gi ON gi.mediaItemId = i.id
             WHERE i.type = ?' . $this->getWhere($folderId) . ' GROUP BY i.id',
            $this->getParameters($type, $folderId)
        );

        // filter on folder?
        if ($folderId !== null) {
            $this->setURL('&folder=' . $folderId, true);
        }

        $this->setExtras($type);
        $this->addMassActions($type);
    }

    private function addMassActions(Type $type): void
    {
        $this->setMassActionCheckboxes('check', '[id]');
        $this->setMassAction($this->getMassActionDropdown($type));
    }

    private function getColumnHeaderLabels(Type $type): array
    {
        if ($type->isMovie()) {
            return [
                'storageType' => ucfirst(Language::lbl('MediaStorageType')),
                'url' => ucfirst(Language::lbl('MediaMovieId')),
                'title' => ucfirst(Language::lbl('MediaMovieTitle')),
            ];
        }

        return [
            'type' => '',
            'url' => ucfirst(Language::lbl('Image')),
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

    private function getMassActionDropdown(Type $type): SpoonFormDropdown
    {
        $ddmMediaItemMassAction = new SpoonFormDropdown(
            'action',
            ['move' => Language::lbl('Move')],
            'move',
            false,
            'form-control',
            'form-control danger'
        );
        $ddmMediaItemMassAction->setAttribute('id', 'mass-action-' . (string) $type);
        $ddmMediaItemMassAction->setOptionAttributes('move', ['data-target' => '#confirmMassActionMediaItemMove']);

        return $ddmMediaItemMassAction;
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

    private function setExtras(Type $type, int $folderId = null): void
    {
        $editActionUrl = Model::createUrlForAction('MediaItemEdit');
        $this->setHeaderLabels($this->getColumnHeaderLabels($type));
        $this->setActiveTab('tab' . ucfirst((string) $type));
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
        $this->setColumnURL(
            'title',
            $editActionUrl
            . '&id=[id]'
            . ($folderId ? '&folder=' . $folderId : '')
        );

        if ($type->isMovie()) {
            $this->setColumnURL(
                'url',
                $editActionUrl
                . '&id=[id]'
                . ($folderId ? '&folder=' . $folderId : '')
            );
        }

        $this->setColumnURL(
            'num_connected',
            $editActionUrl
            . '&id=[id]'
            . ($folderId ? '&folder=' . $folderId : '')
        );

        // If we have an image, show the image
        if ($type->isImage()) {
            // Add image url
            $this->setColumnFunction(
                [new BackendDataGridFunctions(), 'showImage'],
                [
                    Model::get('media_library.storage.local')->getWebDir() . '/[shardingFolderName]',
                    '[url]',
                    '[url]',
                    Model::createUrlForAction('MediaItemEdit') . '&id=[id]' . '&folder=' . $folderId,
                    null,
                    null,
                    'media_library_backend_thumbnail',
                ],
                'url',
                true
            );
        }

        // set column functions
        $this->setColumnFunction(
            [new BackendDataGridFunctions(), 'getLongDate'],
            ['[createdOn]'],
            'createdOn',
            true
        );

        // add edit column
        $this->addColumn(
            'edit',
            null,
            Language::lbl('Edit'),
            $editActionUrl . '&id=[id]' . '&folder=' . $folderId,
            Language::lbl('Edit')
        );

        // our JS needs to know an id, so we can highlight it
        $this->setRowAttributes(['id' => 'row-[id]']);
    }
}
