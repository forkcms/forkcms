<?php

namespace Frontend\Core\Engine;

use Common\Core\Twig\BaseTwigTemplate;
use Common\Core\Twig\Extensions\TwigFilters;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Bridge\Twig\Extension\FormExtension as SymfonyFormExtension;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Twig_Environment;
use Twig_FactoryRuntimeLoader;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a twig template wrapper
 * that glues spoon libraries and code standards with twig
 */
class TwigTemplate extends BaseTwigTemplate
{
    /**
     * @var string
     */
    private $themePath;

    public function __construct(
        Twig_Environment $environment,
        TemplateNameParserInterface $parser,
        FileLocatorInterface $locator
    ) {
        parent::__construct($environment, $parser, $locator);

        $container = Model::getContainer();
        $this->forkSettings = $container->get('fork.settings');
        $this->debugMode = $container->getParameter('kernel.debug');
        $this->environment->disableStrictVariables();
        new FormExtension($this->environment);
        TwigFilters::addFilters($this->environment, 'Frontend');
        $this->startGlobals($this->environment);

        if (!$container->getParameter('fork.is_installed')) {
            return;
        }

        $this->addFrontendPathsToTheTemplateLoader($this->forkSettings->get('Core', 'theme', 'Fork'));
        $this->connectSymfonyForms();
    }

    private function addFrontendPathsToTheTemplateLoader(string $theme): void
    {
        $this->themePath = FRONTEND_PATH . '/Themes/' . $theme;
        $this->environment->setLoader(
            new \Twig_Loader_Chain(
                [$this->environment->getLoader(), new \Twig_Loader_Filesystem($this->getLoadingFolders())]
            )
        );
    }

    private function connectSymfonyForms(): void
    {
        $rendererEngine = new TwigRendererEngine($this->getFormTemplates('FormLayout.html.twig'), $this->environment);
        $csrfTokenManager = Model::get('security.csrf.token_manager');
        $this->environment->addRuntimeLoader(
            new Twig_FactoryRuntimeLoader(
                [
                    TwigRenderer::class => function () use ($rendererEngine, $csrfTokenManager): TwigRenderer {
                        return new TwigRenderer($rendererEngine, $csrfTokenManager);
                    },
                ]
            )
        );

        if (!$this->environment->hasExtension(SymfonyFormExtension::class)) {
            $this->environment->addExtension(new SymfonyFormExtension());
        }
    }

    /**
     * Convert a filename extension
     *
     * @param string $template
     *
     * @return string
     */
    public function getPath(string $template): string
    {
        if (strpos($template, FRONTEND_MODULES_PATH) !== false) {
            return str_replace(FRONTEND_MODULES_PATH . '/', '', $template);
        }

        // else it's in the theme folder
        return str_replace($this->themePath . '/', '', $template);
    }

    /**
     * Fetch the parsed content from this template.
     *
     * @param string $template The location of the template file, used to display this template.
     *
     * @return string The actual parsed content after executing this template.
     */
    public function getContent(string $template): string
    {
        $template = $this->getPath($template);

        $content = $this->render(
            $template,
            $this->variables
        );

        $this->variables = [];

        return $content;
    }

    private function getLoadingFolders(): array
    {
        return $this->filterOutNonExistingPaths(
            [
                $this->themePath . '/Modules',
                $this->themePath,
                FRONTEND_MODULES_PATH,
                FRONTEND_PATH,
            ]
        );
    }

    private function getFormTemplates(string $fileName): array
    {
        return $this->filterOutNonExistingPaths(
            [
                FRONTEND_PATH . '/Core/Layout/Templates/' . $fileName,
                $this->themePath . '/Core/Layout/Templates/' . $fileName,
            ]
        );
    }

    private function filterOutNonExistingPaths(array $files): array
    {
        $filesystem = new Filesystem();

        return array_filter(
            $files,
            function ($folder) use ($filesystem) {
                return $filesystem->exists($folder);
            }
        );
    }
}
