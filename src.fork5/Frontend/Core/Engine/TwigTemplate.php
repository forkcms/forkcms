<?php

namespace Frontend\Core\Engine;

use Frontend\Core\Language\Locale;
use Common\Core\Twig\BaseTwigTemplate;
use Common\Core\Twig\Extensions\TwigFilters;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Bridge\Twig\Extension\FormExtension as SymfonyFormExtension;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Twig\Environment;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\FactoryRuntimeLoader;

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
        Environment $environment,
        TemplateNameParserInterface $parser,
        FileLocatorInterface $locator
    ) {
        $container = Model::getContainer();
        $this->forkSettings = $container->get('fork.settings');
        $this->language = Locale::frontendLanguage();

        parent::__construct($environment, $parser, $locator);

        $this->debugMode = $container->getParameter('kernel.debug');
        if ($this->debugMode) {
            $this->environment->enableAutoReload();
            $this->environment->setCache(false);
        }
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
            new ChainLoader(
                [$this->environment->getLoader(), new FilesystemLoader($this->getLoadingFolders())]
            )
        );
    }

    private function connectSymfonyForms(): void
    {
        $rendererEngine = new TwigRendererEngine($this->getFormTemplates('FormLayout.html.twig'), $this->environment);
        $csrfTokenManager = Model::get('security.csrf.token_manager');
        $this->environment->addRuntimeLoader(
            new FactoryRuntimeLoader(
                [
                    FormRenderer::class => function () use ($rendererEngine, $csrfTokenManager): FormRenderer {
                        return new FormRenderer($rendererEngine, $csrfTokenManager);
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
