<?php

namespace ForkCMS\Modules\OAuth\Controller;

use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleSettings;
use ForkCMS\Modules\OAuth\Domain\Authentication\AzureAuthenticator;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use TheNetworg\OAuth2\Client\Provider\Azure;

class AzureController
{
    public function __construct(
        private readonly Azure $azure,
        private readonly RouterInterface $router,
        private readonly RequestStack $requestStack,
        private readonly ModuleSettings $moduleSettings,
    ) {
    }

    #[Route('/private/connect/azure', name: 'connect_azure_start')]
    public function connectAction(): RedirectResponse {
        if (!$this->moduleSettings->get(ModuleName::fromString('OAuth'), 'enabled', false)) {
            return new RedirectResponse(
                $this->router->generate(
                    'backend_login',
                )
            );
        }

        /** @var OAuth2ClientInterface $client */
        $client = new OAuth2Client(
            $this->azure,
            $this->requestStack,
        );

        return $client
            ->redirect([
                    "openid",
                    "profile",
                    "email",
                    "offline_access",
                ]
            );
    }

    #[Route('/private/connect/azure/check', name: 'connect_azure_check')]
    public function connectCheckAction(AzureAuthenticator $authenticator, Request $request)
    {
    }
}
