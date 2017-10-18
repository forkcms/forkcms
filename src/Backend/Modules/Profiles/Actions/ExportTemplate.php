<?php

namespace Backend\Modules\Profiles\Actions;

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;

/**
 * This is the add-action, it will display a form to add a new profile.
 */
class ExportTemplate extends BackendBaseActionAdd
{
    public function execute(): void
    {
        // define path
        $path = BACKEND_CACHE_PATH . '/Profiles/import_template.csv';

        // define required fields
        $fields = [
            'email',
            'display_name',
            'password',
        ];

        // define file
        $file = new \SpoonFileCSV();

        // download the file
        $file->arrayToFile($path, [], $fields, null, ',', '"', true);
    }
}
