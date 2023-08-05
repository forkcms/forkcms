<?php

namespace ForkCMS\Modules\Backend\Backend\Actions;

use ForkCMS\Modules\Backend\Domain\Action\AbstractDeleteActionController;
use ForkCMS\Modules\Backend\Domain\User\Command\DeleteUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Delete users from the backend.
 */
final class UserDelete extends AbstractDeleteActionController
{
    protected function getFormResponse(Request $request): RedirectResponse
    {
        return $this->handleDeleteForm(
            $request,
            DeleteUser::class,
            UserIndex::getActionSlug()
        );
    }
}
