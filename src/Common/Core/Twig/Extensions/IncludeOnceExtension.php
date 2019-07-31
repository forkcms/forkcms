<?php

namespace Common\Core\Twig\Extensions;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class IncludeOnceExtension extends AbstractExtension
{
    /** @var array */
    private $includedTemplates = [];

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'include_once',
                [$this, 'includeOnce'],
                ['needs_environment' => true, 'needs_context' => true, 'is_safe' => ['all']]
            ),
        ];
    }

    public function includeOnce(
        Environment $env,
        array $context,
        string $template,
        array $variables = [],
        bool $withContext = true,
        bool $ignoreMissing = false,
        bool $sandboxed = false
    ): string {

        if (array_key_exists($template, $this->includedTemplates)) {
            return '';
        }

        $this->includedTemplates[$template] = true;

        return twig_include($env, $context, $template, $variables, $withContext, $ignoreMissing, $sandboxed);
    }
}
