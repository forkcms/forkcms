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
class MoveMediaFolder extends BackendBaseAJAXAction
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

        /** @var MediaFolder|null $mediaFolder */
        $droppedOnMediaFolder = $this->getMediaFolderWhereDroppedOn();

        /** @var string $typeOfDrop */
        $typeOfDrop = $this->getTypeOfDrop();

        // Dropped on no media folder
        if ($droppedOnMediaFolder === null) {
            // Remove parent
            $updateMediaFolder->parent = null;
        // Redefine parent
        } else {
            if ($typeOfDrop == 'inside') {
                // Set new parent
                $updateMediaFolder->parent = $droppedOnMediaFolder;
            } else {
                $updateMediaFolder->parent = $droppedOnMediaFolder->getParent();
            }
        }

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
    private function getMediaFolder()
    {
        $id = \SpoonFilter::getPostValue('id', null, 0, 'int');

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
     * @return MediaFolder|null
     */
    private function getMediaFolderWhereDroppedOn()
    {
        $id = \SpoonFilter::getPostValue('dropped_on', null, -1, 'int');

        if ($id !== -1) {
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

        return null;
    }

    /**
     * @return string
     */
    private function getTypeOfDrop()
    {
        $typeOfDrop = \SpoonFilter::getPostValue(
            'type',
            array(
                'before',
                'after',
                'inside',
            ),
            ''
        );

        if ($typeOfDrop === '') {
            $this->output(
                self::BAD_REQUEST,
                null,
                'no type provided'
            );
        }

        return $typeOfDrop;
    }
}
