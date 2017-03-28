<?php

namespace Backend\Modules\MediaLibrary\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Language\Language;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\Command\UpdateMediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\Event\MediaFolderUpdated;
use Common\Exception\AjaxExitException;

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
        $updateMediaFolder->parent = $this->getMediaFolderWhereDroppedOn($this->getTypeOfDrop());

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
     * @return MediaFolder|null
     * @throws AjaxExitException
     */
    private function getMediaFolder(): MediaFolder
    {
        $id = $this->get('request')->request->getInt('id', 0);

        if ($id === 0) {
            throw new AjaxExitException('no id provided');
        }

        try {
            /** @var MediaFolder $mediaFolder */
            return $this->get('media_library.repository.folder')->findOneById($id);
        } catch (\Exception $e) {
            throw new AjaxExitException('Folder does not exist');
        }
    }

    /**
     * @param string $typeOfDrop
     * @return MediaFolder|null
     * @throws AjaxExitException
     */
    private function getMediaFolderWhereDroppedOn(string $typeOfDrop)
    {
        $id = $this->get('request')->request->getInt('dropped_on', -1);

        if ($id === -1) {
            return null;
        }

        try {
            /** @var MediaFolder $mediaFolder */
            $mediaFolder = $this->get('media_library.repository.folder')->findOneById($id);

            if ($typeOfDrop === 'inside') {
                return $mediaFolder;
            }

            return $mediaFolder->getParent();
        } catch (\Exception $e) {
            throw new AjaxExitException('Folder does not exist');
        }
    }

    /**
     * @return string
     * @throws AjaxExitException
     */
    private function getTypeOfDrop(): string
    {
        $typeOfDrop = $this->get('request')->request->get('type');

        if ($typeOfDrop === null) {
            throw new AjaxExitException('no type provided');
        }

        if (!in_array($typeOfDrop, ['before', 'after', 'inside'])) {
            throw new AjaxExitException('wrong type provide');
        }

        return $typeOfDrop;
    }
}
