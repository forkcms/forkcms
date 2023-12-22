<?php

namespace ForkCMS\Modules\Backend\Backend\Actions;

use ForkCMS\Core\Domain\Header\Breadcrumb\Breadcrumb;
use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessage;
use ForkCMS\Modules\Backend\Domain\Action\AbstractFormActionController;
use ForkCMS\Modules\Backend\Domain\Action\ActionServices;
use ForkCMS\Modules\Backend\Domain\User\Command\ChangeUser;
use ForkCMS\Modules\Backend\Domain\User\User;
use ForkCMS\Modules\Backend\Domain\User\UserType;
use \ForkCMS\Modules\Extensions\Domain\Module\ModuleSettings;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Edit backend users.
 */
final class UserEdit extends AbstractFormActionController
{
    private ModuleSettings $moduleSettings;

    public function __construct(
        private readonly ActionServices $actionServices,
    ) {
        parent::__construct($actionServices);
        $this->moduleSettings = $this->actionServices->moduleSettings;
    }

    public function execute(Request $request): void
    {
        parent::execute($request);

        if ($request->query->has('2fa-enabled')) {
            $this->header->addFlashMessage(FlashMessage::success('2FAEnabled'));
        }

        $backupCodes = null;
        if ($request->getSession()->has('showBackupCodes')) {
            $request->getSession()->remove('showBackupCodes');
            $user = $this->getEntityFromRequest($request, User::class);
            $backupCodes = $user->getBackupCodes();
        }

        $this->assign('backup_codes', $backupCodes);
    }

    protected function getFormResponse(Request $request): ?Response
    {
        $user = $this->getEntityFromRequest($request, User::class);

        $this->assign('user', $user);
        $this->assign('two_factor_authentication_enabled', $this->moduleSettings->get($this->getModuleName(), '2fa_enabled', false));
        $this->assign('two_factor_authentication_required', $this->moduleSettings->get($this->getModuleName(), '2fa_required', false));
        $this->assign('user_has_2_factor_authentication_enabled', $user->getGoogleAuthenticatorSecret() !== null);

        $this->header->addBreadcrumb(new Breadcrumb($user->getDisplayName()));

        if ($this->getRepository(User::class)->count([]) > 1) {
            $this->addDeleteForm(['id' => $user->getId()], UserDelete::getActionSlug());
        }

        return $this->handleForm(
            request: $request,
            formType: UserType::class,
            formData: new ChangeUser($user),
            validCallback: function (FormInterface $form) use ($request): Response {
                $this->commandBus->dispatch($form->getData());

                if ($form->getData()->enableTwoFactorAuthentication) {
                    $request->getSession()->set('showBackupCodes', true);

                    return new RedirectResponse(
                        self::getActionSlug()->generateRoute(
                            $this->router,
                            ['slug' => $form->getData()->getEntity()->getId()]
                        )
                    );
                }

                return new RedirectResponse(UserIndex::getActionSlug()->generateRoute($this->router));
            },
            successFlashMessageCallback: static function (FormInterface $form): FlashMessage {
                return FlashMessage::success('UserEdited', ['%user%' => $form->getData()->displayName]);
            }
        );
    }
}
