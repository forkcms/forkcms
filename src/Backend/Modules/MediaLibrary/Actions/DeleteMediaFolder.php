<?php

namespace Backend\Modules\MediaLibrary\Actions;

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\Command\DeleteMediaFolder as DeleteMediaFolderCommand;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\Event\MediaFolderDeleted;

/**
 * This action will delete a MediaFolder
 */
class DeleteMediaFolder extends BackendBaseActionDelete
{
    /**
     * Execute the action
     */
    public function execute()
    {
        /** @var MediaFolder $mediaFolder */
        $mediaFolder = $this->getMediaFolder();

        // Call parent, this will probably add some general CSS/JS or other required files
        parent::execute();

        /** @var DeleteMediaFolderCommand $deleteMediaFolder */
        $deleteMediaFolder = new DeleteMediaFolderCommand($mediaFolder);

        // Handle the MediaFolder delete
        $this->get('command_bus')->handle($deleteMediaFolder);
        $this->get('event_dispatcher')->dispatch(
            MediaFolderDeleted::EVENT_NAME,
            new MediaFolderDeleted($deleteMediaFolder->mediaFolder)
        );

        $this->redirect(
            $this->getBackLink(
                [
                    'report' => 'deleted',
                    'var' => urlencode($mediaFolder->getName()),
                ]
            )
        );
    }

    /**
     * @return MediaFolder
     */
    protected function getMediaFolder()
    {
        try {
            // Get id to delete
            $id = $this->getParameter('id', 'int');

            /** @var MediaFolder */
            return $this->get('media_library.repository.folder')->getOneById($id);
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
    private function getBackLink(array $parameters = [])
    {
        return Model::createURLForAction(
            'Index',
            null,
            null,
            $parameters
        );
    }
}
