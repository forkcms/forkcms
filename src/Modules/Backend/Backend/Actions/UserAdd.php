<?php

namespace ForkCMS\Modules\Backend\Backend\Actions;

use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessage;
use ForkCMS\Modules\Backend\Domain\Action\AbstractFormActionController;
use ForkCMS\Modules\Backend\Domain\User\Command\CreateUser;
use ForkCMS\Modules\Backend\Domain\User\UserType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Add new users to the backend.
 */
final class UserAdd extends AbstractFormActionController
{
    protected function getFormResponse(Request $request): ?Response
    {
        return $this->handleForm(
            request: $request,
            formType: UserType::class,
            formData: new CreateUser(),
            formOptions: [
                'validation_groups' => ['Default', 'create'],
            ],
            validCallback: function (FormInterface $form) use ($request): Response {
                $redirectResponse = new RedirectResponse(UserIndex::getActionSlug()->generateRoute($this->router));
                $this->commandBus->dispatch($form->getData());

                if ($form->getData()->enableTwoFactorAuthentication) {
                    $request->getSession()->set('showBackupCodes', true);

                    return new RedirectResponse(UserEdit::getActionSlug()->generateRoute($this->router) . '/' . $form->getData()->getEntity()->getId());
                }

                return $redirectResponse;
            },
            flashMessageCallback: static function (FormInterface $form): FlashMessage {
                return FlashMessage::success('UserAdded', ['%user%' => $form->getData()->displayName]);
            }
        );
    }
}
