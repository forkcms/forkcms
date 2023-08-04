<?php

namespace ForkCMS\Core\Domain\Application;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class ApplicationResolver implements ValueResolverInterface
{
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $argument->getType() === Application::class;
    }

    /** @return array<?Application> */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== Application::class) {
            return [];
        }

        if (!$argument->isNullable()) {
            return [Application::from(strtolower($request->attributes->get($argument->getName())))];
        }

        if ($request->attributes->has('application')) {
            return [Application::tryFrom(strtolower($request->attributes->get($argument->getName())))];
        }

        return [];
    }
}
