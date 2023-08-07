<?php

namespace ForkCMS\Modules\Pages\Backend\Ajax;

use ForkCMS\Modules\Backend\Domain\AjaxAction\AbstractAjaxActionController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Move a page over ajax
 */
final class PageMove extends AbstractAjaxActionController
{
    protected function execute(Request $request): void
    {
        // TODO: Implement getFormResponse() method.
        throw new \Exception('Method not implemented');
    }
}
