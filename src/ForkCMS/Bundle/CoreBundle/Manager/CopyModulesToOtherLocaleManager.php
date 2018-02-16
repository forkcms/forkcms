<?php

namespace ForkCMS\Bundle\CoreBundle\Manager;

use Common\Locale;
use ForkCMS\Component\Module\CopyModulesToOtherLocaleResults;
use ForkCMS\Component\Module\CopyModuleToOtherLocaleInterface;
use SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware;

final class CopyModulesToOtherLocaleManager
{
    /** @var MessageBusSupportingMiddleware */
    private $commandBus;

    /** @var array */
    private $moduleCommands = [];

    public function __construct(MessageBusSupportingMiddleware $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function addModule(CopyModuleToOtherLocaleInterface $moduleCommand): void
    {
        $this->moduleCommands[] = $moduleCommand;
    }

    public function copy(Locale $fromLocale, Locale $toLocale): void
    {
        $results = new CopyModulesToOtherLocaleResults();
        $moduleCommands = $this->getModuleCommandsOrderedByPriority();

        /** @var CopyModuleToOtherLocaleInterface $moduleCommand */
        foreach ($moduleCommands as $moduleCommand) {
            // We set the previous results, so they are accessible.
            $moduleCommand->prepareForCopy($fromLocale, $toLocale, $results);

            // Execute the command
            $this->commandBus->handle($moduleCommand);

            // Save results to be accessible in future commands
            $results->add($moduleCommand->getModuleName(), $moduleCommand->getIdMap(), $moduleCommand->getExtraIdMap());
        }
    }

    private function getModuleCommandsOrderedByPriority(): array
    {
        $moduleCommands = $this->moduleCommands;

        usort(
            $moduleCommands,
            function (CopyModuleToOtherLocaleInterface $moduleCommand1, CopyModuleToOtherLocaleInterface $moduleCommand2) {
                return $moduleCommand1->comparePriority($moduleCommand2);
            }
        );

        return $moduleCommands;
    }
}
