<?php

namespace ForkCMS\Modules\Frontend\Domain\Block;

use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

interface BlockRouterInterface
{
    public function getRouteForBlock(
        ModuleBlock $moduleBlock,
        Locale $locale = null,
        array $parameters = [],
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ): string;
}
