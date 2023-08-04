<?php

namespace ForkCMS\Modules\Backend\Domain\Widget;

use Symfony\Component\HttpFoundation\Request;

interface WidgetControllerInterface
{
    public function __invoke(Request $request): string;
}
