<?php

namespace Backend\Modules\MediaLibrary\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;

/**
 * This AJAX-action will get all media items for a group,
 * which was trying to be saved, but another parent error appeared.
 */
class MediaItemGetAllById extends BackendBaseAJAXAction
{
    public function execute(): void
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
