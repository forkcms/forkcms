<?php

namespace ForkCMS\Modules\Pages\Backend\Actions;

use ForkCMS\Modules\Backend\Domain\Action\AbstractFormActionController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class PageCopyToOtherLocale extends AbstractFormActionController
{
    protected function getFormResponse(Request $request): ?Response
    {
        // TODO: Implement getFormResponse() method.
        throw new \Exception('Method not implemented');
    }
}
