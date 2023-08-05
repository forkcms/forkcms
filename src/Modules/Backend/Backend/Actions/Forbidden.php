<?php

namespace ForkCMS\Modules\Backend\Backend\Actions;

use ForkCMS\Modules\Backend\Domain\Action\AbstractActionController;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * This class will handle the page when the user does not have access to it.
 */
final class Forbidden extends AbstractActionController
{
    protected function execute(Request $request): void
    {
        $this->assign('message', TranslationKey::error('ActionNotAllowed'));
        $this->assign('page_title', TranslationKey::label('Error'));
    }

    public function getResponse(Request $request): Response
    {
        $response = parent::getResponse($request);
        $response->setStatusCode(Response::HTTP_FORBIDDEN);

        return $response;
    }
}
