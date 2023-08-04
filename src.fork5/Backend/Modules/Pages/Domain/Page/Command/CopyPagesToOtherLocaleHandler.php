<?php

namespace Backend\Modules\Pages\Domain\Page\Command;

use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Language\Language as BL;
use Backend\Core\Language\Locale as BackendLocale;
use Backend\Modules\Pages\Domain\Page\Page;
use Backend\Modules\Pages\Domain\Page\PageRepository;
use Backend\Modules\Pages\Domain\Page\Status;
use Backend\Modules\Pages\Domain\Page\Type;
use Backend\Modules\Pages\Domain\PageBlock\PageBlockRepository;
use Backend\Modules\Pages\Engine\Model as BackendPagesModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;
use Common\Doctrine\Entity\Meta;
use Common\Doctrine\Repository\MetaRepository;
use Common\Locale;
use DateTime;
use ForkCMS\Utility\Module\CopyContentToOtherLocale\CopyModuleContentToOtherLocaleHandlerInterface;
use ForkCMS\Utility\Module\CopyContentToOtherLocale\CopyModuleContentToOtherLocaleInterface;

final class CopyPagesToOtherLocaleHandler implements CopyModuleContentToOtherLocaleHandlerInterface
{
    public function handle(CopyModuleContentToOtherLocaleInterface $command): void
    {
        /** @var MetaRepository $metaRepository */
        $metaRepository = BackendModel::get(MetaRepository::class);

        $toLanguage = $command->getToLocale();
        $fromLanguage = $command->getFromLocale();
        $previousResults = $command->getPreviousResults();

        // Get already copied ContentBlock ids
        $hasContentBlocks = $previousResults->hasModule('ContentBlocks');

        $contentBlockIds = [];
        $contentBlockOldIds = [];

        if ($hasContentBlocks) {
            $contentBlockIds = $previousResults->getModuleExtraIds('ContentBlocks');
            $contentBlockOldIds = array_keys($contentBlockIds);
        }

        // Get already copied Location ids
        $hasLocations = $previousResults->hasModule('Location');

        $locationWidgetIds = [];
        $locationWidgetOldIds = [];

        if ($hasLocations) {
            $locationWidgetIds = $previousResults->getModuleExtraIds('Location');
            $locationWidgetOldIds = array_keys($locationWidgetIds);
        }

        /** @var PageRepository $pageRepository */
        $pageRepository = BackendModel::getContainer()->get(PageRepository::class);

        $oldPagesToRemove = $this->getOldPagesToRemove($pageRepository, $command, $toLanguage);
        $activePagesToCopy = $this->getActivePagesToCopy($pageRepository, $command, $fromLanguage);

        // delete existing pages
        foreach ($oldPagesToRemove as $page) {
            $this->deletePage($page, $pageRepository, $toLanguage, $metaRepository);
        }

        BackendSearchModel::removeIndexByModuleAndLanguage('pages', $toLanguage);

        /** @var Page $activePageToCopy */
        foreach ($activePagesToCopy as $activePageToCopy) {
            $this->copyPage(
                $activePageToCopy,
                $fromLanguage,
                $metaRepository,
                $toLanguage,
                $pageRepository,
                $hasContentBlocks,
                $contentBlockOldIds,
                $contentBlockIds,
                $hasLocations,
                $locationWidgetOldIds,
                $locationWidgetIds
            );
        }

        // build cache
        BackendPagesModel::buildCache($toLanguage);
    }

    private function deletePage(
        Page $page,
        PageRepository $pageRepository,
        string $toLanguage,
        MetaRepository $metaRepository
    ): void {
        // redefine
        $activePage = $page->getId();

        // get revision ids
        $pagesById = $pageRepository->findBy(['id' => $activePage, 'locale' => $toLanguage]);
        $revisionIDs = array_map(
            function (Page $page) {
                return $page->getRevisionId();
            },
            $pagesById
        );

        /** @var Page $item */
        foreach ($pagesById as $item) {
            $metaRepository->remove($item->getMeta());
        }

        // delete blocks and their revisions
        if (count($revisionIDs) !== 0) {
            /** @var PageBlockRepository $pageBlockRepository */
            $pageBlockRepository = BackendModel::get(PageBlockRepository::class);
            $pageBlockRepository->deleteByRevisionIds($revisionIDs);
        }

        // delete page and the revisions
        /** @var Page $item */
        foreach ($pagesById as $item) {
            $pageRepository->remove($item);
        }
    }

