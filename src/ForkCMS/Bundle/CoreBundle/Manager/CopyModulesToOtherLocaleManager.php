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
    private $copyModuleToOtherLocaleCommands = [];

    public function __construct(MessageBusSupportingMiddleware $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function addModule(CopyModuleToOtherLocaleInterface $moduleCommand): void
    {
        $this->copyModuleToOtherLocaleCommands[] = $moduleCommand;
    }

    public function copy(Locale $fromLocale, Locale $toLocale): void
    {
        $results = new CopyModulesToOtherLocaleResults();
        $copyModuleToOtherLocaleCommands = $this->getModuleCommandsOrderedByPriority();

        /** @var CopyModuleToOtherLocaleInterface $moduleCommand */
        foreach ($copyModuleToOtherLocaleCommands as $moduleCommand) {
            // We set the previous results, so they are accessible.
            $moduleCommand->prepareForCopy($fromLocale, $toLocale, $results);

            // Execute the command
            $this->commandBus->handle($moduleCommand);

            // Save results to be accessible in future commands
            $results->add($moduleCommand->getModuleName(), $moduleCommand->getIdMap(), $moduleCommand->getModuleExtraIdMap());
        }
    }

    private function getModuleCommandsOrderedByPriority(): array
    {
        $copyModuleToOtherLocaleCommands = $this->copyModuleToOtherLocaleCommands;

        usort(
            $copyModuleToOtherLocaleCommands,
            function (CopyModuleToOtherLocaleInterface $moduleCommand1, CopyModuleToOtherLocaleInterface $moduleCommand2) {
                return $moduleCommand1->getPriority() > $moduleCommand2->getPriority();
            }
        );

        return $copyModuleToOtherLocaleCommands;
    }
}
