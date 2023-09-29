<?php

namespace ForkCMS\Modules\Backend\Backend\Ajax;

use ForkCMS\Modules\Backend\Domain\AjaxAction\AbstractAjaxActionController;
use ForkCMS\Modules\Backend\Domain\User\User;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConfirmTwoFactorAuthentication extends AbstractAjaxActionController
{
    private int $responseCode = Response::HTTP_OK;

    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly GoogleAuthenticatorInterface $googleAuthenticator,
        private readonly TranslatorInterface $translator,
    ) {
    }

    protected function execute(Request $request): void
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser()->getId();
        $secret = $request->request->get('secret');
        $code = $request->request->get('code');

        $user->setGoogleAuthenticatorSecret($secret);
        if (!$this->googleAuthenticator->checkCode($user, $code)) {
            $this->responseCode = Response::HTTP_UNAUTHORIZED;
            $this->assign('message', $this->translator->trans('msg.InvalidCode'));
            $user->setGoogleAuthenticatorSecret(null);

            return;
        }

        $backupCodes = [];
        for ($i = 0; $i < 10; ++$i) {
            $backupCodes[] = $this->googleAuthenticator->generateSecret();
        }

        $user->setBackupCodes($backupCodes);
        $this->assign('backupCodes', $backupCodes);
        $this->assign('message', $this->translator->trans('msg.2FAEnabled'));
    }

    public function getResponse(Request $request): Response
    {
        $response = parent::getResponse($request);
        $response->setStatusCode(Response::HTTP_FORBIDDEN);

        return $response;
    }
}
