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
class EditMediaFolder extends BackendBaseAJAXAction
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
        $updateMediaFolder->name = \SpoonFilter::htmlspecialchars($name);

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
    protected function getMediaFolder()
    {
        $id = trim(\SpoonFilter::getPostValue('folder_id', null, '', 'int'));

        // validate values
        if ($id === '') {
            $this->output(self::BAD_REQUEST, null, Language::err('FolderIdIsRequired'));
        }

        try {
            /** @var MediaFolder $mediaFolder */
            return $this->get('media_library.repository.folder')->getOneById($id);
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
    protected function getFolderName()
    {
        // Define name
        $name = trim(\SpoonFilter::getPostValue('name', null, '', 'string'));
        $name = Uri::getUrl($name);

        if ($name === '') {
            $this->output(self::BAD_REQUEST, null, Language::err('TitleIsRequired'));
        }

        return $name;
    }
}
