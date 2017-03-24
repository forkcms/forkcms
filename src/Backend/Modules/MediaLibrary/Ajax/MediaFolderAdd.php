<?php

namespace Backend\Modules\MediaLibrary\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Language\Language;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\Command\CreateMediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\Event\MediaFolderCreated;
use Common\Uri;

/**
 * This AJAX-action will add a new MediaFolder.
 */
class MediaFolderAdd extends BackendBaseAJAXAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        /** @var MediaFolder|null $parent */
        $parent = $this->getParent();

        /** @var string $name */
        $name = $this->getFolderName($parent);

        /** @var CreateMediaFolder $createMediaFolder */
        $createMediaFolder = new CreateMediaFolder(
            $name,
            $parent,
            BackendAuthentication::getUser()->getUserId()
        );

        // Handle the MediaFolder create
        $this->get('command_bus')->handle($createMediaFolder);
        $this->get('event_dispatcher')->dispatch(
            MediaFolderCreated::EVENT_NAME,
            new MediaFolderCreated($createMediaFolder->getMediaFolderEntity())
        );

        // Success message
        $this->output(
            self::OK,
            $createMediaFolder->getMediaFolderEntity()->__toArray(),
            vsprintf(
                Language::msg('AddedFolder'),
                [
                    $createMediaFolder->getMediaFolderEntity()->getId()
                ]
            )
        );
    }

    /**
     * @param MediaFolder|null $parent
     * @return string
     */
    protected function getFolderName(MediaFolder $parent = null): string
    {
        // Define name
        $name = trim($this->get('request')->request->get('name'));

        // Urlise name
        $name = Uri::getUrl($name);

        // We don't have a name
        if ($name === '') {
            // Throw output error
            $this->output(
                self::BAD_REQUEST,
                null,
                Language::err('NameIsRequired')
            );

            return null;
        }

        // Folder name already exists
        if ($this->get('media_library.repository.folder')->existsByName($name, $parent)) {
            // Throw output error
            $this->output(
                self::BAD_REQUEST,
                null,
                Language::err('MediaFolderExists')
            );

            return null;
        }

        return $name;
    }

    /**
     * @return MediaFolder|null
     */
    protected function getParent()
    {
        // Get parameters
        $parentId = $this->get('request')->request->getInt('parent_id');

        if ($parentId === null) {
            return null;
        }

        try {
            return $this->get('media_library.repository.folder')->findOneById($parentId);
        } catch (\Exception $e) {
            // Throw output error
            $this->output(
                self::BAD_REQUEST,
                null,
                Language::err('ParentNotExists')
            );
        }
    }
}
