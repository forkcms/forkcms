<?php

namespace ForkCMS\Modules\Backend\Backend\Actions;

use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessage;
use ForkCMS\Modules\Backend\Domain\Action\AbstractDeleteActionController;
use ForkCMS\Modules\Backend\Domain\User\Command\DeleteUser;
use ForkCMS\Modules\Backend\Domain\User\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Delete users from the backend.
 */
final class UserDelete extends AbstractDeleteActionController
{
    protected function getFormResponse(Request $request): RedirectResponse
    {
        $successFlashMessage = null;
        $userId = $request->request->all('action')['id'] ?? null;
        if ($userId !== null) {
            $user = $this->getRepository(User::class)->find($userId);
            if ($user instanceof User) {
                $successFlashMessage = FlashMessage::success('UserDeleted', ['%user%' => $user->getDisplayName()]);
            }
        }

        return $this->handleDeleteForm(
            $request,
            DeleteUser::class,
            UserIndex::getActionSlug(),
            $successFlashMessage,
            notFoundFlashMessage: FlashMessage::error('NonExistingUser')
        );
    }
}
