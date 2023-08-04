<?php

namespace Backend\Modules\Location\Domain\Location\Command;

use Backend\Modules\Location\Domain\Location\Exception\CopyLocationWidgetsToOtherLocaleException;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtra;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtraRepository;
use ForkCMS\Utility\Module\CopyContentToOtherLocale\CopyModuleContentToOtherLocaleHandlerInterface;
use ForkCMS\Utility\Module\CopyContentToOtherLocale\CopyModuleContentToOtherLocaleInterface;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtraType;

final class CopyLocationWidgetsToOtherLocaleHandler implements CopyModuleContentToOtherLocaleHandlerInterface
{
    /** @var ModuleExtraRepository */
    private $moduleExtraRepository;

    public function __construct(ModuleExtraRepository $moduleExtraRepository)
    {
        $this->moduleExtraRepository = $moduleExtraRepository;
    }

    public function handle(CopyModuleContentToOtherLocaleInterface $command): void
    {
        if (!$command instanceof CopyLocationWidgetsToOtherLocale) {
            CopyLocationWidgetsToOtherLocaleException::forWrongCommand();
        }

        $currentWidgets = $this->moduleExtraRepository->findModuleExtra(
            'Location',
            'Location',
            ModuleExtraType::widget()
        );

        foreach ($currentWidgets as $currentWidget) {
            $data = $currentWidget->getData();

            if (!is_array($data)
                || !isset($data['language'])
                || $data['language'] !== $command->getFromLocale()->getLocale()
            ) {
                // This is not a widget we want to duplicate
                continue;
            }

            // Replace the language of our widget
            $data['language'] = $command->getToLocale()->getLocale();

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
            $command->setModuleExtraId($oldId, $newWidget->getId());
        }
    }
}
