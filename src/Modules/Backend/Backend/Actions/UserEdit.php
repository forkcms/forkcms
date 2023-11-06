<?php

namespace ForkCMS\Modules\Backend\Backend\Actions;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use ForkCMS\Core\Domain\Header\Breadcrumb\Breadcrumb;
use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessage;
use ForkCMS\Modules\Backend\Domain\Action\AbstractFormActionController;
use ForkCMS\Modules\Backend\Domain\Action\ActionServices;
use ForkCMS\Modules\Backend\Domain\User\Command\ChangeUser;
use ForkCMS\Modules\Backend\Domain\User\User;
use ForkCMS\Modules\Backend\Domain\User\UserType;
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
            formData: new ChangeUser($user, $this->displayQrCode($user)),
            redirectResponse: new RedirectResponse(UserIndex::getActionSlug()->generateRoute($this->router)),
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
            ->writer(new PngWriter())
            ->data($this->googleAuthenticator->getQRContent($user))
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(200)
            ->margin(0)
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->build()
            ->getDataUri();
    }
}
