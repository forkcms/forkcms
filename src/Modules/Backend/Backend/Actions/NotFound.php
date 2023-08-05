<?php

namespace ForkCMS\Modules\Backend\Backend\Actions;

use ForkCMS\Modules\Backend\Domain\Action\AbstractActionController;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * This class will handle the backend 404 page.
 */
final class NotFound extends AbstractActionController
{
    protected function execute(Request $request): void
    {
        $this->assign('message', TranslationKey::error('NotFound'));
        $this->assign('page_title', 404);
    }

    public function getResponse(Request $request): Response
    {
        $response = parent::getResponse($request);
        $response->setStatusCode(Response::HTTP_NOT_FOUND);

        return $response;
    }
}
