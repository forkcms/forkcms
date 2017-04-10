<?php

namespace Backend\Modules\MediaLibrary\Actions;

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\Command\DeleteMediaFolder as DeleteMediaFolderCommand;
use Common\Exception\RedirectException;

class MediaFolderDelete extends BackendBaseActionDelete
{
    public function execute()
    {
        /** @var MediaFolder $mediaFolder */
        $mediaFolder = $this->getMediaFolder();

        if (count($this->get('media_library.repository.folder')->findAll()) === 1) {
            $this->redirect(
                $this->getBackLink(
                    [
                        'error' => 'media-folder-delete-not-possible',
                    ]
                )
            );
        }

        if ($mediaFolder->hasConnectedItems() && $mediaFolder->hasChildrenWithConnectedItems()) {
            $this->redirect(
                $this->getBackLink(
                    [
                        'error' => 'media-folder-delete-not-possible-because-of-connected-media-items',
                    ]
                )
            );
        }

        parent::execute();

        // Handle the MediaFolder delete
        $this->get('command_bus')->handle(new DeleteMediaFolderCommand($mediaFolder));

        $this->redirect(
            $this->getBackLink(
                [
                    'report' => 'media-folder-deleted',
                    'var' => urlencode($mediaFolder->getName()),
                ]
            )
        );
    }

    /**
     * @return MediaFolder
     * @throws RedirectException
     */
    private function getMediaFolder(): MediaFolder
    {
        try {
            /** @var MediaFolder */
            return $this->get('media_library.repository.folder')->findOneById($this->get('request')->query->getInt('id'));
        } catch (\Exception $e) {
            $this->redirect(
                $this->getBackLink(
                    [
                        'error' => 'non-existing'
                    ]
                )
            );
        }
    }

    /**
     * @param array $parameters
     * @return string
     */
    private function getBackLink(array $parameters = []): string
    {
        return Model::createURLForAction(
            'MediaItemIndex',
            null,
            null,
            $parameters
        );
    }
}
