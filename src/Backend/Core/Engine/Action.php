<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

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
            return $this->redirectToErrorPage('action-not-allowed', 307);
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
}
