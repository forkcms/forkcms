<?php

namespace ForkCMS\Modules\Backend\Domain\User\Command;

use ForkCMS\Modules\Backend\Domain\User\User;
use ForkCMS\Modules\Backend\Domain\User\UserDataTransferObject;

final class ChangeUser extends UserDataTransferObject
{
    public bool $enableTwoFactorAuthentication = false;

    public ?string $qrCode = null;

    public function __construct(User $user, ?string $qrCode = null)
    {
        parent::__construct($user);
        $this->enableTwoFactorAuthentication = $user->getGoogleAuthenticatorSecret() !== null;
        $this->qrCode = $qrCode;
    }
}
