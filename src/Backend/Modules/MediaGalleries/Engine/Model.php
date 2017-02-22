<?php

namespace Backend\Modules\MediaGalleries\Engine;

use Backend\Core\Language\Language;
use Backend\Core\Engine\Model as BackendModel;

/**
 * In this file we store all generic functions that we will be using in the MediaGalleries module.
 */
class Model
{
    /**
     * Checks the settings and optionally returns an array with warnings
     *
     * @return array
     */
    public static function checkSettings()
    {
        $warnings = array();

        // MediaLibrary "Index" action should be allowed
        if (!BackendModel::isModuleInstalled('MediaLibrary')) {
            // Add warning
            $warnings[] = array(
                'message' => sprintf(
                    Language::err('MediaLibraryModuleRequired', 'MediaGalleries'),
                    BackendModel::createURLForAction('Modules', 'Extensions')
                )
            );
        }

        return $warnings;
    }
}
