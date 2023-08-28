<?php

namespace ForkCMS\Modules\Frontend\Controller;

use ForkCMS\Modules\Backend\Domain\AjaxAction\AjaxActionControllerInterface;
use ForkCMS\Modules\Backend\Domain\AjaxAction\AjaxActionSlug;
use InvalidArgumentException;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class FrontendAjaxController
{
    public function __construct(private readonly ServiceLocator $ajaxActions)
    {
    }

    public function __invoke(
        Request $request,
        AjaxActionSlug $actionSlug
    ): Response {
        try {
            $action = $this->ajaxActions->get($actionSlug->getFQCN());
        } catch (NotFoundExceptionInterface) {
            throw new InvalidArgumentException(
                sprintf(
                    'The ajax action class %s must be registered as a service and implement %s',
                    $actionSlug->getFQCN(),
                    AjaxActionControllerInterface::class
                )
            );
        }

        return $action($request);
    }
}
