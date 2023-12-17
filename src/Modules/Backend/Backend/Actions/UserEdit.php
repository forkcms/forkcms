<?php

namespace ForkCMS\Modules\Backend\Backend\Actions;

use ForkCMS\Core\Domain\Header\Breadcrumb\Breadcrumb;
use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessage;
use ForkCMS\Modules\Backend\Domain\Action\AbstractFormActionController;
use ForkCMS\Modules\Backend\Domain\User\Command\ChangeUser;
use ForkCMS\Modules\Backend\Domain\User\User;
use ForkCMS\Modules\Backend\Domain\User\UserType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Edit backend users.
 */
final class UserEdit extends AbstractFormActionController
{
    protected function getFormResponse(Request $request): ?Response
    {
        $user = $this->getEntityFromRequest($request, User::class);

        $this->assign('user', $user);
        $this->header->addBreadcrumb(new Breadcrumb($user->getDisplayName()));

        if ($this->getRepository(User::class)->count([]) > 1) {
            $this->addDeleteForm(['id' => $user->getId()], UserDelete::getActionSlug());
        }

        return $this->handleForm(
            request: $request,
            formType: UserType::class,
            formData: new ChangeUser($user),
            redirectResponse: new RedirectResponse(UserIndex::getActionSlug()->generateRoute($this->router)),
            successFlashMessageCallback: static function (FormInterface $form): FlashMessage {
                return FlashMessage::success('UserEdited', ['%user%' => $form->getData()->displayName]);
            }
        );
    }
}
