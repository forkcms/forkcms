<?php

namespace ForkCMS\Modules\Pages\Backend\Actions;

use ForkCMS\Modules\Backend\Domain\Action\AbstractDeleteActionController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class PageDelete extends AbstractDeleteActionController
{
    protected function getFormResponse(Request $request): ?Response
    {
        // TODO: Implement getFormResponse() method.
        throw new \Exception('Method not implemented');
    }
}
