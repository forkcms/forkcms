<?php

namespace Backend\Modules\Pages\Domain\Page\Command;

use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Language\Language as BL;
use Backend\Modules\Pages\Domain\Page\Page;
use Backend\Modules\Pages\Domain\Page\PageRepository;
use Backend\Modules\Pages\Domain\Page\Status;
use Backend\Modules\Pages\Domain\PageBlock\PageBlockRepository;
use Backend\Modules\Pages\Engine\Model as BackendPagesModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;
use Common\Doctrine\Entity\Meta;
use Common\Doctrine\Repository\MetaRepository;
use DateTime;
use ForkCMS\Utility\Module\CopyContentToOtherLocale\CopyModuleContentToOtherLocaleHandlerInterface;
use ForkCMS\Utility\Module\CopyContentToOtherLocale\CopyModuleContentToOtherLocaleInterface;
use SpoonDatabase;

final class CopyPagesToOtherLocaleHandler implements CopyModuleContentToOtherLocaleHandlerInterface
{
    public function handle(CopyModuleContentToOtherLocaleInterface $command): void
    {
        // get database
        /** @var SpoonDatabase $database */
        $database = BackendModel::getContainer()->get('database');

        /** @var MetaRepository $metaRepository */
        $metaRepository = BackendModel::get('fork.repository.meta');

        $toLanguage = (string) $command->getToLocale();
        $fromLanguage = (string) $command->getFromLocale();
        $previousResults = $command->getPreviousResults();

        // Get already copied ContentBlock ids
        $hasContentBlocks = $previousResults->hasModule('ContentBlocks');
        $contentBlockIds = [];
        if ($hasContentBlocks) {
            $contentBlockIds = $previousResults->getModuleExtraIds('ContentBlocks');
            $contentBlockOldIds = array_keys($contentBlockIds);
        }

        // Get already copied Location ids
        $hasLocations = $previousResults->hasModule('Location');
        $locationWidgetIds = false;
        if ($hasLocations) {
            $locationWidgetIds = $previousResults->getModuleExtraIds('Location');
            $locationWidgetOldIds = array_keys($locationWidgetIds);
        }

        /** @var PageRepository $pageRepository */
        $pageRepository = BackendModel::getContainer()->get(PageRepository::class);

        if ($command->getPageToCopy() instanceof Page) {
            $oldPagesToRemove = $pageRepository->findBy(
                [
                    'id' => $command->getPageToCopy()->getId(),
                    'language' => $command->getToLocale(),
                    'status' => Status::active(),
                ]
            );
            $activePagesToCopy = [$command->getPageToCopy()];
        } else {
            $oldPagesToRemove = $pageRepository->findBy(['language' => $toLanguage, 'status' => Status::active()]);
            $activePagesToCopy = $pageRepository->findBy(['language' => $fromLanguage, 'status' => Status::active()]);
        }

        // delete existing pages
        foreach ($oldPagesToRemove as $page) {
            $this->deletePage($page, $pageRepository, $toLanguage, $metaRepository);
        }

        BackendSearchModel::removeIndexByModuleAndLanguage('pages', $toLanguage);

        // loop
        /** @var Page $activePageToCopy */
        foreach ($activePagesToCopy as $activePageToCopy) {
            // get data
            $sourceData = BackendPagesModel::get($activePageToCopy->getId(), null, $fromLanguage);

            // get and build meta
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
                $toLanguage,
                $sourceData['type'],
                $sourceData['title'],
                new DateTime(),
                $sourceData['sequence'],
                $sourceData['navigation_title_overwrite'],
                $sourceData['hidden'],
                Status::active(),
                $sourceData['type'],
                $sourceData['data'],
                $sourceData['allow_move'],
                $sourceData['allow_children'],
                $sourceData['allow_edit'],
                $sourceData['allow_delete']
            );

            // insert page, store the id, we need it when building the blocks
            $pageRepository->add($page);
            $pageRepository->save($page);

            $revisionId = $page->getRevisionId();

            $blocks = [];

            // get the blocks
            $sourceBlocks = BackendPagesModel::getBlocks($activePageToCopy->getId(), null, $fromLanguage);

            // loop blocks
            foreach ($sourceBlocks as $sourceBlock) {
                // build block
                $block = $sourceBlock;
                $block['revision_id'] = $revisionId;
                $block['created_on'] = BackendModel::getUTCDate();
                $block['edited_on'] = BackendModel::getUTCDate();

                if ($hasContentBlocks) {
                    // Overwrite the extra_id of the old content block with the id of the new one
                    if (in_array($block['extra_id'], $contentBlockOldIds, true)) {
                        $block['extra_id'] = $contentBlockIds[$block['extra_id']];
                    }
                }

                if ($hasLocations) {
                    // Overwrite the extra_id of the old location widget with the id of the new one
                    if (in_array($block['extra_id'], $locationWidgetOldIds, true)) {
                        $block['extra_id'] = $locationWidgetIds[$block['extra_id']];
                    }
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

            // get tags
            $tags = BackendTagsModel::getTags('pages', $activePageToCopy->getId(), 'string', $fromLanguage);

            // save tags
            if ($tags !== '') {
                $saveWorkingLanguage = BL::getWorkingLanguage();

                // If we don't set the working language to the target language,
                // BackendTagsModel::getUrl() will use the current working
                // language, possibly causing unnecessary '-2' suffixes in
                // tags.url
                BL::setWorkingLanguage($toLanguage);

                BackendTagsModel::saveTags($page->getId(), $tags, 'pages', $toLanguage);
                BL::setWorkingLanguage($saveWorkingLanguage);
            }
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
        $pagesById = $pageRepository->findBy(['id' => $activePage, 'language' => $toLanguage]);
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
}
