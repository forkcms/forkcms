<?php

namespace Backend\Modules\Pages\Domain\Page\Command;

use SimpleBus\Message\Bus\MessageBus;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Pages\Engine\Model as BackendPagesModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;
use ForkCMS\Component\Module\CopyModuleToOtherLocaleInterface;
use ForkCMS\Component\Module\CopyModuleToOtherLocaleHandlerInterface;

final class CopyPagesToOtherLocaleHandler implements CopyModuleToOtherLocaleHandlerInterface
{
    public function handle(CopyModuleToOtherLocaleInterface $command): void
    {
        // get database
        $database = BackendModel::getContainer()->get('database');

        $toLanguage = (string) $command->getToLocale();
        $fromLanguage = (string) $command->getFromLocale();

        // Get already copied ContentBlock ids
        $hasContentBlocks = $command->getPreviousResults()->hasModule('ContentBlocks');
        if ($hasContentBlocks) {
            $contentBlockIds = $command->getPreviousResults()->getExtraIds('ContentBlocks');
            $contentBlockOldIds = array_keys($contentBlockIds);
        }

        // Get already copied Location ids
        $hasLocations = $command->getPreviousResults()->hasModule('Location');
        if ($hasLocations) {
            $locationWidgetIds = $command->getPreviousResults()->getExtraIds('Location');
            $locationWidgetOldIds = array_keys($locationWidgetIds);
        }

        // get all old pages
        $ids = $database->getColumn(
            'SELECT id
             FROM pages AS i
             WHERE i.language = ? AND i.status = ?',
            [$toLanguage, 'active']
        );

        // any old pages
        if (!empty($ids)) {
            // delete existing pages
            foreach ($ids as $id) {
                // redefine
                $id = (int) $id;

                // get revision ids
                $revisionIDs = (array) $database->getColumn(
                    'SELECT i.revision_id
                     FROM pages AS i
                     WHERE i.id = ? AND i.language = ?',
                    [$id, $toLanguage]
                );

                // get meta ids
                $metaIDs = (array) $database->getColumn(
                    'SELECT i.meta_id
                     FROM pages AS i
                     WHERE i.id = ? AND i.language = ?',
                    [$id, $toLanguage]
                );

                // delete meta records
                if (!empty($metaIDs)) {
                    $database->delete('meta', 'id IN (' . implode(',', $metaIDs) . ')');
                }

                // delete blocks and their revisions
                if (!empty($revisionIDs)) {
                    $database->delete(
                        'pages_blocks',
                        'revision_id IN (' . implode(',', $revisionIDs) . ')'
                    );
                }

                // delete page and the revisions
                if (!empty($revisionIDs)) {
                    $database->delete('pages', 'revision_id IN (' . implode(',', $revisionIDs) . ')');
                }
            }
        }

        // delete search indexes
        $database->delete('search_index', 'module = ? AND language = ?', ['pages', $toLanguage]);

        // get all active pages
        $ids = $database->getColumn(
            'SELECT id
             FROM pages AS i
             WHERE i.language = ? AND i.status = ?',
            [$fromLanguage, 'active']
        );

        // loop
        foreach ($ids as $id) {
            // get data
            $sourceData = BackendPagesModel::get($id, null, $fromLanguage);

            // get and build meta
            $meta = $database->getRecord(
                'SELECT *
                 FROM meta
                 WHERE id = ?',
                [$sourceData['meta_id']]
            );

            // remove id
            unset($meta['id']);

            // init page
            $page = [];

            // build page
            $page['id'] = $sourceData['id'];
            $page['user_id'] = $sourceData['user_id'];
            $page['parent_id'] = $sourceData['parent_id'];
            $page['template_id'] = $sourceData['template_id'];
            $page['meta_id'] = (int) $database->insert('meta', $meta);
            $page['language'] = $toLanguage;
            $page['type'] = $sourceData['type'];
            $page['title'] = $sourceData['title'];
            $page['navigation_title'] = $sourceData['navigation_title'];
            $page['navigation_title_overwrite'] = $sourceData['navigation_title_overwrite'];
            $page['hidden'] = $sourceData['hidden'];
            $page['status'] = 'active';
            $page['publish_on'] = BackendModel::getUTCDate();
            $page['created_on'] = BackendModel::getUTCDate();
            $page['edited_on'] = BackendModel::getUTCDate();
            $page['allow_move'] = $sourceData['allow_move'];
            $page['allow_children'] = $sourceData['allow_children'];
            $page['allow_edit'] = $sourceData['allow_edit'];
            $page['allow_delete'] = $sourceData['allow_delete'];
            $page['sequence'] = $sourceData['sequence'];
            $page['data'] = ($sourceData['data'] !== null) ? serialize($sourceData['data']) : null;

            // insert page, store the id, we need it when building the blocks
            $revisionId = BackendPagesModel::insert($page);

            $blocks = [];

            // get the blocks
            $sourceBlocks = BackendPagesModel::getBlocks($id, null, $fromLanguage);

            // loop blocks
            foreach ($sourceBlocks as $sourceBlock) {
                // build block
                $block = $sourceBlock;
                $block['revision_id'] = $revisionId;
                $block['created_on'] = BackendModel::getUTCDate();
                $block['edited_on'] = BackendModel::getUTCDate();

                if ($hasContentBlocks) {
                    // Overwrite the extra_id of the old content block with the id of the new one
                    if (in_array($block['extra_id'], $contentBlockOldIds)) {
                        $block['extra_id'] = $contentBlockIds[$block['extra_id']];
                    }
                }

                if ($hasLocations) {
                    // Overwrite the extra_id of the old location widget with the id of the new one
                    if (in_array($block['extra_id'], $locationWidgetOldIds)) {
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
                (int) $page['id'],
                ['title' => $page['title'], 'text' => $text],
                $toLanguage
            );

            // get tags
            $tags = BackendTagsModel::getTags('pages', $id, 'string', $fromLanguage);

            // save tags
            if ($tags != '') {
                $saveWorkingLanguage = BL::getWorkingLanguage();

                // If we don't set the working language to the target language,
                // BackendTagsModel::getUrl() will use the current working
                // language, possibly causing unnecessary '-2' suffixes in
                // tags.url
                BL::setWorkingLanguage($toLanguage);

                BackendTagsModel::saveTags($page['id'], $tags, 'pages', $toLanguage);
                BL::setWorkingLanguage($saveWorkingLanguage);
            }
        }

        // build cache
        BackendPagesModel::buildCache($toLanguage);
    }
}
