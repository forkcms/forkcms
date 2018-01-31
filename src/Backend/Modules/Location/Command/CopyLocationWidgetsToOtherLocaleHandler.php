<?php

namespace Backend\Modules\Location\Command;

use Common\ModuleExtraType;
use ForkCMS\Component\Module\CopyModuleToOtherLocaleCommandHandlerInterface;
use ForkCMS\Component\Module\CopyModuleToOtherLocaleCommandInterface;
use SpoonDatabase;

final class CopyLocationWidgetsToOtherLocaleHandler implements CopyModuleToOtherLocaleCommandHandlerInterface
{
    /** @var SpoonDatabase */
    private $database;

    public function __construct(SpoonDatabase $database)
    {
        $this->database = $database;
    }

    public function handle(CopyModuleToOtherLocaleCommandInterface $command): void
    {
        if (!$command instanceof CopyLocationWidgetsToOtherLocale) {
            throw new \Exception('The class should be ' . CopyLocationWidgetsToOtherLocale::class);
        }

        $currentWidgets = $this->database->getRecords(
            'SELECT * FROM modules_extras WHERE module = ? AND type = ? AND action = ?',
            [
                'Location',
                ModuleExtraType::widget(),
                'Location'
            ]
        );

        if (empty($currentWidgets)) {
            return;
        }

        foreach ($currentWidgets as $currentWidget) {
            $data = unserialize($currentWidget['data']);

            if (!is_array($data)
                || !isset($data['language'])
                || $data['language'] !== $command->getFromLocale()->getLocale()
            ) {
                // This is not a widget we want to duplicate
                continue;
            }

            // Replace the language of our widget
            $data['language'] = $command->getToLocale()->getLocale();
            $currentWidget['data'] = serialize($data);

            // Save the old ID
            $oldId = $currentWidget['id'];

            // Unset the ID so we get a new one
            unset($currentWidget['id']);

            // Insert the new widget and save the id
            $newId = $this->database->insert('modules_extras', $currentWidget);

            // Map the new ID
            $command->setExtraId($oldId, $newId);
        }
    }
}
