<?php

namespace Backend\Modules\MediaGalleries\Domain\MediaGallery;

use Backend\Core\Engine\Authentication;
use Backend\Core\Engine\DataGridDatabase;
use Backend\Core\Engine\DataGridFunctions;
use Backend\Core\Engine\Model;
use Backend\Core\Language\Language;

/**
 * @TODO replace with a doctrine implementation of the data grid
 */
class MediaGalleryDataGrid extends DataGridDatabase
{
    public function __construct()
    {
        parent::__construct(
            'SELECT i.id, i.title, i.action, UNIX_TIMESTAMP(i.editedOn) AS editedOn
             FROM MediaGallery AS i'
        );

        $this->setHeaderLabels(['title' => ucfirst(Language::lbl('Title'))]);
        $this->setSortingFunctions();
        $this->setExtraFunctions();
    }

    public static function getHtml(): string
    {
        return (string) (new self())->getContent();
    }

    private function setSortingFunctions(): void
    {
        $this->setSortingColumns(
            [
                'title',
                'action',
                'editedOn',
            ],
            'title'
        );
        $this->setSortParameter('asc');
    }

    private function setExtraFunctions(): void
    {
        $this->setColumnFunction(
            [new DataGridFunctions(), 'getLongDate'],
            ['[editedOn]'],
            'editedOn',
            true
        );

        if (Authentication::isAllowedAction('MediaGalleryEdit')) {
            // Define edit url
            $editUrl = Model::createUrlForAction('MediaGalleryEdit', null, null, ['id' => '[id]'], false);
            $this->setColumnURL('title', $editUrl);
            $this->addColumn('edit', null, Language::lbl('Edit'), $editUrl, Language::lbl('MediaGalleryEdit'));
        }
    }
}
