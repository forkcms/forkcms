<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\Config;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Backend\Core\Engine\Base\Action as BackendBaseAction;
use Backend\Core\Language\Language as BackendLanguage;

/**
 * This class is the real code, it creates an action, loads the config file, ...
 */
class Action extends Base\Object
{
    /**
     * BackendTemplate
     *
     * @var TwigTemplate
     */
    public $tpl;

    /**
     * You have to specify the action and module so we know what to do with this instance
     *
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        parent::__construct($kernel);

        // grab stuff from the reference and store them in this object (for later/easy use)
        $this->tpl = $this->getContainer()->get('template');
    }

    /**
     * Execute the action
     * We will build the classname, require the class and call the execute method.
     */
    public function execute(): Response
    {
        // is the requested action available? If not we redirect to the error page.
        if (!$this->getConfig()->isActionAvailable($this->action)) {
            $this->get('url')->redirectToErrorPage('action-not-allowed', Response::HTTP_TEMPORARY_REDIRECT);
        }

        $actionClass = $this->buildActionClass();
        $this->assignWorkingLanguagesToTemplate();

        /** @var $action BackendBaseAction */
        $action = new $actionClass($this->getKernel());
        $this->getContainer()->get('logger')->info(
            "Executing backend action '{$action->getAction()}' for module '{$action->getModule()}'."
        );
        $action->execute();

        return $action->getContent();
    }

    private function buildActionClass(): string
    {
        $actionClass = 'Backend\\Modules\\' . $this->getModule() . '\\Actions\\' . $this->getAction();

        if (!class_exists($actionClass)) {
            throw new Exception('The class ' . $actionClass . ' could not be found.');
        }

        return $actionClass;
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
        $this->tpl->assign('workingLanguages', $workingLanguages);
    }

    /**
     * Load the config file for the requested module.
     * In the config file we have to find disabled actions, the constructor
     * will read the folder and set possible actions
     * Other configurations will be stored in it also.
     */
    public function getConfig(): Config
    {
        // check if we can load the config file
        $configClass = 'Backend\\Modules\\' . $this->getModule() . '\\Config';
        if ($this->getModule() === 'Core') {
            $configClass = Config::class;
        }

        // validate if class exists (aka has correct name)
        if (!class_exists($configClass)) {
            throw new Exception('The config file ' . $configClass . ' could not be found.');
        }

        // create config-object, the constructor will do some magic
        return new $configClass($this->getKernel(), $this->getModule());
    }
}
