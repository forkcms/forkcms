<?php

namespace Backend\Modules\MediaLibrary\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Language\Language;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolderRepository;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\Exception\MediaGroupNotFound;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroupRepository;
use Common\Exception\AjaxExitException;
use Symfony\Component\HttpFoundation\Response;

/**
 * This AJAX-action will get the counts for every folder in a group.
 */
class MediaFolderGetCountsForGroup extends BackendBaseAJAXAction
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
                ? $this->get(MediaFolderRepository::class)->getCountsForMediaGroup($mediaGroup) : []
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
            return $this->get(MediaGroupRepository::class)->findOneById($id);
        } catch (MediaGroupNotFound $mediaGroupNotFound) {
            return null;
        }
    }
}
