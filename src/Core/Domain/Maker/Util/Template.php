<?php

namespace ForkCMS\Core\Domain\Maker\Util;

final class Template
{
    public static function getPath(string $template): string
    {
        return 'src/Core/templates/Maker/' . $template;
    }
}
