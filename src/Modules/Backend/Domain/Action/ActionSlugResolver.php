<?php

namespace ForkCMS\Modules\Backend\Domain\Action;

use ForkCMS\Modules\Backend\Backend\Actions\Forbidden;
use ForkCMS\Modules\Backend\Backend\Actions\NotFound;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class ActionSlugResolver implements ValueResolverInterface
{
    public function __construct(private AuthorizationCheckerInterface $authorizationChecker)
    {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $argument->getType() === ActionSlug::class;
    }

    /** @return array<?ActionSlug> */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== ActionSlug::class) {
            return [];
        }

        $actionSlug = ActionSlug::fromRequest($request);
        if (!$this->authorizationChecker->isGranted($actionSlug->asModuleAction()->asRole())) {
            return [Forbidden::getActionSlug()];
        }

        return [$actionSlug];
    }
}
