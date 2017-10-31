<?php

namespace Backend\Modules\Location\Command;

use Backend\Core\Engine\Model;
use Common\ModuleExtraType;
use SpoonDatabase;

final class CopyLocationWidgetsToOtherLocaleHandler
{
    public function handle(CopyLocationWidgetsToOtherLocale $copyLocationWidgetsToOtherLocale): void
    {
        /** @var SpoonDatabase $database */
        $database = Model::get('database');
        $currentWidgets = $database->getRecords(
            'SELECT * FROM modules_extras WHERE module = ? AND type = ? AND action = ?',
            [
                'Location',
                ModuleExtraType::widget(),
                'Location'
            ]
        );

        foreach ($currentWidgets as $currentWidget) {
            $data = unserialize($currentWidget['data']);

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
            $newId = $database->insert('modules_extras', $currentWidget);

            // Map the new ID
            $copyLocationWidgetsToOtherLocale->extraIdMap[$oldId] = $newId;
        }
    }
}
