<?php

namespace Backend\Modules\Pages\Domain\PageBlock;

use RuntimeException;

final class PageBlockNotFound extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('PageBlock not found');
    }
}
