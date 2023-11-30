<?php

namespace Common\Core\Twig;

use Common\Core\Form;
use Common\Core\Model;
use Common\ModulesSettings;
use SpoonForm;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Twig\Environment;

/**
 * This is a twig template wrapper
 * that glues spoon libraries and code standards with twig.
 */
abstract class BaseTwigTemplate extends TwigEngine
{
    /**
     * @var string
     */
    protected $language;

    /**
     * Should we add slashes to each value?
     *
     * @var bool
     */
    protected $addSlashes = false;

    /**
     * Debug mode.
     *
     * @var bool
     */
    protected $debugMode = false;

    /**
     * List of form objects.
     *
     * @var Form[]
     */
    protected $forms = [];

    /**
     * List of assigned variables.
     *
     * @var array
     */
    protected $variables = [];

    /**
     * @var ModulesSettings
     */
    protected $forkSettings;

    /**
     * List of globals that have been assigned at runtime
     *
     * @var array
     */
    protected $runtimeGlobals = [];

    public function assign(string $key, $values): void
    {
        $this->variables[$key] = $values;
    }

    public function assignGlobal(string $key, $value): void
    {
        $this->runtimeGlobals[$key] = $value;
    }

    /**
     * Assign an entire array with keys & values.
     *
     * @param array $variables This array with keys and values will be used to search and replace in the template file.
     * @param string|null $index
     */
    public function assignArray(array $variables, string $index = null): void
    {
        // artifacts?
        if (!empty($index) && isset($variables['Core'])) {
            unset($variables['Core']);
            $variables = [$index => $variables];
        }

        // merge the variables array_merge might be to slow for bigger sites
        // as array_merge tend to slow down at +100 keys
        foreach ($variables as $key => $val) {
            $this->variables[$key] = $val;
        }
    }

    public function addForm(SpoonForm $form): void
    {
        $this->forms[$form->getName()] = $form;
    }

    /**
     * Retrieves the already assigned variables.
     *
     * @return array
     */
    public function getAssignedVariables(): array
    {
        return $this->variables;
    }

    /** @todo Refactor out constants #1106
     * We need to deprecate this asap
     *
     * @param Environment $twig
     */
    protected function startGlobals(Environment $twig)
    {
        // get all defined constants
        $constants = get_defined_constants(true);

        /* Setup Backend for the Twig environment. */
        if (!$this->forkSettings || !Model::getContainer()->getParameter('fork.is_installed')) {
            return;
        }

        $twig->addGlobal('timeFormat', $this->forkSettings->get('Core', 'time_format'));
        $twig->addGlobal('dateFormatShort', $this->forkSettings->get('Core', 'date_format_short'));
        $twig->addGlobal('dateFormatLong', $this->forkSettings->get('Core', 'date_format_long'));

        // old theme checker
        if ($this->forkSettings->get('Core', 'theme') !== null) {
            $twig->addGlobal('THEME', $this->forkSettings->get('Core', 'theme', 'Fork'));
            $themePath = '/src/Frontend/Themes/'.$this->forkSettings->get('Core', 'theme', 'Fork');
            $twig->addGlobal(
                'THEME_URL',
                $themePath
            );

            $rootDir = __DIR__ . '/../../../../';

            if (file_exists(realpath($rootDir . $themePath. '/apple-touch-icon.png'))) {
                $twig->addGlobal(
                    'THEME_ICON',
                    $themePath . '/apple-touch-icon.png'
                );
            } else {
                $twig->addGlobal(
                    'THEME_ICON',
                    '/apple-touch-icon.png'
                );
            }
        }
    }

    /**
     * Should we execute addSlashed on the locale?
     *
     * @param bool $enabled Enable addslashes.
     */
    public function setAddSlashes(bool $enabled = true): void
    {
        $this->addSlashes = $enabled;
    }

    public function render($template, array $variables = []): string
    {
        if (!empty($this->forms)) {
        }

        return $this->environment->render($template, array_merge($this->runtimeGlobals, $variables));
    }
}
