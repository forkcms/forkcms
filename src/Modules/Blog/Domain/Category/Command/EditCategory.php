<?php

namespace ForkCMS\Modules\Blog\Domain\Category\Command;

use ForkCMS\Modules\Blog\Domain\Category\Category;

class EditCategory extends CategoryDataTransferObject
{
    public function __construct(Category $category)
    {
        parent::__construct($category);
    }
}
