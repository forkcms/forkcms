<?php

namespace Backend\Modules\MediaLibrary\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;

/**
 * This AJAX-action will get all media folders.
 */
class MediaFolderFindAll extends BackendBaseAJAXAction
{
    /**
     * Show counter for MediaItem items in folder
     *
     * @param bool
     */
    private $showCount = false;

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        // We must show count of items in folder
        if ((bool) $this->get('request')->request->get('show_count', false)) {
            $this->showCount = true;
        }

        // Output success message with MediaFolder items
        $this->output(
            self::OK,
            $this->get('media_library.cache_builder')->getFoldersForDropdown(
                $this->showCount,
                true
            )
        );
    }
}
