<?php

namespace Backend\Modules\MediaLibrary\Actions;

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Model;
use Backend\Form\Type\DeleteType;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Command\UpdateMediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\Exception\MediaItemNotFound;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItemType;

class MediaItemEdit extends BackendBaseActionEdit
{
    /** @var int */
    protected $folderId;

    /** @var string */
    protected $mediaItem;

    public function execute(): void
    {
        parent::execute();

        $this->parseJsFiles();
        $this->parseCssFiles();

        /** @var MediaItem $mediaItem */
        $mediaItem = $this->getMediaItem();

        // Define folder id
        $this->folderId = $this->getRequest()->query->getInt('folder');

        $form = $this->createForm(
            MediaItemType::class,
            new UpdateMediaItem(
                $mediaItem
            )
        );

        $form->handleRequest($this->get('request'));

        $deleteForm = $this->createForm(
            DeleteType::class,
            ['id' => $mediaItem->getId()],
            ['module' => $this->getModule(), 'action' => 'MediaItemDelete']
        );
        $this->template->assign('deleteForm', $deleteForm->createView());

        if (!$form->isValid()) {
            $this->template->assign('folderId', $this->folderId);
            $this->template->assign('tree', $this->get('media_library.manager.tree')->getHTML());
            $this->header->addJsData(
                'MediaLibrary',
                'openedFolderId',
                $this->folderId ?? null
            );
            $this->template->assign('form', $form->createView());
            $this->template->assign('mediaItem', $mediaItem);
            $this->template->assign('backLink', $this->getBackLink());

            // Call parent
            $this->parse();
            $this->display();

            return;
        }

        /** @var UpdateMediaItem $updateMediaItem */
        $updateMediaItem = $form->getData();

        // Handle the MediaItem update
        $this->get('command_bus')->handle($updateMediaItem);

        $this->redirect(
            $this->getBackLink(
                [
                    'report' => 'media-item-edited',
                    'var' => $updateMediaItem->title,
                    'highlight' => 'row-' . $updateMediaItem->getMediaItemEntity()->getId(),
                    'id' => $updateMediaItem->getMediaItemEntity()->getId(),
                    'folder' => $this->folderId,
                ]
            )
        );
    }

    private function getMediaItem(): MediaItem
    {
        try {
            // Define MediaItem from repository
            return $this->get('media_library.repository.item')->findOneById(
                $this->getRequest()->query->get('id')
            );
        } catch (MediaItemNotFound $mediaItemNotFound) {
            $this->redirect(
                $this->getBackLink(
                    [
                        'error' => 'media-item-not-existing',
                    ]
                )
            );
        }
    }

    private function getBackLink(array $parameters = []): string
    {
        return Model::createUrlForAction(
            'MediaItemIndex',
            null,
            null,
            $parameters
        );
    }

    private function parseJsFiles(): void
    {
        $this->header->addJS('/js/vendors/jstree.js', null, false, true);
    }

    private function parseCssFiles(): void
    {
        $this->header->addCSS('/css/vendors/jstree/style.css', null, true, false);
    }
}
