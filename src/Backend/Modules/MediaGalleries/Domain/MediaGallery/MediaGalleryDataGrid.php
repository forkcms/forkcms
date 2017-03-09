<?php

namespace Backend\Modules\MediaGalleries\Domain\MediaGallery;

use Backend\Core\Engine\Authentication;
use Backend\Core\Engine\DataGridDB;
use Backend\Core\Engine\DataGridFunctions;
use Backend\Core\Engine\Model;
use Backend\Core\Language\Language;

/**
 * @TODO replace with a doctrine implementation of the data grid
 */
class MediaGalleryDataGrid extends DataGridDB
{
    /**
     * MediaGalleryDataGrid constructor.
     */
    public function __construct()
    {
        parent::__construct(
            'SELECT i.id, i.title, i.action, UNIX_TIMESTAMP(i.publishOn) AS publishOn, UNIX_TIMESTAMP(i.editedOn) AS editedOn
             FROM MediaGallery AS i'
        );

        // Setting header labels
        $this->setHeaderLabels(array(
            'title' => ucfirst(Language::lbl('Title'))
        ));

        // Sorting columns
        $this->setSortingColumns(
            array(
                'title',
                'action',
                'publishOn',
                'editedOn'
            ),
            'title'
        );

        // Set sorting parameter
        $this->setSortParameter('asc');

        // Set column functions
        $this->setColumnFunction(
            array(new DataGridFunctions(), 'getLongDate'),
            array('[publishOn]'),
            'publishOn',
            true
        );
        $this->setColumnFunction(
            array(new DataGridFunctions(), 'getLongDate'),
            array('[editedOn]'),
            'editedOn',
            true
        );

        if (Authentication::isAllowedAction('MediaGalleryEdit')) {
            // Define edit url
            $editUrl = Model::createURLForAction('MediaGalleryEdit', null, null, ['id' => '[id]'], false);
            $this->setColumnURL('title', $editUrl);
            $this->addColumn('edit', null, Language::lbl('MediaGalleryEdit'), $editUrl, Language::lbl('MediaGalleryEdit'));
        }
    }

    /**
     * @return string
     */
    public static function getHtml(): string
    {
        $dataGrid = new self();

        return (string) $dataGrid->getContent();
    }
}
