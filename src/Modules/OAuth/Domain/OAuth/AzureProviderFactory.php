<?php

namespace ForkCMS\Modules\OAuth\Domain\OAuth;

use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleSettings;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use TheNetworg\OAuth2\Client\Provider\Azure;

class AzureProviderFactory
{
    public function __construct(
        private readonly ModuleSettings $moduleSettings,
        private readonly RouterInterface $router,
    ) {
    }

    public function create(): Azure
    {
        return new Azure(
            [
                'clientId' => $this->moduleSettings->get(ModuleName::fromString('OAuth'), 'client_id'),
                'clientSecret' => $this->moduleSettings->get(ModuleName::fromString('OAuth'), 'client_secret'),
                'tenant'=> $this->moduleSettings->get(ModuleName::fromString('OAuth'), 'tenant'),
                'redirectUri'=> $this->router->generate(
                    'connect_azure_check',
                    [],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                'defaultEndPointVersion' => '2.0',
            ]
        );
    }
}
