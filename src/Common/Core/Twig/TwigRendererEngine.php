<?php

namespace Common\Core\Twig;

use Symfony\Bridge\Twig\Form\TwigRendererEngine as BridgeTwigRendererEngine;

final class TwigRendererEngine extends BridgeTwigRendererEngine
{
    public function overwriteDefaultThemes(array $defaultThemes): void
    {
        $this->defaultThemes = $defaultThemes;
    }

    public function getDefaultThemes(): array
    {
        return $this->defaultThemes;
    }
}
