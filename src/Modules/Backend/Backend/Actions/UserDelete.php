<?php

namespace ForkCMS\Modules\Backend\Backend\Actions;

use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessage;
use ForkCMS\Modules\Backend\Domain\Action\AbstractDeleteActionController;
use ForkCMS\Modules\Backend\Domain\User\Command\DeleteUser;
use ForkCMS\Modules\Backend\Domain\User\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Delete users from the backend.
 */
final class UserDelete extends AbstractDeleteActionController
{
    protected function getFormResponse(Request $request): RedirectResponse
    {
        $user = $this->getEntityFromRequestOrNull($request, User::class, 'action.id');

        return $this->handleDeleteForm(
            $request,
            DeleteUser::class,
            UserIndex::getActionSlug(),
            FlashMessage::success('UserDeleted', ['%user%' => $user?->getDisplayName()]),
            notFoundFlashMessage: FlashMessage::error('NonExistingUser')
        );
    }
}
