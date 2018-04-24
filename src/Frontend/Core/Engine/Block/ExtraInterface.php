<?php

namespace Frontend\Core\Engine\Block;

use ForkCMS\App\KernelLoader;
use Frontend\Core\Engine\TwigTemplate;
use Frontend\Core\Engine\Url;
use Symfony\Component\HttpKernel\KernelInterface;
use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Base\Config;
use Frontend\Core\Engine\Exception as FrontendException;
use Frontend\Core\Language\Language as FL;

/**
 * This class will handle all stuff related to blocks
 */
class ExtraInterface extends KernelLoader implements ModuleExtraInterface
{
    /**
     * The current action
     *
     * @var string
     */
    private $action;

    /**
     * The config file
     *
     * @var Config
     */
    private $config;

    /**
     * The data that was passed by the extra
     *
     * @var mixed
     */
    private $data;

    /**
     * The current module
     *
     * @var string
     */
    private $module;

    /**
     * The extra object
     *
     * @var FrontendBaseBlock
     */
    private $object;

    /**
     * The block's output
     *
     * @var string
     */
    private $output;

    /**
     * Should the template overwrite the current one
     *
     * @var bool
     */
    protected $overwrite = false;

    /**
     * The path for the template
     *
     * @var string
     */
    protected $templatePath = '';

    /**
     * TwigTemplate instance
     *
     * @var TwigTemplate
     */
    protected $template;

    /**
     * URL instance
     *
     * @var Url
     */
    protected $url;

    public function __construct(KernelInterface $kernel, string $module, string $action = null, $data = null)
    {
        parent::__construct($kernel);

        // set properties
        $this->setModule($module);
        $this->setAction($action);
        $this->setData($data);
        $this->template = $this->getContainer()->get('templating');
        $this->url = $this->getContainer()->get('url');

        // load the config file for the required module
        $this->loadConfig();
    }

    public function execute(): void
    {
        // build action-class-name
        $actionClass = 'Frontend\\Modules\\' . $this->getModule() . '\\Actions\\' . $this->getAction();
        if ($this->getModule() === 'Core') {
            $actionClass = 'Frontend\\Core\\Actions\\' . $this->getAction();
        }

        // validate if class exists (aka has correct name)
        if (!class_exists($actionClass)) {
            throw new FrontendException('The action class couldn\'t be found: ' . $actionClass . '.');
        }

        // create action-object
        $this->object = new $actionClass($this->getKernel(), $this->getModule(), $this->getAction(), $this->getData());

        // validate if the execute-method is callable
        if (!is_callable([$this->object, 'execute'])) {
            throw new FrontendException('The action file should contain a callable method "execute".');
        }

        // call the execute method of the real action (defined in the module)
        $this->object->execute();

        // set some properties
        $this->setOverwrite($this->object->getOverwrite());
        if ($this->object->getTemplatePath() !== null) {
            $this->setTemplatePath($this->object->getTemplatePath());
        }
    }

    /**
     * Get the current action
     * REMARK: You should not use this method from your code, but it has to be
     * public so we can access it later on in the core-code
     * When the action is null the default action of the module will be used
     *
     * @return string|null
     */
    public function getAction(): ?string
    {
        if ($this->action !== null) {
            if (!\in_array($this->action, $this->config->getPossibleActions(), true)) {
                $this->setAction($this->config->getDefaultAction());
            }

            return $this->action;
        }

        // get first parameter
        $actionParameter = $this->url->getParameter(0);

        // unknown action and not provided in URL
        if ($actionParameter === null) {
            $this->setAction($this->config->getDefaultAction());

            return $this->action;
        }

        // action provided in the URL
        // loop possible actions
        $actionParameter = \SpoonFilter::toCamelCase($actionParameter);
        foreach ($this->config->getPossibleActions() as $actionName) {
            // get action that should be passed as parameter
            $actionUrl = \SpoonFilter::toCamelCase(
                rawurlencode(FL::act(\SpoonFilter::toCamelCase($actionName)))
            );

            // the action is the requested one
            if ($actionUrl === $actionParameter) {
                // set action
                $this->setAction($actionName);

                // stop the loop
                break;
            }
        }

        // we need this fallback when we add extra slugs but still need the default action
        if (!\in_array($this->action, $this->config->getPossibleActions(), true)) {
            $this->setAction($this->config->getDefaultAction());
        }

        return $this->action;
    }

    public function getContent(): string
    {
        // set path to template if the widget didn't return any data
        if ($this->output === null) {
            return trim($this->object->getContent());
        }

        // return possible output
        return trim($this->output);
    }

    /**
     * Get the data
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get the current module
     * REMARK: You should not use this method from your code, but it has to be
     * public so we can access it later on in the core-code
     *
     * @return string
     */
    public function getModule(): string
    {
        return $this->module;
    }

    /**
     * Get overwrite mode
     *
     * @return bool
     */
    public function getOverwrite(): bool
    {
        return $this->overwrite;
    }

    public function getTemplate(): TwigTemplate
    {
        return $this->object->getTemplate();
    }

    public function getTemplatePath(): string
    {
        return $this->templatePath;
    }

    /**
     * Get the assigned variables for this block.
     *
     * @return array
     */
    public function getVariables(): array
    {
        return (array) $this->template->getAssignedVariables();
    }

    /**
     * Load the config file for the requested block.
     * In the config file we have to find disabled actions, the constructor
     * will read the folder and set possible actions
     * Other configurations will also be stored in it.
     */
    public function loadConfig(): void
    {
        $configClass = 'Frontend\\Modules\\' . $this->getModule() . '\\Config';
        if ($this->getModule() === 'Core') {
            $configClass = 'Frontend\\Core\\Config';
        }

        // validate if class exists (aka has correct name)
        if (!class_exists($configClass)) {
            throw new FrontendException('The config file ' . $configClass . ' could not be found.');
        }

        // create config-object, the constructor will do some magic
        $this->config = new $configClass($this->getKernel(), $this->getModule());
    }

    private function setAction(string $action = null): void
    {
        $this->action = $action;
    }

    /**
     * Set the data
     *
     * @param mixed $data The data that should be set.
     */
    private function setData($data): void
    {
        $this->data = $data;
    }

    private function setModule(string $module): void
    {
        $this->module = $module;
    }

    /**
     * Set overwrite mode
     *
     * @param bool $overwrite Should the template overwrite the already loaded template.
     */
    private function setOverwrite(bool $overwrite): void
    {
        $this->overwrite = $overwrite;
    }

    private function setTemplatePath(string $path): void
    {
        $this->templatePath = $path;
    }
}
