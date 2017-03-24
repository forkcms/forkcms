<?php

namespace Backend\Modules\MediaLibrary\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Language\Language;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;

/**
 * This AJAX-action will get the counts for every folder in a group.
 */
class MediaFolderGetCountsForGroup extends BackendBaseAJAXAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        /** @var MediaGroup|null $mediaGroup */
        $mediaGroup = $this->getMediaGroup();

        // Output success message
        $this->output(
            self::OK,
            $mediaGroup instanceof MediaGroup
                ? $this->get('media_library.repository.folder')->getCountsForMediaGroup($mediaGroup) : []
        );
    }

    /**
     * @return MediaGroup|null
     */
    private function getMediaGroup()
    {
        $id = $this->get('request')->request->get('group_id');

        // GroupId not valid
        if ($id === null) {
            // Throw error output
            $this->output(
                self::BAD_REQUEST,
                null,
                Language::err('GroupIdIsRequired')
            );
        }

        try {
            /** @var MediaGroup */
            return $this->get('media_library.repository.group')->findOneById($id);
        } catch (\Exception $e) {
            return null;
        }
    }
}