    private function copyPage(
        Page $activePageToCopy,
        string $fromLanguage,
        MetaRepository $metaRepository,
        string $toLanguage,
        PageRepository $pageRepository,
        bool $hasContentBlocks,
        array $contentBlockOldIds,
        array $contentBlockIds,
        bool $hasLocations,
        array $locationWidgetOldIds,
        array $locationWidgetIds
    ): void {
        // get data
        $sourceData = BackendPagesModel::get(
            $activePageToCopy->getId(),
            null,
            BackendLocale::fromString($fromLanguage)
        );

        /** @var Meta $originalMeta */
        $originalMeta = $metaRepository->find($sourceData['meta_id']);

        $meta = clone $originalMeta;

        // Insert new meta
        $metaRepository->add($meta);
        $metaRepository->save($meta);

        // build page
        $page = new Page(
            $sourceData['id'],
            BackendAuthentication::getUser()->getUserId(),
            $sourceData['parent_id'],
            $sourceData['template_id'],
            $meta,
            BackendLocale::fromString($toLanguage),
            $sourceData['type'],
            $sourceData['title'],
            null,
            new DateTime(),
            null,
            $sourceData['sequence'],
            $sourceData['navigation_title_overwrite'],
            $sourceData['hidden'],
            Status::active(),
            new Type($sourceData['type']),
            $sourceData['data'],
            $sourceData['allow_move'],
            $sourceData['allow_children'],
            $sourceData['allow_edit'],
            $sourceData['allow_delete']
        );

        $pageRepository->add($page);
        $pageRepository->save($page);

        $this->processBlocks(
            $activePageToCopy,
            $fromLanguage,
            $toLanguage,
            $hasContentBlocks,
            $contentBlockOldIds,
            $contentBlockIds,
            $hasLocations,
            $locationWidgetOldIds,
            $locationWidgetIds,
            $page
        );

        $this->processTags($activePageToCopy, $fromLanguage, $toLanguage, $page);
    }

    private function getOldPagesToRemove(
        PageRepository $pageRepository,
        CopyModuleContentToOtherLocaleInterface $command,
        Locale $toLocale
    ): array {
        if ($command->getPageToCopy() instanceof Page) {
            return $pageRepository->findBy(
                [
                    'id' => $command->getPageToCopy()->getId(),
                    'locale' => $command->getToLocale(),
                    'status' => Status::active(),
                ]
            );
        }

        return $pageRepository->findBy(['locale' => $toLocale, 'status' => Status::active()]);
    }

    private function getActivePagesToCopy(
        PageRepository $pageRepository,
        CopyModuleContentToOtherLocaleInterface $command,
        Locale $fromLocale
    ): array {
        if ($command->getPageToCopy() instanceof Page) {
            return [$command->getPageToCopy()];
        }

        return $pageRepository->findBy(['locale' => $fromLocale, 'status' => Status::active()]);
    }

    private function processTags(Page $activePageToCopy, string $fromLanguage, string $toLanguage, Page $page): void
    {
        $tags = BackendTagsModel::getTags('pages', $activePageToCopy->getId(), 'string', $fromLanguage);

        // save tags
        if ($tags === '') {
            return;
        }

        $saveWorkingLanguage = BL::getWorkingLanguage();

        // If we don't set the working language to the target language,
        // BackendTagsModel::getUrl() will use the current working
        // language, possibly causing unnecessary '-2' suffixes in
        // tags.url
        BL::setWorkingLanguage($toLanguage);

        BackendTagsModel::saveTags($page->getId(), $tags, 'pages', $toLanguage);
        BL::setWorkingLanguage($saveWorkingLanguage);
    }

    private function processBlocks(
        Page $activePageToCopy,
        string $fromLanguage,
        string $toLanguage,
        bool $hasContentBlocks,
        array $contentBlockOldIds,
        array $contentBlockIds,
        bool $hasLocations,
        array $locationWidgetOldIds,
        array $locationWidgetIds,
        Page $page
    ): void {
        $blocks = [];

        // get the blocks
        $sourceBlocks = BackendPagesModel::getBlocks(
            $activePageToCopy->getId(),
            null,
            BackendLocale::fromString($fromLanguage)
        );

        // loop blocks
        foreach ($sourceBlocks as $sourceBlock) {
            // build block
            $block = $sourceBlock;
            $block['page'] = $page;
            $block['created_on'] = BackendModel::getUTCDate();
            $block['edited_on'] = BackendModel::getUTCDate();

            // Overwrite the extra_id of the old content block with the id of the new one
            if ($hasContentBlocks && in_array($block['extra_id'], $contentBlockOldIds, true)) {
                $block['extra_id'] = $contentBlockIds[$block['extra_id']];
            }

            // Overwrite the extra_id of the old location widget with the id of the new one
            if ($hasLocations && in_array($block['extra_id'], $locationWidgetOldIds, true)) {
                $block['extra_id'] = $locationWidgetIds[$block['extra_id']];
            }

            // add block
            $blocks[] = $block;
        }

        // insert the blocks
        BackendPagesModel::insertBlocks($blocks);

        $text = '';

        // build search-text
        foreach ($blocks as $block) {
            $text .= ' ' . $block['html'];
        }

        // add
        BackendSearchModel::saveIndex(
            'Pages',
            (int) $page->getId(),
            ['title' => $page->getTitle(), 'text' => $text],
            $toLanguage
        );
    }
}
