<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a widget with the related items based on tags
 *
 * @author Matthias Mullie <matthias@mullie.eu>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 */
class FrontendTagsWidgetRelated extends FrontendBaseWidget
{
	/**
	 * Records to exclude
	 *
	 * @var		array
	 */
	private $exclude = array();

	/**
	 * Tags on this page
	 *
	 * @var		array
	 */
	private $tags = array();

	/**
	 * Related records
	 *
	 * @var		array
	 */
	private $related = array();

	/**
	 * Execute the extra
	 */
	public function execute()
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
	private function getRelated()
	{
		// loop tags
		foreach($this->tags as $tag)
		{
			// fetch entries
			$items = (array) FrontendModel::getDB()->getRecords(
				'SELECT mt.module, mt.other_id
				 FROM modules_tags AS mt
				 INNER JOIN tags AS t ON t.id = mt.tag_id
				 WHERE t.language = ? AND t.tag = ?',
				array(FRONTEND_LANGUAGE, $tag)
			);

			// loop items
			foreach($items as $item)
			{
				// loop existing items
				foreach($this->related as $related)
				{
					// already exists
					if($item == $related) continue 2;
				}

				// add to list of related items
				$this->related[] = $item;
			}
		}

		// loop entries
		foreach($this->related as $id => $entry)
		{
			// loop excluded records
			foreach($this->exclude as $exclude)
			{
				// check if this entry should be excluded
				if($entry['module'] == $exclude['module'] && $entry['other_id'] == $exclude['other_id'])
				{
					unset($this->related[$id]);
					continue 2;
				}
			}

			// set module class
			$class = 'Frontend' . SpoonFilter::toCamelCase($entry['module']) . 'Model';

			// get module record
			$this->related[$id] = FrontendTagsModel::callFromInterface($entry['module'], $class, 'getForTags', (array) array($entry['other_id']));
			if($this->related[$id]) $this->related[$id] = array_pop($this->related[$id]);

			// remove empty items
			if(empty($this->related[$id])) unset($this->related[$id]);
		}

		// only show 3
		$this->related = array_splice($this->related, 0, 3);
	}

	/**
	 * Get tags for current "page"
	 */
	private function getTags()
	{
		// get page id
		$pageId = FrontendPage::getCurrentPageId();

		// array of excluded records
		$this->exclude[] = array('module' => 'pages', 'other_id' => $pageId);

		// get tags for page
		$tags = (array) FrontendTagsModel::getForItem('pages', $pageId);
		foreach($tags as $tag) $this->tags = array_merge((array) $this->tags, (array) $tag['name']);

		// get page record
		$record = (array) FrontendNavigation::getPageInfo($pageId);

		// loop blocks
		foreach((array) $record['extra_blocks'] as $block)
		{
			// set module class
			$class = 'Frontend' . SpoonFilter::toCamelCase($block['module']) . 'Model';

			// get record for module
			$record = FrontendTagsModel::callFromInterface($block['module'], $class, 'getIdForTags', $this->URL);

			// check if record exists
			if(!$record) continue;

			// add to excluded records
			$this->exclude[] = array('module' => $block['module'], 'other_id' => $record['id']);

			// get record's tags
			$tags = (array) FrontendTagsModel::getForItem($block['module'], $record['id']);
			foreach($tags as $tag) $this->tags = array_merge((array) $this->tags, (array) $tag['name']);
		}
	}

	/**
	 * Parse
	 */
	private function parse()
	{
		// assign
		$this->tpl->assign('widgetTagsRelated', $this->related);
	}
}
