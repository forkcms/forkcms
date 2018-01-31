<?php

namespace ForkCMS\Component\Module;

use Common\Locale;
use SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware;

final class CopyModulesToOtherLocale
{
    /**
     * @var MessageBusSupportingMiddleware
     */
    private $commandBus;

    /**
     * @var array
     */
    private $moduleCommands = [];

    public function __construct(MessageBusSupportingMiddleware $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function addModule(CopyModuleToOtherLocaleCommandInterface $moduleCommand): void
    {
        $this->moduleCommands[] = $moduleCommand;
    }

    public function copy(Locale $fromLocale, Locale $toLocale): void
    {
        $results = new CopyModulesToOtherLocaleResults();
        $moduleCommands = $this->getModuleCommandsOrderedByPriority();

        /**
         * @var CopyModuleToOtherLocaleCommandInterface $moduleCommand
         */
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
            function (CopyModuleToOtherLocaleCommandInterface $moduleCommand1, CopyModuleToOtherLocaleCommandInterface $moduleCommand2) {
                return $moduleCommand1->compare($moduleCommand2);
            }
        );

        return $moduleCommands;
    }
}
