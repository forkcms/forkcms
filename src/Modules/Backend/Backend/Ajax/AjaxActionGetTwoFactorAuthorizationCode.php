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

class AjaxActionGetTwoFactorAuthorizationCode extends AbstractAjaxActionController
{
    public function __construct(
        private readonly GoogleAuthenticatorInterface $googleAuthenticator,
        private readonly TokenStorageInterface $tokenStorage
    ) {
    }

    public function execute(Request $request): void
    {
        $user = $this->tokenStorage->getToken()->getUser();

        if (!$user instanceof User) {
            $this->assign('error', 'User not found');

            return;
        }

        $secret = $this->googleAuthenticator->generateSecret();
        $user->setGoogleAuthenticatorSecret($secret);
        $code = $this->googleAuthenticator->getQRContent($user);
        $this->assign('secret', $secret);
        $this->assign('code', $code);

        $qrCode = Builder::create()
            ->writer(new SvgWriter())
            ->data($code)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->writerOptions([SvgWriter::WRITER_OPTION_EXCLUDE_SVG_WIDTH_AND_HEIGHT => true])
            ->build()
            ->getDataUri();

        $this->assign('qrCode', $qrCode);

        // Reset the secret to prevent false updates
        $user->setGoogleAuthenticatorSecret(null);
    }
}
