<?php

namespace ForkCMS\Core\Domain\Twig;

use ForkCMS\Modules\Backend\Domain\Action\ActionName;
use ForkCMS\Modules\Backend\Domain\Action\ActionSlug;
use ForkCMS\Modules\Backend\Domain\Action\ModuleAction;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class SecurityExtension extends AbstractExtension
{
    public function __construct(
        private RequestStack $requestStack,
        private AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'is_allowed',
                [$this, 'isAllowedAction']
            ),
        ];
    }

    public function isAllowedAction(string $actionName = null, string $moduleName = null): bool
    {
        return $this->authorizationChecker->isGranted($this->getRole($moduleName, $actionName));
    }

    private function getRole(?string $moduleName, ?string $actionName): string
    {
        $defaultSlug = ActionSlug::fromRequest($this->requestStack->getMainRequest());
        if ($moduleName === null && $actionName === null) {
            $defaultSlug->asModuleAction()->asRole();
        }

        if ($moduleName === null) {
            $moduleName = $defaultSlug->getModuleName()->getName();
        }
        if ($actionName === null) {
            $actionName = $defaultSlug->getActionName()->getName();
        }

        return (new ModuleAction(
            ModuleName::fromString($moduleName),
            ActionName::fromString($actionName)
        ))->asRole();
    }
}
