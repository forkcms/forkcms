<?php

namespace Backend\Modules\Location\Command;

use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtra;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtraRepository;
use SpoonDatabase;

final class CopyLocationWidgetsToOtherLocaleHandler
{
    /** @var ModuleExtraRepository */
    private $moduleExtraRepository;

    public function __construct(ModuleExtraRepository $moduleExtraRepository)
    {
        $this->moduleExtraRepository = $moduleExtraRepository;
    }

    public function handle(CopyLocationWidgetsToOtherLocale $copyLocationWidgetsToOtherLocale): void
    {
        $currentWidgets = $this->moduleExtraRepository->findWidgetsByModuleAndAction('Location', 'Location');

        /** @var ModuleExtra $currentWidget */
        foreach ($currentWidgets as $currentWidget) {
            $data = $currentWidget->getData();

            if (!is_array($data)
                || !isset($data['language'])
                || $data['language'] !== $copyLocationWidgetsToOtherLocale->fromLocale->getLocale()
            ) {
                // This is not a widget we want to duplicate
                continue;
            }

            // Replace the language of our widget
            $data['language'] = $copyLocationWidgetsToOtherLocale->toLocale->getLocale();

            // Save the old ID
            $oldId = $currentWidget->getId();

            $newWidget = new ModuleExtra(
                $currentWidget->getModule(),
                $currentWidget->getType(),
                $currentWidget->getLabel(),
                $currentWidget->getAction(),
                $data,
                $currentWidget->isHidden(),
                $currentWidget->getSequence()
            );

            // Insert the new widget and save the id
            $this->moduleExtraRepository->add($newWidget);
            $this->moduleExtraRepository->save($newWidget);

            // Map the new ID
            $copyLocationWidgetsToOtherLocale->extraIdMap[$oldId] = $newWidget->getId();
        }
    }
}
