<?php

namespace Backend\Core\Engine;

use Backend\Core\Engine\Base\Config;
use Backend\Core\Engine\Base\AjaxAction as BaseAjaxAction;
use ForkCMS\App\KernelLoader;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * This class is the real code, it creates an action, loads the config file, ...
 */
final class AjaxAction extends KernelLoader
{
    /**
     * @var BaseAjaxAction
     */
    private $ajaxAction;

    public function display(): Response
    {
        $this->ajaxAction->execute();

        return $this->ajaxAction->getContent();
    }

    public function __construct(KernelInterface $kernel, string $module, string $action)
    {
        parent::__construct($kernel);

        $config = Config::forModule($kernel, $module);
        $actionClass = $config->getActionClass('ajax', $action);

        $this->ajaxAction = new $actionClass($this->getKernel());
    }
}
