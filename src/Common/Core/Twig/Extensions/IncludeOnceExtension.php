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
            new TwigFunction(
                'is_included',
                [$this, 'isIncluded']
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
        if ($this->isIncluded($template)) {
            return '';
        }

        $this->includedTemplates[$template] = true;

        return twig_include($env, $context, $template, $variables, $withContext, $ignoreMissing, $sandboxed);
    }

    public function isIncluded(string $template): bool
    {
        return array_key_exists($template, $this->includedTemplates);
    }
}
