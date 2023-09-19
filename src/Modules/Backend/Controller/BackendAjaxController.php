<?php

namespace ForkCMS\Modules\Backend\Controller;

use ForkCMS\Modules\Backend\Backend\Ajax\NotFound;
use ForkCMS\Modules\Backend\Domain\AjaxAction\AjaxActionControllerInterface;
use ForkCMS\Modules\Backend\Domain\AjaxAction\AjaxActionSlug;
use InvalidArgumentException;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class BackendAjaxController
{
    /** @param ServiceLocator<AjaxActionControllerInterface> $ajaxActions */
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

        try {
            return $action($request);
        } catch (NotFoundHttpException) {
            return $this->ajaxActions->get(NotFound::class)($request);
        }
    }
}
