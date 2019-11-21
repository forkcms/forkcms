<?php

namespace ForkCMS\Utility\Module\CopyContentToOtherLocale;

use Backend\Modules\Pages\Domain\Page\Page;
use Common\Locale;
use SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware;

final class CopyContentFromModulesToOtherLocaleManager
{
    /** @var MessageBusSupportingMiddleware */
    private $commandBus;

    /** @var array|CopyModuleContentToOtherLocaleInterface[] */
    private $copyContentFromModulesToOtherLocaleCommands = [];

    public function __construct(MessageBusSupportingMiddleware $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function addModule(CopyModuleContentToOtherLocaleInterface $moduleCommand): void
    {
        $this->copyContentFromModulesToOtherLocaleCommands[] = $moduleCommand;
    }

    public function copyOne(Page $pageToCopy, Locale $fromLocale, Locale $toLocale): void
    {
        $this->copy($fromLocale, $toLocale, $pageToCopy);
    }

    public function copyAll(Locale $fromLocale, Locale $toLocale): void
    {
        $this->copy($fromLocale, $toLocale, null);
    }

    private function copy(Locale $fromLocale, Locale $toLocale, ?Page $pageToCopy): void
    {
        $results = new Results();
        $copyContentFromModulesToOtherLocaleCommands = $this->getModuleCommandsOrderedByPriority();

        /** @var CopyModuleContentToOtherLocaleInterface $moduleCommand */
        foreach ($copyContentFromModulesToOtherLocaleCommands as $moduleCommand) {
            // We set the previous results, so they are accessible.
            $moduleCommand->prepareForCopy($fromLocale, $toLocale, $pageToCopy, $results);

            // Execute the command
            $this->commandBus->handle($moduleCommand);

            // Save results to be accessible in future commands
            $results->add(
                $moduleCommand->getModuleName(),
                $moduleCommand->getIdMap(),
                $moduleCommand->getModuleExtraIdMap()
            );
        }
    }

    private function getModuleCommandsOrderedByPriority(): array
    {
        $copyContentFromModulesToOtherLocaleCommands = $this->copyContentFromModulesToOtherLocaleCommands;

        usort(
            $copyContentFromModulesToOtherLocaleCommands,
            static function (
                CopyModuleContentToOtherLocaleInterface $moduleCommand1,
                CopyModuleContentToOtherLocaleInterface $moduleCommand2
            ) {
                return $moduleCommand1->getPriority() <=> $moduleCommand2->getPriority();
            }
        );

        return $copyContentFromModulesToOtherLocaleCommands;
    }
}
