<?php

namespace ForkCMS\Modules\Backend\Backend\Widgets;

use ForkCMS\Modules\Backend\Domain\Widget\WidgetControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment as Twig;

final class UserStatistics implements WidgetControllerInterface
{
    public function __construct(private Twig $twig)
    {
    }

    public function __invoke(Request $request): string
    {
        return $this->twig->render('@Backend/Backend/Widgets/UserStatistics.html.twig');
    }
}
