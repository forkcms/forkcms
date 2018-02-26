<?php

namespace ForkCMS\Backend\Modules\MediaLibrary\Ajax;

use ForkCMS\Backend\Core\Engine\Base\AjaxAction;
use Symfony\Component\HttpFoundation\Response;

/**
 * This AJAX-action will get all media folders.
 */
class MediaFolderFindAll extends AjaxAction
{
    public function execute(): void
    {
        parent::execute();

        // Output success message with MediaFolder items
        $this->output(Response::HTTP_OK, $this->get('media_library.cache.media_folder')->get());
    }
}
