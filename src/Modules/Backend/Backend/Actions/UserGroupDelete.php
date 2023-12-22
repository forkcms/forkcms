<?php

namespace ForkCMS\Modules\Backend\Backend\Actions;

use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessage;
use ForkCMS\Modules\Backend\Domain\Action\AbstractDeleteActionController;
use ForkCMS\Modules\Backend\Domain\UserGroup\Command\DeleteUserGroup;
use ForkCMS\Modules\Backend\Domain\UserGroup\UserGroup;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Delete user groups from the backend.
 */
final class UserGroupDelete extends AbstractDeleteActionController
{
    protected function getFormResponse(Request $request): RedirectResponse
    {
        $userGroup = $this->getEntityFromRequestOrNull($request, UserGroup::class, 'action.id');

        return $this->handleDeleteForm(
            $request,
            DeleteUserGroup::class,
            UserGroupIndex::getActionSlug(),
            successFlashMessage: FlashMessage::success('UserGroupDeleted', ['%userGroup%' => $userGroup?->getName()]),
        );
    }
}
