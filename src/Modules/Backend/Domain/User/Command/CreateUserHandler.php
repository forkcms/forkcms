<?php

namespace ForkCMS\Modules\Backend\Domain\User\Command;

use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use ForkCMS\Modules\Backend\Domain\User\User;
use ForkCMS\Modules\Backend\Domain\User\UserRepository;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class CreateUserHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly GoogleAuthenticatorInterface $googleAuthenticator,
    ) {
    }

    public function __invoke(CreateUser $createUser): void
    {
        $user = User::fromDataTransferObject($createUser);
        if (!$createUser->enableTwoFactorAuthentication) {
            $user->setGoogleAuthenticatorSecret(null);
            $user->setBackupCodes();
        } elseif ($user->getGoogleAuthenticatorSecret() === null) {
            $user->setGoogleAuthenticatorSecret(
                $this->generateGoogleAuthenticatorSecret()
            );

            $backupCodes = [];
            for ($i = 0; $i < 10; ++$i) {
                $backupCodes[] = $this->generateGoogleAuthenticatorSecret();
            }

            $user->setBackupCodes($backupCodes);
        }


        $user->hashPassword($this->passwordHasher);
        $this->userRepository->save($user);
        $createUser->setEntity($user);
        $this->eventDispatcher->dispatch($user);
    }

    private function generateGoogleAuthenticatorSecret(): string
    {
        return $this->googleAuthenticator->generateSecret();
    }
}
