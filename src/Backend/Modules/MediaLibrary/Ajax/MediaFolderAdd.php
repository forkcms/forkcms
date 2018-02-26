<?php

namespace ForkCMS\Backend\Modules\MediaLibrary\Ajax;

use ForkCMS\Backend\Core\Engine\Base\AjaxAction;
use ForkCMS\Backend\Core\Engine\Authentication as BackendAuthentication;
use ForkCMS\Backend\Core\Language\Language;
use ForkCMS\Backend\Modules\MediaLibrary\Domain\MediaFolder\Command\CreateMediaFolder;
use ForkCMS\Backend\Modules\MediaLibrary\Domain\MediaFolder\Exception\MediaFolderNotFound;
use ForkCMS\Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use ForkCMS\Common\Exception\AjaxExitException;
use Symfony\Component\HttpFoundation\Response;

/**
 * This AJAX-action will add a new MediaFolder.
 */
class MediaFolderAdd extends AjaxAction
{
    public function execute(): void
    {
        parent::execute();

        /** @var CreateMediaFolder $createMediaFolder */
        $createMediaFolder = $this->createMediaFolder();

        // Success message
        $this->output(
            Response::HTTP_OK,
            $createMediaFolder->getMediaFolderEntity(),
            vsprintf(
                Language::msg('FolderIsAdded'),
                [
                    $createMediaFolder->getMediaFolderEntity()->getId(),
                ]
            )
        );
    }

    private function createMediaFolder(): CreateMediaFolder
    {
        /** @var MediaFolder|null $parent */
        $parent = $this->getParent();

        /** @var string $name */
        $name = $this->getFolderName($parent);

        /** @var CreateMediaFolder $createMediaFolder */
        $createMediaFolder = new CreateMediaFolder(
            $name,
            BackendAuthentication::getUser()->getUserId(),
            $parent
        );

        // Handle the MediaFolder create
        $this->get('command_bus')->handle($createMediaFolder);

        return $createMediaFolder;
    }

    protected function getFolderName(MediaFolder $parent = null): string
    {
        // Define name
        $name = $this->getRequest()->request->get('name');

        // We don't have a name
        if ($name === null) {
            throw new AjaxExitException(Language::err('NameIsRequired'));
        }

        // Folder name already exists
        if ($this->get('media_library.repository.folder')->existsByName($name, $parent)) {
            throw new AjaxExitException(Language::err('MediaFolderExists'));
        }

        return $name;
    }

    protected function getParent(): ?MediaFolder
    {
        // Get parameters
        $parentId = $this->getRequest()->request->getInt('parent_id');

        if ($parentId === 0) {
            return null;
        }

        try {
            return $this->get('media_library.repository.folder')->findOneById($parentId);
        } catch (MediaFolderNotFound $mediaFolderNotFound) {
            throw new AjaxExitException(Language::err('ParentNotExists'));
        }
    }
}
