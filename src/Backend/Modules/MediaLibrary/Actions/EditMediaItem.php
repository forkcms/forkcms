<?php

namespace Backend\Modules\MediaLibrary\Actions;

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Model;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Command\UpdateMediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Event\MediaItemUpdated;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItemType;

/**
 * This action to Edit a MediaItem
 */
class EditMediaItem extends BackendBaseActionEdit
{
    /**
     * MediaItem
     *
     * @var string
     */
    protected $mediaItem;

    /**
     * Execute the action
     *
     * @return void
     */
    public function execute()
    {
        // Call parent, this will probably add some general CSS/JS or other required files
        parent::execute();

        /** @var MediaItem $mediaItem */
        $mediaItem = $this->getMediaItem();

        $form = $this->createForm(
            new MediaItemType(),
            new UpdateMediaItem(
                $mediaItem
            )
        );

        $form->handleRequest($this->get('request'));

        if (!$form->isValid()) {
            $this->tpl->assign('form', $form->createView());
            $this->tpl->assign('mediaItem', $mediaItem);
            $this->tpl->assign('backLink', $this->getBackLink());

            // Call parent
            $this->parse();
            $this->display();

            return;
        }

        /** @var UpdateMediaItem $updateMediaItem */
        $updateMediaItem = $form->getData();

        // Handle the MediaItem update
        $this->get('command_bus')->handle($updateMediaItem);
        $this->get('event_dispatcher')->dispatch(
            MediaItemUpdated::EVENT_NAME,
            new MediaItemUpdated(
                $updateMediaItem->mediaItem
            )
        );

        $this->redirect(
            $this->getBackLink(
                [
                    'report' => 'edited-media-item',
                    'var' => $updateMediaItem->title,
                    'highlight' => 'row-' . $updateMediaItem->mediaItem->getId(),
                    'id' => $updateMediaItem->mediaItem->getId(),
                ]
            )
        );
    }

    /**
     * Get media item
     *
     * @return MediaItem
     */
    private function getMediaItem()
    {
        try {
            // Define MediaItem from repository
            return $this->get('media_library.repository.item')->getOneById(
                $this->getParameter('id', 'string')
            );
        } catch (\Exception $e) {
            return $this->redirect(
                $this->getBackLink(
                    [
                        'error' => 'media-item-not-existing'
                    ]
                )
            );
        }
    }

    /**
     * @param array $parameters
     *
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
