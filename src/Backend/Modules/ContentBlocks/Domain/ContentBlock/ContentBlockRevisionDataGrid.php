<?php

namespace Backend\Modules\ContentBlocks\DataGrid;

use Backend\Core\Engine\DataGridDB;
use Backend\Core\Language\Locale;
use Backend\Modules\ContentBlocks\Entity\ContentBlock;
use Backend\Modules\ContentBlocks\ValueObject\ContentBlockStatus;

/**
 * @TODO replace with a doctrine implementation of the data grid
 */
class ContentBlockRevisionDataGrid extends DataGridDB
{
    public function __construct(ContentBlock $contentBlock, Locale $locale)
    {
        parent::__construct(
            'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.user_id
             FROM content_blocks AS i
             WHERE i.status = :archived AND i.id = :id AND i.language = :language
             ORDER BY i.edited_on DESC',
            ['archived' => ContentBlockStatus::archived(), 'language' => $locale, 'id' => $contentBlock->getId()]
        );
    }
}
