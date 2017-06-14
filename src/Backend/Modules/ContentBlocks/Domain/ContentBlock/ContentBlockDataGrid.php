<?php

namespace Backend\Modules\ContentBlocks\Domain\ContentBlock;

use Backend\Core\Engine\DataGridDB;
use Backend\Core\Language\Locale;
use Backend\Modules\ContentBlocks\ValueObject\ContentBlockStatus;

/**
 * @TODO replace with a doctrine implementation of the data grid
 */
class ContentBlockDataGrid extends DataGridDB
{
    public function __construct(Locale $locale)
    {
        parent::__construct(
            'SELECT i.id, i.title, i.hidden
             FROM content_blocks AS i
             WHERE i.status = :active AND i.language = :language',
            ['active' => ContentBlockStatus::active(), 'language' => $locale]
        );
    }
}
