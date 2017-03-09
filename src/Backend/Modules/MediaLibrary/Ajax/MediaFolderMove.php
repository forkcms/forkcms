<?php

namespace Backend\Modules\MediaLibrary\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Language\Language;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\Command\UpdateMediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\Event\MediaFolderUpdated;

/**
 * This edit-action will reorder moved pages using Ajax
 */
class MediaFolderMove extends BackendBaseAJAXAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        // call parent
        parent::execute();

        /** @var MediaFolder $mediaFolder */
        $mediaFolder = $this->getMediaFolder();

        /** @var UpdateMediaFolder $updateMediaFolder */
        $updateMediaFolder = new UpdateMediaFolder($mediaFolder);

        /** @var string $typeOfDrop */
        $typeOfDrop = $this->getTypeOfDrop();

        $updateMediaFolder->parent = $this->getMediaFolderWhereDroppedOn($typeOfDrop);

        // Handle the MediaFolder update
        $this->get('command_bus')->handle($updateMediaFolder);
        $this->get('event_dispatcher')->dispatch(
            MediaFolderUpdated::EVENT_NAME,
            new MediaFolderUpdated($updateMediaFolder->getMediaFolderEntity())
        );

        $this->output(
            self::OK,
            $mediaFolder->__toArray(),
            sprintf(Language::msg('MediaFolderMoved'), $mediaFolder->getName())
        );
    }

    /**
     * @return MediaFolder
     */
    private function getMediaFolder(): MediaFolder
    {
        $id = (int) $this->get('request')->request->get('id', 0);

        if ($id === 0) {
            $this->output(
                self::BAD_REQUEST,
                null,
                'no id provided'
            );
        }

        try {
            /** @var MediaFolder $mediaFolder */
            return $this->get('media_library.repository.folder')->getOneById($id);
        } catch (\Exception $e) {
            $this->output(
                self::BAD_REQUEST,
                null,
                'Folder does not exist'
            );
        }
    }

    /**
     * @param string $typeOfDrop
     * @return MediaFolder|null
     */
    private function getMediaFolderWhereDroppedOn(string $typeOfDrop)
    {
        $id = (int) $this->get('request')->request->get('dropped_on', -1);

        if ($id !== -1) {
            try {
                /** @var MediaFolder $mediaFolder */
                $mediaFolder = $this->get('media_library.repository.folder')->getOneById($id);

                if ($typeOfDrop === 'inside') {
                    return $mediaFolder;
                }

                return $mediaFolder->getParent();
            } catch (\Exception $e) {
                $this->output(
                    self::BAD_REQUEST,
                    null,
                    'Folder does not exist'
                );
            }
        }

        return null;
    }

    /**
     * @return string
     */
    private function getTypeOfDrop(): string
    {
        $typeOfDrop = $this->get('request')->request->get('type');

        if ($typeOfDrop === null) {
            $this->output(
                self::BAD_REQUEST,
                null,
                'no type provided'
            );
        }

        if (!in_array($typeOfDrop, ['before', 'after', 'inside'])) {
            $this->output(
                self::BAD_REQUEST,
                null,
                'wrong type provided'
            );
        }

        return $typeOfDrop;
    }
}
