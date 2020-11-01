<?php

namespace Backend\Modules\Location\Command;

use Common\ModuleExtraType;
use SpoonDatabase;

final class CopyLocationWidgetsToOtherLocaleHandler
{
    /** @var SpoonDatabase */
    private $database;

    public function __construct(SpoonDatabase $database)
    {
        $this->database = $database;
    }

    public function handle(CopyLocationWidgetsToOtherLocale $copyLocationWidgetsToOtherLocale): void
    {
        $currentWidgets = (array) $this->database->getRecords(
            'SELECT * FROM modules_extras WHERE module = ? AND type = ? AND action = ?',
            [
                'Location',
                ModuleExtraType::widget(),
                'Location'
            ]
        );

        foreach ($currentWidgets as $currentWidget) {
            $data = unserialize($currentWidget['data'], ['allowed_classes' => false]);

            if (!is_array($data)
                || !isset($data['language'])
                || $data['language'] !== $copyLocationWidgetsToOtherLocale->fromLocale->getLocale()
            ) {
                // This is not a widget we want to duplicate
                continue;
            }

            // Replace the language of our widget
            $data['language'] = $copyLocationWidgetsToOtherLocale->toLocale->getLocale();
            $currentWidget['data'] = serialize($data);

            // Save the old ID
            $oldId = $currentWidget['id'];

            // Unset the ID so we get a new one
            unset($currentWidget['id']);

            // Insert the new widget and save the id
            $newId = $this->database->insert('modules_extras', $currentWidget);

            // Map the new ID
            $copyLocationWidgetsToOtherLocale->extraIdMap[$oldId] = $newId;
        }
    }
}
