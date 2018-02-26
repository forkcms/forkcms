<?php

namespace ForkCMS\Backend\Modules\MediaLibrary\Ajax;

use ForkCMS\Backend\Core\Engine\Base\AjaxAction;
use ForkCMS\Backend\Core\Language\Language;
use ForkCMS\Backend\Modules\MediaLibrary\Domain\MediaGroup\Exception\MediaGroupNotFound;
use ForkCMS\Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use ForkCMS\Common\Exception\AjaxExitException;
use Symfony\Component\HttpFoundation\Response;

/**
 * This AJAX-action will get the counts for every folder in a group.
 */
class MediaFolderGetCountsForGroup extends AjaxAction
{
    /**
     * Execute the action
     */
    public function execute(): void
    {
        parent::execute();

        /** @var MediaGroup|null $mediaGroup */
        $mediaGroup = $this->getMediaGroup();

        // Output success message
        $this->output(
            Response::HTTP_OK,
            $mediaGroup instanceof MediaGroup
                ? $this->get('media_library.repository.folder')->getCountsForMediaGroup($mediaGroup) : []
        );
    }

    private function getMediaGroup(): ?MediaGroup
    {
        $id = $this->getRequest()->request->get('group_id');

        // GroupId not valid
        if ($id === null) {
            throw new AjaxExitException(Language::err('GroupIdIsRequired'));
        }

        try {
            return $this->get('media_library.repository.group')->findOneById($id);
        } catch (MediaGroupNotFound $mediaGroupNotFound) {
            return null;
        }
    }
}
