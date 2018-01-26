<?php

namespace Backend\Core\Engine;

use Backend\Core\Engine\Base\Config;
use ForkCMS\App\KernelLoader;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Backend\Core\Engine\Base\Action as BackendBaseAction;
use Backend\Core\Language\Language as BackendLanguage;

/**
 * This class is the real code, it creates an action, loads the config file, ...
 */
class Action extends KernelLoader
{
    /**
     * BackendTemplate
     *
     * @var TwigTemplate
     */
    private $template;

    /**
     * @var Config
     */
    private $config;

    /**
     * You have to specify the action and module so we know what to do with this instance
     *
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        parent::__construct($kernel);

        // grab stuff from the reference and store them in this object (for later/easy use)
        $this->template = $this->getContainer()->get('template');

        $this->config = Config::forModule($kernel, $this->get('url')->getModule());
    }

    /**
     * Execute the action
     * We will build the classname, require the class and call the execute method.
     */
    public function execute(): Response
    {
        // is the requested action available? If not we redirect to the error page.
        if (!$this->config->isActionAvailable($this->get('url')->getAction())) {
            $this->get('url')->redirectToErrorPage('action-not-allowed', Response::HTTP_TEMPORARY_REDIRECT);
        }

        $actionClass = $this->config->getActionClass('actions', $this->get('url')->getAction());
        $this->assignWorkingLanguagesToTemplate();

        /** @var $action BackendBaseAction */
        $action = new $actionClass($this->getKernel());
        $this->getContainer()->get('logger.public')->info(
            "Executing backend action '{$action->getAction()}' for module '{$action->getModule()}'."
        );
        $action->execute();

        return $action->getContent();
    }

    private function assignWorkingLanguagesToTemplate(): void
    {
        // get working languages
        $languages = BackendLanguage::getWorkingLanguages();
        $workingLanguages = [];

        // loop languages and build an array that we can assign
        foreach ($languages as $abbreviation => $label) {
            $workingLanguages[] = [
                'abbr' => $abbreviation,
                'label' => $label,
                'selected' => $abbreviation === BackendLanguage::getWorkingLanguage(),
            ];
        }

        // assign the languages
        $this->template->assign('workingLanguages', $workingLanguages);
    }
}
