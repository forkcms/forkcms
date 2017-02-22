<?php

namespace Backend\Modules\MediaLibrary\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Language\Language;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;

/**
 * This AJAX-action will get the counts for every folder in a group.
 */
class GetMediaFolderCountsForGroup extends BackendBaseAJAXAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        /** @var MediaGroup|null $mediaGroup */
        $mediaGroup = $this->getMediaGroup();

        /** @var array $counts */
        $counts = ($mediaGroup instanceof MediaGroup)
            ? $this->get('media_library.cache_builder')->getFolderCountsForGroup($mediaGroup) : array();

        // Output success message
        $this->output(
            self::OK,
            $counts
        );
    }

    /**
     * @return MediaGroup|null
     */
    private function getMediaGroup()
    {
        $id = trim(\SpoonFilter::getPostValue('group_id', null, '', 'string'));

        // GroupId not valid
        if ($id === '') {
            // Throw error output
            $this->output(
                self::BAD_REQUEST,
                null,
                Language::err('GroupIdIsRequired')
            );
        }

        try {
            /** @var MediaGroup */
            return $this->get('media_library.repository.group')->getOneById($id);
        } catch (\Exception $e) {
            return null;
        }
    }
}
