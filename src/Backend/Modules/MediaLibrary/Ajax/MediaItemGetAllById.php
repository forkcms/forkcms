<?php

namespace ForkCMS\Backend\Modules\MediaLibrary\Ajax;

use ForkCMS\Backend\Core\Engine\Base\AjaxAction;
use Symfony\Component\HttpFoundation\Response;

/**
 * This AJAX-action will get all media items for a group,
 * which was trying to be saved, but another parent error appeared.
 */
class MediaItemGetAllById extends AjaxAction
{
    public function execute(): void
    {
        parent::execute();

        // Output success message with variables
        $this->output(
            Response::HTTP_OK,
            [
                'items' => $this->getMediaItems(),
            ]
        );
    }

    protected function getMediaItems(): array
    {
        /** @var array $ids */
        $ids = explode(',', $this->getRequest()->request->get('media_ids'));

        // We have no ids
        if ($ids === null) {
            return [];
        }

        return $this->get('media_library.repository.item')->findById($ids);
    }
}
