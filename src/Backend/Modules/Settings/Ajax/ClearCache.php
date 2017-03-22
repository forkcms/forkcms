<?php

namespace Backend\Modules\Settings\Ajax;

use Backend\Core\Engine\Base\AjaxAction;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

class ClearCache extends AjaxAction
{
    public function execute()
    {
        parent::execute();

        $kernel = $this->getKernel();
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(
            [
                'command' => 'forkcms:cache:clear'
            ]
        );

        $exitCode = $application->run($input);

        $this->output(
            self::OK,
            [
                'exitCode' => $exitCode,
            ]
        );
    }
}
