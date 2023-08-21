<?php

namespace ForkCMS\Modules\Frontend\Domain\Block;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface BlockControllerInterface
{
    /** @return string|array<string, mixed> */
    public function __invoke(Request $request, Response $response, Block $block): string|array;

    public function getResponseOverride(): ?Response;

    public static function getModuleBlock(): ModuleBlock;

    public function assign(string $key, mixed $value): void;
}
