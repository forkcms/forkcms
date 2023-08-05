<?php

namespace ForkCMS\Modules\Backend\Backend\Ajax;

use ForkCMS\Modules\Backend\Domain\AjaxAction\AbstractAjaxActionController;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This class will handle the backend ajax 404 page.
 */
final class NotFound extends AbstractAjaxActionController
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    protected function execute(Request $request): void
    {
        $this->assign('message', TranslationKey::error('NotFound')->trans($this->translator));
    }

    public function getResponse(Request $request): Response
    {
        $response = parent::getResponse($request);
        $response->setStatusCode(Response::HTTP_NOT_FOUND);

        return $response;
    }
}
