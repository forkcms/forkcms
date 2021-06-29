<?php

namespace Backend\Modules\MediaLibrary\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Language\Language;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\Command\UpdateMediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\Exception\MediaFolderNotFound;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolderRepository;
use Backend\Modules\Pages\Engine\Model;
use Common\Exception\AjaxExitException;
use Symfony\Component\HttpFoundation\Response;

/**
 * This edit-action will reorder moved pages using Ajax
 */
class MediaFolderMove extends BackendBaseAJAXAction
{
    public function execute(): void
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

        $this->output(
            Response::HTTP_OK,
            $mediaFolder,
            sprintf(Language::msg('MediaFolderMoved'), $mediaFolder->getName())
        );
    }

    private function getMediaFolder(): MediaFolder
    {
        $id = $this->getRequest()->request->getInt('id', 0);

        if ($id === 0) {
            throw new AjaxExitException('no id provided');
        }

        try {
            /** @var MediaFolder $mediaFolder */
            return $this->get(MediaFolderRepository::class)->findOneById($id);
        } catch (MediaFolderNotFound $mediaFolderNotFound) {
            throw new AjaxExitException('Folder does not exist');
        }
    }

    private function getMediaFolderWhereDroppedOn(string $typeOfDrop): ?MediaFolder
    {
        $id = $this->getRequest()->request->getInt('dropped_on', -1);

        if ($id === -1) {
            return null;
        }

        try {
            /** @var MediaFolder $mediaFolder */
            $mediaFolder = $this->get(MediaFolderRepository::class)->findOneById($id);

            if ($typeOfDrop === Model::TYPE_OF_DROP_INSIDE) {
                return $mediaFolder;
            }

            return $mediaFolder->getParent();
        } catch (MediaFolderNotFound $mediaFolderNotFound) {
            throw new AjaxExitException('Folder does not exist');
        }
    }

    private function getTypeOfDrop(): string
    {
        $typeOfDrop = $this->getRequest()->request->get('type');

        if ($typeOfDrop === null) {
            throw new AjaxExitException('no type provided');
        }

        if (!in_array($typeOfDrop, Model::POSSIBLE_TYPES_OF_DROP, true)) {
            throw new AjaxExitException('wrong type provide');
        }

        return $typeOfDrop;
    }
}
