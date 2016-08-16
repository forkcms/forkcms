<?php

namespace Backend\Modules\ContentBlocks\DataGrid;

use Backend\Core\Engine\DataGridDB;
use Backend\Core\Language\Locale;
use Backend\Modules\ContentBlocks\Entity\ContentBlock;
use Backend\Modules\ContentBlocks\ValueObject\Status;

class RevisionDataGrid extends DataGridDB
{
    /**
     * @param ContentBlock $contentBlock
     * @param Locale $locale
     */
    public function __construct(ContentBlock $contentBlock, Locale $locale)
    {
        parent::__construct(
            'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.user_id
             FROM content_blocks AS i
             WHERE i.status = :archived AND i.id = :id AND i.language = :language
             ORDER BY i.edited_on DESC',
            ['archived' => Status::archived(), 'language' => $locale, 'id' => $contentBlock->getId()]
        );
    }
}
