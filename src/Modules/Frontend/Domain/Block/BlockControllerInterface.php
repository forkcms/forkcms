<?php

namespace ForkCMS\Modules\Frontend\Domain\Block;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface BlockControllerInterface
{
    public function __invoke(Request $request, Response $response, Block $block): string|array;

    public function getResponseOverride(): ?Response;

    public static function getModuleBlock(): ModuleBlock;
}
