<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem;

use Backend\Core\Engine\DataGridDB;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
use Backend\Core\Engine\Model;
use Backend\Core\Language\Language;
use Backend\Modules\MediaLibrary\Component\StorageProvider\LocalStorageProvider;

/**
 * @TODO replace with a doctrine implementation of the data grid
 */
class MediaItemDataGrid extends DataGridDB
{
    /**
     * MediaItemDataGrid constructor.
     *
     * @param Type $type
     * @param int|null $folderId
     */
    public function __construct(Type $type, int $folderId = null)
    {
        $andWhere = '';
        $parameters = [(string) $type];

        if ($folderId !== null) {
            $andWhere .= ' AND i.mediaFolderId = ?';
            $parameters[] = (int) $folderId;
        }

        parent::__construct(
            'SELECT i.id, i.storageType, i.type, i.url, i.title, i.shardingFolderName, COUNT(gi.mediaItemId) as num_connected, i.mime, UNIX_TIMESTAMP(i.createdOn) AS createdOn
             FROM MediaItem AS i
             LEFT OUTER JOIN MediaGroupMediaItem as gi ON gi.mediaItemId = i.id
             WHERE i.type = ?'
                . $andWhere
                . ' GROUP BY i.id',
            $parameters
        );

        // filter on folder?
        if ($folderId !== null) {
            // set the URL
            $this->setURL('&folder=' . $folderId, true);
        }

        // define editActionUrl
        $editActionUrl = Model::createURLForAction('MediaItemEdit');

        // set headers
        $this->setHeaderLabels($this->getColumnHeaderLabels($type));

        // active tab
        $this->setActiveTab('tab' . ucfirst((string) $type));

        // hide columns
        $this->setColumnsHidden($this->getColumnsThatNeedToBeHidden($type));

        // sorting columns
        $this->setSortingColumns(
            array(
                'createdOn',
                'url',
                'title',
                'num_connected',
                'mime'
            ),
            'title'
        );
        $this->setSortParameter('asc');

        // set column URLs
        $this->setColumnURL(
            'title',
            $editActionUrl
            . '&id=[id]'
            . (($folderId) ? '&folder=' . $folderId : '')
        );

        if ($type->isMovie()) {
            // set column URLs
            $this->setColumnURL(
                'url',
                $editActionUrl
                . '&id=[id]'
                . (($folderId) ? '&folder=' . $folderId : '')
            );
        }

        $this->setColumnURL(
            'num_connected',
            $editActionUrl
            . '&id=[id]'
            . (($folderId) ? '&folder=' . $folderId : '')
        );

        // If we have an image, show the image
        if ($type->isImage()) {
            // Add image url
            $this->setColumnFunction(
                array(new BackendDataGridFunctions(), 'showImage'),
                array(
                    Model::get('media_library.storage.local')->getWebDir() . '/[shardingFolderName]',
                    '[url]',
                    '[url]',
                    Model::createURLForAction('MediaItemEdit')
                    . '&id=[id]'
                    . '&folder=' . $folderId,
                    Model::get('fork.settings')->get('MediaLibrary', 'backend_thumbnail_width'),
                    Model::get('fork.settings')->get('MediaLibrary', 'backend_thumbnail_height'),
                    'media_library_backend_thumbnail'
                ),
                'url',
                true
            );
        }

        // set column functions
        $this->setColumnFunction(
            array(new BackendDataGridFunctions(), 'getLongDate'),
            array('[createdOn]'),
            'createdOn',
            true
        );

        // add edit column
        $this->addColumn(
            'edit',
            null,
            Language::lbl('Edit'),
            $editActionUrl
            . '&id=[id]'
            . '&folder=' . $folderId,
            Language::lbl('Edit')
        );

        // our JS needs to know an id, so we can highlight it
        $this->setRowAttributes(array('id' => 'row-[id]'));

        // add checkboxes
        $this->setMassActionCheckboxes('check', '[id]');

        // add mass action dropdown
        $ddmMediaItemMassAction = new \SpoonFormDropdown(
            'action',
            array(
                'move' => Language::lbl('MoveMedia'),
                'delete' => Language::lbl('MediaItemDeletes')
            ),
            'move',
            false,
            'form-control',
            'form-control danger'
        );
        $ddmMediaItemMassAction->setAttribute(
            'id',
            'mass-action-' . (string) $type
        );
        $ddmMediaItemMassAction->setOptionAttributes(
            'move',
            array(
                'data-target' => '#confirmMassActionMediaItemMove',
            )
        );
        $ddmMediaItemMassAction->setOptionAttributes(
            'delete',
            array(
                'data-target' => '#confirmMassActionMediaItemDelete',
            )
        );
        $this->setMassAction($ddmMediaItemMassAction);
    }

    /**
     * @param Type $type
     * @return array
     */
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

    /**
     * @param Type $type
     * @return array
     */
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

    /**
     * @param Type $type
     * @param int|null $folderId
     * @return DataGridDB
     */
    public static function getDataGrid(Type $type, int $folderId = null): DataGridDB
    {
        $dataGrid = new self($type, $folderId);

        return $dataGrid;
    }

    /**
     * @param Type $type
     * @param int|null $folderId
     * @return string
     */
    public static function getHtml(Type $type, int $folderId = null): string
    {
        $dataGrid = new self($type, $folderId);

        return (string) $dataGrid->getContent();
    }
}
