<?php

namespace ForkCMS\Modules\Blog\Backend\Actions;

use ForkCMS\Modules\Backend\Domain\Action\AbstractDataGridActionController;
use ForkCMS\Modules\Blog\Domain\BlogPost\BlogPost;
use Symfony\Component\HttpFoundation\Request;

class BlogIndex extends AbstractDataGridActionController
{
    protected function execute(Request $request): void
    {
        // TODO: Implement execute() method.
        $this->renderDataGrid(BlogPost::class);
    }
}
