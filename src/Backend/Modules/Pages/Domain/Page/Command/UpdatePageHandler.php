<?php

namespace Backend\Modules\Pages\Domain\Page\Command;

use Backend\Core\Engine\Authentication;
use Backend\Core\Language\Locale;
use Backend\Modules\Pages\Domain\Page\Page;
use Backend\Modules\Pages\Domain\Page\PageRepository;
use Backend\Modules\Pages\Domain\Page\Status;
use Backend\Modules\Pages\Domain\PageBlock\PageBlock;
use Backend\Modules\Pages\Domain\PageBlock\PageBlockDataTransferObject;
use Backend\Modules\Pages\Domain\PageBlock\PageBlockRepository;
use Backend\Modules\Pages\Engine\Model as BackendPagesModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;

final class UpdatePageHandler
{
    /** @var PageRepository */
    private $pageRepository;

    /** @var PageBlockRepository */
    private $pageBlockRepository;

    public function __construct(PageRepository $pageRepository, PageBlockRepository $pageBlockRepository)
    {
        $this->pageRepository = $pageRepository;
        $this->pageBlockRepository = $pageBlockRepository;
    }

    public function handle(UpdatePage $updatePage): void
    {
        $updatePage->userId = Authentication::getUser()->getUserId();
        $page = Page::fromDataTransferObject($updatePage);

        if ($page->getStatus()->isDraft()) {
            $this->pageRepository->deleteByIdAndUserIdAndStatusAndLocale(
                $page->getId(),
                $page->getUserId(),
                Status::draft(),
                $page->getLocale()
            );
        }

        $this->pageRepository->add($page);
        $this->pageRepository->save($page);
        $this->saveBlocks($page, $updatePage);
        $this->saveTags($page, $updatePage);

        $this->clearCache($page, $updatePage->page);

        if (!$page->getStatus()->isActive()) {
            BackendPagesModel::buildCache(Locale::workingLocale());

            return;
        }

        $data = $page->getData();
        $this->saveSearchIndex(
            $data['remove_from_search_index'] ?? false || $data['internal_redirect']['page_id'] ?? false || ['external_redirect']['url'] ?? false,
            $page
        );

        $oldPage = $updatePage->getPageEntity();
        $oldPage->archive();
        $this->pageRepository->save($oldPage);

        BackendPagesModel::buildCache(Locale::workingLocale());
    }

    private function saveSearchIndex(bool $removeFromSearchIndex, Page $page): void
    {
        if ($removeFromSearchIndex) {
            BackendSearchModel::removeIndex(
                'Pages',
                $page->getId()
            );

            return;
        }

        $searchText = '';
        /*
         * @TODO fix this with the new editor
        foreach ($this->blocksContent as $block) {
            $searchText .= ' ' . $block['html'];
        }
        */

        BackendSearchModel::saveIndex(
            'Pages',
            $page->getId(),
            ['title' => $page->getTitle(), 'text' => $searchText]
        );
    }

    private function saveBlocks(Page $page, UpdatePage $updatePage): void
    {
        foreach ($updatePage->blocks as $position => $blocks) {
            /** @var PageBlockDataTransferObject $blockDataTransferObject */
            foreach ($blocks as $blockDataTransferObject) {
                dump($blockDataTransferObject);
                $blockDataTransferObject->page = $page;
                $blockDataTransferObject->position = $position;
                $pageBlock = PageBlock::fromDataTransferObject($blockDataTransferObject);
                $this->pageBlockRepository->add($pageBlock);
                $this->pageBlockRepository->save($pageBlock);
            }
        }
    }

    private function saveTags(Page $page, UpdatePage $updatePage): void
    {
        if (!Authentication::isAllowedAction('Edit', 'Tags')
            || !Authentication::isAllowedAction('GetAllTags', 'Tags')) {
            return;
        }

        BackendTagsModel::saveTags(
            $page->getId(),
            $updatePage->tags,
            'Pages'
        );
    }

    private function clearCache(Page $page, ?Page $originalPage): void
    {
        if (
            !$originalPage instanceof Page
            && $page->getTitle() === $originalPage->getTitle()
            && $page->getNavigationTitle() === $originalPage->getNavigationTitle()
            && $page->isNavigationTitleOverwrite() === $originalPage->isNavigationTitleOverwrite()
            && $page->isHidden() === $originalPage->isHidden()
            && $page->getPublishOn() == $originalPage->getPublishOn()
            && $page->getPublishUntil() == $originalPage->getPublishUntil()
        ) {
            return;
        }

        BackendPagesModel::buildCache($page->getLocale());
    }
}
