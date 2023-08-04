<?php

namespace ForkCMS\Modules\Extensions\Domain\Module;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class ModuleNameResolver implements ValueResolverInterface
{
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $argument->getType() === ModuleName::class;
    }

    /** @return array<ModuleName> */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== ModuleName::class) {
            return [];
        }
        if ($argument->isNullable() && !$request->attributes->has($argument->getName())) {
            return [];
        }

        return [ModuleName::fromString($request->attributes->get($argument->getName()))];
    }
}
