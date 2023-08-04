<?php

namespace ForkCMS\Core\Domain\Twig;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Extension\AbstractExtension;
use Twig\Extension\SandboxExtension;
use Twig\TwigFunction;

final class IncludeOnceExtension extends AbstractExtension
{
    /** @var array<string, bool> */
    private array $includedTemplates = [];

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

    /**
     * @param array<string, mixed> $context
     * @param array<string, mixed> $variables
     */
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

        $alreadySandboxed = false;
        $sandbox = null;
        $output = '';
        $isSandboxed = $sandboxed && $env->hasExtension(SandboxExtension::class);

        if ($withContext) {
            $variables = array_merge($context, $variables);
        }

        if ($isSandboxed) {
            /** @var SandboxExtension $sandbox */
            $sandbox = $env->getExtension(SandboxExtension::class);
            if (!$alreadySandboxed = $sandbox->isSandboxed()) {
                $sandbox->enableSandbox();
            }
        }

        try {
            try {
                $loaded = $env->resolveTemplate($template);
                $output = $loaded->render($variables);
            } catch (LoaderError $e) {
                if (!$ignoreMissing) {
                    throw $e;
                }
            }
        } finally {
            if ($isSandboxed && !$alreadySandboxed) {
                $sandbox->disableSandbox();
            }
        }

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
