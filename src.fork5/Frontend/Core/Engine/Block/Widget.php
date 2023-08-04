<?php

namespace Frontend\Core\Engine\Block;

use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtraRepository;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtraType;
use ForkCMS\App\KernelLoader;
use Frontend\Core\Engine\Base\Config;
use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Core\Engine\Exception as FrontendException;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\TwigTemplate;
use Frontend\Core\Engine\Url;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * This class will handle all stuff related to widgets
 */
class Widget extends KernelLoader implements ModuleExtraInterface
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
     * @var FrontendBaseWidget
     */
    private $object;

    /**
     * The block's output
     *
     * @var string
     */
    private $output;

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
        $actionClass = 'Frontend\\Modules\\' . $this->getModule() . '\\Widgets\\' . $this->getAction();
        if ($this->getModule() === 'Core') {
            $actionClass = 'Frontend\\Core\\Widgets\\' . $this->getAction();
        }

        // validate if class exists (aka has correct name)
        if (!class_exists($actionClass)) {
            throw new FrontendException('The action file ' . $actionClass . ' could not be found.');
        }
        // create action-object
        $this->object = new $actionClass($this->getKernel(), $this->getModule(), $this->getAction(), $this->getData());

        // validate if the execute-method is callable
        if (!is_callable([$this->object, 'execute'])) {
            throw new FrontendException('The action file should contain a callable method "execute".');
        }

        // call the execute method of the real action (defined in the module)
        $this->object->execute();
        $this->output = $this->render($this->getCustomTemplate());
    }

    /**
     * Get the current action
     * REMARK: You should not use this method from your code, but it has to be
     * public so we can access it later on in the core-code
     *
     * @return string
     */
    public function getAction(): string
    {
        // no action specified?
        if ($this->action === null) {
            $this->setAction($this->config->getDefaultAction());
        }

        // return action
        return $this->action;
    }

    public function getContent(): string
    {
        return $this->output;
    }

    /**
     * Get the block content
     *
     * @param string $template
     *
     * @return string
     */
    public function render(string $template = null): string
    {
        // set path to template if the widget didn't return any data
        if ($this->output === null) {
            return trim($this->object->getContent($template));
        }

        // return possible output
        return trim($this->output);
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    public function getCustomTemplate(): ?string
    {
        $data = @unserialize($this->data, ['allowed_classes' => false]);
        if (is_array($data) && array_key_exists('custom_template', $data)) {
            return $this->module . '/Layout/Widgets/' . $data['custom_template'];
        }

        return null;
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

    public function getTemplate(): TwigTemplate
    {
        return $this->object->getTemplate();
    }

    /**
     * Load the config file for the requested block.
     * In the config file we have to find disabled actions,
     * the constructor will read the folder and set possible actions
     * Other configurations will be stored in it also.
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
     * @param KernelInterface $kernel
     * @param string $module The module to load.
     * @param string $action The action to load.
     * @param int|null $id This is not the modules_extra id but the id of the item itself
     *
     * @return self
     */
    public static function getForId(
        KernelInterface $kernel,
        string $module,
        string $action,
        int $id = null
    ): self {
        $moduleExtraRepository = FrontendModel::getContainer()->get(ModuleExtraRepository::class);

        return new self(
            $kernel,
            $module,
            $action,
            $moduleExtraRepository->getModuleExtraDataByModuleAndActionAndItemId(
                ModuleExtraType::widget(),
                $module,
                $action,
                $id
            )
        );
    }

    public function getTemplatePath(): string
    {
        return $this->template->getPath($this->getCustomTemplate() ?? $this->object->templatePath ?? '');
    }
}
