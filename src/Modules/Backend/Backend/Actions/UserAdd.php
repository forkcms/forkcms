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
            redirectResponse: new RedirectResponse(UserIndex::getActionSlug()->generateRoute($this->router)),
            formOptions: [
                'validation_groups' => ['Default', 'create'],
            ],
            successFlashMessageCallback: static function (FormInterface $form): FlashMessage {
                return FlashMessage::success('UserAdded', ['%user%' => $form->getData()->displayName]);
            }
        );
    }
}
