<?php

namespace App\Backend\Modules\MediaLibrary\Ajax;

use App\Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use App\Backend\Core\Language\Language;
use App\Backend\Modules\MediaLibrary\Domain\MediaFolder\Command\UpdateMediaFolder;
use App\Backend\Modules\MediaLibrary\Domain\MediaFolder\Exception\MediaFolderNotFound;
use App\Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use App\Common\Exception\AjaxExitException;
use App\Common\Uri;
use Symfony\Component\HttpFoundation\Response;

/**
 * This edit-action will update a folder using AJAX
 */
class MediaFolderEdit extends BackendBaseAJAXAction
{
    public function execute(): void
    {
        parent::execute();

        /** @var UpdateMediaFolder $updateMediaFolder */
        $updateMediaFolder = $this->updateMediaFolder();

        // Output
        $this->output(
            Response::HTTP_OK,
            $updateMediaFolder->getMediaFolderEntity(),
            sprintf(
                Language::msg('MediaFolderIsEdited'),
                $updateMediaFolder->getMediaFolderEntity()->getName()
            )
        );
    }

    protected function getMediaFolder(): MediaFolder
    {
        $id = $this->getRequest()->request->getInt('folder_id');

        // validate values
        if ($id === null) {
            throw new AjaxExitException(Language::err('FolderIdIsRequired'));
        }

        try {
            /** @var MediaFolder $mediaFolder */
            return $this->get('media_library.repository.folder')->findOneById($id);
        } catch (MediaFolderNotFound $mediaFolderNotFound) {
            throw new AjaxExitException(Language::err('MediaFolderDoesNotExists'));
        }
    }

    protected function getFolderName(): string
    {
        $name = $this->getRequest()->request->get('name');

        if ($name === null) {
            throw new AjaxExitException(Language::err('TitleIsRequired'));
        }

        return Uri::getUrl($name);
    }

    private function updateMediaFolder(): UpdateMediaFolder
    {
        /** @var MediaFolder $mediaFolder */
        $mediaFolder = $this->getMediaFolder();

        /** @var string $name */
        $name = $this->getFolderName();

        /** @var UpdateMediaFolder $updateMediaFolder */
        $updateMediaFolder = new UpdateMediaFolder($mediaFolder);
        $updateMediaFolder->name = htmlspecialchars($name, ENT_QUOTES);

        // Handle the MediaFolder update
        $this->get('command_bus')->handle($updateMediaFolder);

        return $updateMediaFolder;
    }
}
