<?php

namespace ForkCMS\Modules\Backend\Backend\Ajax;

use ForkCMS\Modules\Backend\Domain\AjaxAction\AbstractAjaxActionController;
use ForkCMS\Modules\Backend\Domain\User\User;
use ForkCMS\Modules\Backend\Domain\User\UserRepository;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AjaxActionConfirmTwoFactorAuthorizationCode extends AbstractAjaxActionController
{
    public function __construct(
        private readonly GoogleAuthenticatorInterface $googleAuthenticator,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly UserRepository $userRepository
    ) {
    }

    public function execute(Request $request): void
    {
        $parameters = json_decode(
            $request->request->get('parameters'),
            true
        );

        if (!is_array($parameters) || !array_key_exists('code', $parameters) || !array_key_exists('secret', $parameters)) {
            $this->assign('error', 'Invalid parameters');

            return;
        }

        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        $user->setGoogleAuthenticatorSecret($parameters['secret']);

        if (!$this->googleAuthenticator->checkCode($user, $parameters['code'])) {
            $this->assign('error', 'Invalid authorization code');

            return;
        }

        $backupCodes = [];
        for ($i = 0; $i < 10; ++$i) {
            $backupCodes[] = $this->googleAuthenticator->generateSecret();
        }
        $user->setBackupCodes($backupCodes);
        $this->userRepository->save($user);

        $this->assign('backupCodes', $backupCodes);
    }
}
