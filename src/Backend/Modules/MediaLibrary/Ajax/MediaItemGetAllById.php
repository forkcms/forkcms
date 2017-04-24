<?php

namespace Backend\Modules\MediaLibrary\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Language\Language;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;

/**
 * This AJAX-action will get all media items for a group, which was trying to be saved, but another parent error appeared.
 */
class MediaItemGetAllById extends BackendBaseAJAXAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        // Output success message with variables
        $this->output(
            self::OK,
            [
                'items' => $this->getMediaItems(),
            ]
        );
    }

    /**
     * @return array
     */
    protected function getMediaItems(): array
    {
        /** @var array $ids */
        $ids = explode(',', $this->get('request')->request->get('media_ids'));

        // We have no ids
        if ($ids === null) {
            return [];
        }

        return $this->get('media_library.repository.item')->findById($ids);
    }
}
