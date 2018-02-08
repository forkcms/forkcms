<?php

namespace Backend\Modules\ContentBlocks\Domain\ContentBlock;

use Backend\Core\Engine\Authentication;
use Backend\Core\Engine\DataGridDatabase;
use Backend\Core\Engine\DataGridFunctions;
use App\Component\Model\BackendModel;
use App\Component\Locale\BackendLanguage;
use App\Component\Locale\BackendLocale;

/**
 * @TODO replace with a doctrine implementation of the data grid
 */
class ContentBlockRevisionDataGrid extends DataGridDatabase
{
    public function __construct(ContentBlock $contentBlock, BackendLocale $locale)
    {
        parent::__construct(
            'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.user_id
             FROM content_blocks AS i
             WHERE i.status = :archived AND i.id = :id AND i.language = :language
             ORDER BY i.edited_on DESC',
            ['archived' => Status::archived(), 'language' => $locale, 'id' => $contentBlock->getId()]
        );

        // hide columns
        $this->setColumnsHidden(['id', 'revision_id']);

        // disable paging
        $this->setPaging(false);

        // set headers
        $this->setHeaderLabels(
            [
                'user_id' => ucfirst(BackendLanguage::lbl('By')),
                'edited_on' => ucfirst(BackendLanguage::lbl('LastEditedOn')),
            ]
        );

        // set column-functions
        $this->setColumnFunction([DataGridFunctions::class, 'getUser'], ['[user_id]'], 'user_id');
        $this->setColumnFunction([DataGridFunctions::class, 'getTimeAgo'], ['[edited_on]'], 'edited_on');

        // check if this action is allowed
        if (Authentication::isAllowedAction('Edit')) {
            $editRevisionUrl = BackendModel::createUrlForAction(
                'Edit',
                null,
                null,
                ['id' => '[id]', 'revision' => '[revision_id]'],
                false
            );
            // set column URLs
            $this->setColumnURL('title', $editRevisionUrl);

            // add use column
            $this->addColumn(
                'use_revision',
                null,
                BackendLanguage::lbl('UseThisVersion'),
                $editRevisionUrl,
                BackendLanguage::lbl('UseThisVersion')
            );
        }
    }

    public static function getHtml(ContentBlock $contentBlock, BackendLocale $locale): string
    {
        return (new self($contentBlock, $locale))->getContent();
    }
}
