<?php

namespace Backend\Modules\MediaLibrary\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;

/**
 * This AJAX-action will get all media folders.
 */
class MediaFolderFindAll extends BackendBaseAJAXAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        // Output success message with MediaFolder items
        $this->output(self::OK, $this->get('media_library.cache.media_folder')->get());
    }
}
