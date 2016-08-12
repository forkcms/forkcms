<?php

namespace Backend\Modules\ContentBlocks\ContentBlock;

use Backend\Core\Engine\DataGridDB;
use Backend\Core\Language\Locale;

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
            ['archived' => Status::active(), 'language' => $locale, 'id' => $contentBlock->getId()]
        );
    }
}
