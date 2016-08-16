<?php

namespace Backend\Modules\ContentBlocks\DataGrid;

use Backend\Core\Engine\DataGridDB;
use Backend\Core\Language\Locale;
use Backend\Modules\ContentBlocks\ValueObject\Status;

/**
 * @TODO replace with a doctrine implementation of the data grid
 */
class ContentBlockDataGrid extends DataGridDB
{
    /**
     * @param Locale $locale
     */
    public function __construct(Locale $locale)
    {
        parent::__construct(
            'SELECT i.id, i.title, i.hidden
             FROM content_blocks AS i
             WHERE i.status = :active AND i.language = :language',
            ['active' => Status::active(), 'language' => $locale]
        );
    }
}
