<?php

namespace Backend\Modules\MediaLibrary\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;

/**
 * This AJAX-action will get all media folders.
 */
class GetMediaFolders extends BackendBaseAJAXAction
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
        if (trim(\SpoonFilter::getPostValue('show_count', null, '', 'bool'))) {
            $this->showCount = true;
        }

        // Get folders
        $folders = $this->get('media_library.cache_builder')->getFoldersForDropdown(
            $this->showCount,
            true
        );

        // Output success message with MediaFolder items
        $this->output(
            self::OK,
            $folders
        );
    }
}
