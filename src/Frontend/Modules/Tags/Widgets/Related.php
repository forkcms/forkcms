<?php

namespace Frontend\Modules\Tags\Widgets;

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Tags\Engine\Model as FrontendTagsModel;

/**
 * This is a widget with the related items based on tags
 */
class Related extends FrontendBaseWidget
{
    /**
     * Records to exclude
     *
     * @var array
     */
    private $exclude = [];

    /**
     * Tags on this page
     *
     * @var array
     */
    private $tags = [];

    /**
     * Related records
     *
     * @var array
     */
    private $related = [];

    public function execute(): void
    {
        parent::execute();
        $this->getTags();
        $this->getRelated();
        $this->loadTemplate();
        $this->parse();
    }

    /**
     * Get related "things" based on tags
     */
    private function getRelated(): void
    {
        // loop tags
        foreach ($this->tags as $tag) {
            // fetch entries
            $items = (array) $this->get('database')->getRecords(
                'SELECT mt.module, mt.other_id
                 FROM modules_tags AS mt
                 INNER JOIN tags AS t ON t.id = mt.tag_id
                 WHERE t.language = ? AND t.tag = ?',
                [LANGUAGE, $tag]
            );

            // loop items
            foreach ($items as $item) {
                // loop existing items
                foreach ($this->related as $related) {
                    // already exists
                    if ($item == $related) {
                        continue 2;
                    }
                }

                // add to list of related items
                $this->related[] = $item;
            }
        }

        // loop entries
        foreach ($this->related as $id => $entry) {
            // loop excluded records
            foreach ($this->exclude as $exclude) {
                // check if this entry should be excluded
                if ($entry['module'] == $exclude['module'] && $entry['other_id'] == $exclude['other_id']) {
                    unset($this->related[$id]);
                    continue 2;
                }
            }

            // set module class
            $class = 'Frontend\\Modules\\' . $entry['module'] . '\\Engine\\Model';

            // get module record
            $this->related[$id] = FrontendTagsModel::callFromInterface(
                $entry['module'],
                $class,
                'getForTags',
                (array) [$entry['other_id']]
            );
            if ($this->related[$id]) {
                $this->related[$id] = array_pop($this->related[$id]);
            }

            // remove empty items
            if (empty($this->related[$id])) {
                unset($this->related[$id]);
            }
        }

        // only show 3
        $this->related = array_splice($this->related, 0, 3);
    }

    /**
     * Get tags for current "page"
     */
    private function getTags(): void
    {
        $pageId = $this->getContainer()->get('page')->getId();
        $this->exclude[] = ['module' => 'Pages', 'other_id' => $pageId];

        $tags = (array) FrontendTagsModel::getForItem('pages', $pageId);
        foreach ($tags as $tag) {
            $this->tags = array_merge((array) $this->tags, (array) $tag['name']);
        }

        $record = (array) FrontendNavigation::getPageInfo($pageId);
        foreach ((array) $record['extra_blocks'] as $block) {
            $class = 'Frontend\\Modules\\' . $block['module'] . '\\Engine\\Model';

            if (is_callable([$class, 'getIdForTags'])) {
                $itemId = FrontendTagsModel::callFromInterface($block['module'], $class, 'getIdForTags', $this->url);

                if (!$itemId) {
                    continue;
                }

                $this->exclude[] = ['module' => $block['module'], 'other_id' => $itemId];

                $tags = (array) FrontendTagsModel::getForItem($block['module'], $itemId);
                foreach ($tags as $tag) {
                    $this->tags = array_merge((array) $this->tags, (array) $tag['name']);
                }
            }
        }
    }

    private function parse(): void
    {
        // assign
        $this->template->assign('widgetTagsRelated', $this->related);
    }
}
