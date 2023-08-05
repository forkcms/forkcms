<?php

namespace ForkCMS\Modules\Backend\Backend\Actions;

use ForkCMS\Modules\Backend\Domain\Action\AbstractDeleteActionController;
use ForkCMS\Modules\Backend\Domain\UserGroup\Command\DeleteUserGroup;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Delete user groups from the backend.
 */
final class UserGroupDelete extends AbstractDeleteActionController
{
    protected function getFormResponse(Request $request): RedirectResponse
    {
        return $this->handleDeleteForm(
            $request,
            DeleteUserGroup::class,
            UserGroupIndex::getActionSlug()
        );
    }
}
