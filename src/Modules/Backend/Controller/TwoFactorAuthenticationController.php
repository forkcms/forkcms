<?php

namespace ForkCMS\Modules\Backend\Controller;

use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleSettings;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
use Scheb\TwoFactorBundle\Controller\FormController;
use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorTokenInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\TwoFactorProviderRegistry;
use Scheb\TwoFactorBundle\Security\TwoFactor\Trusted\TrustedDeviceManagerInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\TwoFactorFirewallContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Logout\LogoutUrlGenerator;
use Symfony\Contracts\Translation\TranslatorInterface;

class TwoFactorAuthenticationController extends FormController
{
    public function __construct(
        TokenStorageInterface $tokenStorage,
        TwoFactorProviderRegistry $providerRegistry,
        TwoFactorFirewallContext $twoFactorFirewallContext,
        LogoutUrlGenerator $logoutUrlGenerator,
        ?TrustedDeviceManagerInterface $trustedDeviceManager,
        bool $trustedFeatureEnabled,
        private readonly TranslatorInterface $translator,
        private readonly ModuleSettings $moduleSettings,
    ) {
        parent::__construct(
            $tokenStorage,
            $providerRegistry,
            $twoFactorFirewallContext,
            $logoutUrlGenerator,
            $trustedDeviceManager,
            $trustedFeatureEnabled
        );
    }

    #[Route('/private/2fa', name: 'backend_2fa_login')]
    public function form(Request $request): Response
    {
        return parent::form($request);
    }

    protected function getTemplateVars(Request $request, TwoFactorTokenInterface $token): array
    {
        $templateVars = parent::getTemplateVars($request, $token);

        return array_merge(
            $templateVars,
            [
                'page_title' => $this->translator->trans(TranslationKey::label('2FA')),
                'displayTrustedOption' => $templateVars['displayTrustedOption'] && $this->moduleSettings->get(
                    ModuleName::fromString('Backend'),
                    'trusted_devices_enabled',
                    false
                ),
            ]
        );
    }
}
