<?php

namespace ForkCMS\Modules\Backend\Backend\Actions;

use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessage;
use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessageType;
use ForkCMS\Modules\Backend\Domain\Action\AbstractActionController;
use ForkCMS\Modules\Backend\Domain\Action\ActionServices;
use ForkCMS\Modules\Backend\Domain\User\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use ForkCMS\Modules\Backend\Domain\User\Command\DisableTwoFactorAuthentication as DisableTwoFactorAuthenticationCommand;
use Symfony\Component\HttpFoundation\Response;

class DisableTwoFactorAuthentication extends AbstractActionController
{
    private User $user;

    public function __construct(ActionServices $actionServices)
    {
        parent::__construct($actionServices);
    }

    protected function execute(Request $request): void
    {
        /** @var User $user */
        $this->user = $this->getEntityFromRequest($request, User::class);
        $this->commandBus->dispatch(
            new DisableTwoFactorAuthenticationCommand($this->user)
        );

        $this->header->addFlashMessage(
            new FlashMessage(
                'msg.2FAIsDisabled',
                FlashMessageType::SUCCESS
            )
        );
    }

    public function getResponse(Request $request): Response
    {
        return new RedirectResponse(
            UserEdit::getActionSlug()->generateRoute($this->router, ['slug' => $this->user->getId()])
        );
    }
}
