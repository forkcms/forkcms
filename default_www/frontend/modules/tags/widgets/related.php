<?php

/**
 * FrontendTagsWidgetRelated
 * This is a widget with the related items based on tags
 *
 * @package		frontend
 * @subpackage	tags
 *
 * @author 		Matthias Mullie <matthias@netlash.com>
 * @author 		Annelies Van Extergem <annelies@netlash.com>
 * @since		2.0
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
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent
		parent::execute();

		// get tags
		$this->getTags();

		// get related "things" based on tags
		$this->getRelated();

		// load template
		$this->loadTemplate();

		// parse
		$this->parse();
	}


	/**
	 * Get related "things" based on tags
	 *
	 * @return	void
	 */
	private function getRelated()
	{
		// loop tags
		foreach($this->tags as $tag)
		{
			// fetch entries
			$items = FrontendModel::getDB()->retrieve('SELECT mt.module, mt.other_id
														FROM modules_tags AS mt
														INNER JOIN tags AS t ON t.id = mt.tag_id
														WHERE t.tag = ?;', array($tag));

			// loop items
			foreach($items as $i => $item)
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
			$class = 'Frontend'. SpoonFilter::toCamelCase($entry['module']) .'Model';

			// reflection of my class
			$reflection = new ReflectionClass($class);

			// check to see if the interface is implemented
			if($reflection->implementsInterface('FrontendTagsInterface'))
			{
				// get module record
				$this->related[$id] = call_user_func(array($class, 'getForTags'), (array) array($entry['other_id']));
				if($this->related[$id]) $this->related[$id] = array_pop($this->related[$id]);
			}

			// interface is not implemented
			else
			{
				// when debug is on throw an exception
				if(SPOON_DEBUG) throw new FrontendException('To use the tags module you need to implement the FrontendTagsInterface in the model of your module ('. $entry['module'] .').');

				// when debug is off show a descent message
				else exit(SPOON_DEBUG_MESSAGE);
			}

			// remove empty items
			if(empty($this->related[$id])) unset($this->related[$id]);
		}

		// only show 3
		$this->related = array_splice($this->related, 0, 3);
	}


	/**
	 * Get tags for current "page"
	 *
	 * @return void
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
			$class = 'Frontend'. SpoonFilter::toCamelCase($block['module']) .'Model';

			// reflection of my class
			$reflection = new ReflectionClass($class);

			// check to see if the interface is implemented
			if($reflection->implementsInterface('FrontendTagsInterface'))
			{
				// get record for module
				$record = call_user_func(array($class, 'getIdForTags'), $this->URL);

				// check if record exists
				if(!$record) continue;

				// add to excluded records
				$this->exclude[] = array('module' => $block['module'], 'other_id' => $record['id']);

				// get record's tags
				$tags = (array) FrontendTagsModel::getForItem($block['module'], $record['id']);
				foreach($tags as $tag) $this->tags = array_merge((array) $this->tags, (array) $tag['name']);
			}

			// interface is not implemented
			else
			{
				// when debug is on throw an exception
				if(SPOON_DEBUG) throw new FrontendException('To use the tags module you need to implement the FrontendTagsInterface in the model of your module ('. $block['module'] .').');

				// when debug is off show a descent message
				else exit(SPOON_DEBUG_MESSAGE);
			}
		}
	}


	/**
	 * Parse
	 *
	 * @return	void
	 */
	private function parse()
	{
		// assign
		$this->tpl->assign('widgetRelated', $this->related);
	}
}

?>