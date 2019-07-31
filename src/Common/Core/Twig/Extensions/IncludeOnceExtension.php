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
            new TwigFunction(
                'set_included',
                [$this, 'setIncluded']
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

        $output = twig_include($env, $context, $template, $variables, $withContext, $ignoreMissing, $sandboxed);

        // this needs to happen after we capture the output so we can check if it was previously included
        $this->setIncluded($template);

        return $output;
    }

    public function isIncluded(string $template): bool
    {
        return $this->includedTemplates[$template] ?? false;
    }

    public function setIncluded(string $template, bool $isIncluded = true): void
    {
        $this->includedTemplates[$template] = $isIncluded;
    }
}
