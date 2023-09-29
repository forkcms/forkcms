<?php

namespace ForkCMS\Modules\Backend\Backend\Actions;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\SvgWriter;
use ForkCMS\Core\Domain\Header\Breadcrumb\Breadcrumb;
use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessage;
use ForkCMS\Modules\Backend\Domain\Action\AbstractFormActionController;
use ForkCMS\Modules\Backend\Domain\Action\ActionServices;
use ForkCMS\Modules\Backend\Domain\User\Command\ChangeUser;
use ForkCMS\Modules\Backend\Domain\User\User;
use ForkCMS\Modules\Backend\Domain\User\UserType;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Edit backend users.
 */
final class UserEdit extends AbstractFormActionController
{
    public function __construct(
        ActionServices $services,
        private readonly GoogleAuthenticatorInterface $googleAuthenticator,
    ) {
        parent::__construct($services);
    }

    public function execute(Request $request): void
    {
        parent::execute($request);

        $backupCodes = null;
        if ($request->getSession()->has('showBackupCodes')) {
            $request->getSession()->remove('showBackupCodes');
            $user = $this->getEntityFromRequest($request, User::class);
            $backupCodes = $user->getBackupCodes();
        }

        $this->assign('backupCodes', $backupCodes);
    }

    protected function getFormResponse(Request $request): ?Response
    {
        $user = $this->getEntityFromRequest($request, User::class);

        $this->assign('user', $user);
        $this->assign('twoFAEnabled', $this->moduleSettings->get($this->getModuleName(), '2fa_enabled', false));
        $this->assign('twoFARequired', $this->moduleSettings->get($this->getModuleName(), '2fa_required', false));
        $this->assign('userHas2FAEnabled', $user->getGoogleAuthenticatorSecret() !== null);

        $this->header->addBreadcrumb(new Breadcrumb($user->getDisplayName()));

        if ($this->getRepository(User::class)->count([]) > 1) {
            $this->addDeleteForm(['id' => $user->getId()], UserDelete::getActionSlug());
        }

        return $this->handleForm(
            request: $request,
            formType: UserType::class,
            formData: new ChangeUser($user, $this->displayQrCode($user)),
            validCallback: function (FormInterface $form) use ($request): Response {
                $redirectResponse = new RedirectResponse(UserIndex::getActionSlug()->generateRoute($this->router));
                $this->commandBus->dispatch($form->getData());

                if ($form->getData()->enableTwoFactorAuthentication) {
                    $request->getSession()->set('showBackupCodes', true);

                    return new RedirectResponse(self::getActionSlug()->generateRoute($this->router) . '/' . $form->getData()->getEntity()->getId());
                }

                return $redirectResponse;
            },
            flashMessageCallback: static function (FormInterface $form): FlashMessage {
                return FlashMessage::success('UserEdited', ['%user%' => $form->getData()->displayName]);
            }
        );
    }

    private function displayQrCode(User $user): ?string
    {
        if ($user->getGoogleAuthenticatorSecret() === null) {
            return null;
        }

        return Builder::create()
            ->writer(new SvgWriter())
            ->data($this->googleAuthenticator->getQRContent($user))
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->writerOptions([SvgWriter::WRITER_OPTION_EXCLUDE_SVG_WIDTH_AND_HEIGHT => true])
            ->build()
            ->getDataUri();
    }
}
