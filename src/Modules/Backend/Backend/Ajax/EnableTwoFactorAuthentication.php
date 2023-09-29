<?php

namespace ForkCMS\Modules\Backend\Backend\Ajax;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\SvgWriter;
use ForkCMS\Modules\Backend\Domain\AjaxAction\AbstractAjaxActionController;
use ForkCMS\Modules\Backend\Domain\User\User;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class EnableTwoFactorAuthentication extends AbstractAjaxActionController
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly GoogleAuthenticatorInterface $googleAuthenticator,
    ) {
    }

    protected function execute(Request $request): void
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser()->getId();
        $secret = $this->googleAuthenticator->generateSecret();

        $this->assign('user', $this->tokenStorage->getToken()->getUser()->getId());
        $this->assign('secret', $secret);
        // Set the secret in the user to generate a valid QR code
        $user->setGoogleAuthenticatorSecret($secret);
        $this->assign('qrCode', $this->displayQrCode($user));
        // Clear the secret after generating the QR code
        $user->setGoogleAuthenticatorSecret(null);
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
