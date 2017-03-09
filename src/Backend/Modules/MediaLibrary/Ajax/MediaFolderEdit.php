<?php

namespace Backend\Modules\MediaLibrary\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Language\Language;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\Command\UpdateMediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\Event\MediaFolderUpdated;
use Common\Uri;

/**
 * This edit-action will update a folder using AJAX
 */
class MediaFolderEdit extends BackendBaseAJAXAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        /** @var MediaFolder $mediaFolder */
        $mediaFolder = $this->getMediaFolder();

        /** @var string $name */
        $name = $this->getFolderName();

        /** @var UpdateMediaFolder $updateMediaFolder */
        $updateMediaFolder = new UpdateMediaFolder($mediaFolder);
        $updateMediaFolder->name = htmlspecialchars($name, ENT_QUOTES);

        // Handle the MediaFolder update
        $this->get('command_bus')->handle($updateMediaFolder);
        $this->get('event_dispatcher')->dispatch(
            MediaFolderUpdated::EVENT_NAME,
            new MediaFolderUpdated($updateMediaFolder->getMediaFolderEntity())
        );

        // Output
        $this->output(
            self::OK,
            $updateMediaFolder->getMediaFolderEntity()->__toArray(),
            sprintf(
                Language::msg('MediaFolderIsEdited'),
                $updateMediaFolder->getMediaFolderEntity()->getName()
            )
        );
    }

    /**
     * @return MediaFolder
     */
    protected function getMediaFolder(): MediaFolder
    {
        $id = $this->get('request')->request->get('folder_id');

        // validate values
        if ($id === null) {
            $this->output(self::BAD_REQUEST, null, Language::err('FolderIdIsRequired'));
        }

        try {
            /** @var MediaFolder $mediaFolder */
            return $this->get('media_library.repository.folder')->getOneById((int) $id);
        } catch (\Exception $e) {
            $this->output(
                self::BAD_REQUEST,
                null,
                Language::err('MediaFolderDoesNotExists')
            );
        }
    }

    /**
     * @return string
     */
    protected function getFolderName(): string
    {
        // Define name
        $name = $this->get('request')->request->get('name');

        if ($name === null) {
            $this->output(self::BAD_REQUEST, null, Language::err('TitleIsRequired'));
        }

        $name = Uri::getUrl($name);
        return $name;
    }
}
