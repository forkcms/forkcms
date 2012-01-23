<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * BackendPagesCopy
 * This is the copy-action, it will copy pages from one language to another
 * @remark:	IMPORTANT existing data will be removed, this feature is also expiremental!
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendPagesCopy extends BackendBaseActionDelete
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// get parameters
		$from = $this->getParameter('from');
		$to = $this->getParameter('to');

		// validate
		if($from == '') throw new BackendException('Specify a from-parameter.');
		if($to == '') throw new BackendException('Specify a to-parameter.');

		// get db
		$db = BackendModel::getDB(true);

		// get all old pages
		$ids = $db->getColumn(
			'SELECT id
			 FROM pages AS i
			 WHERE i.language = ? AND i.status = ?',
			array($to, 'active')
		);

		// any old pages
		if(!empty($ids))
		{
			// delete existing pages
			foreach($ids as $id)
			{
				// redefine
				$id = (int) $id;

				// get revision ids
				$revisionIDs = (array) $db->getColumn(
					'SELECT i.revision_id
					 FROM pages AS i
					 WHERE i.id = ? AND i.language = ?',
					array($id, $to)
				);

				// get meta ids
				$metaIDs = (array) $db->getColumn(
					'SELECT i.meta_id
					 FROM pages AS i
					 WHERE i.id = ? AND i.language = ?',
					array($id, $to)
				);

				// delete meta records
				if(!empty($metaIDs)) $db->delete('meta', 'id IN (' . implode(',', $metaIDs) . ')');

				// delete blocks and their revisions
				if(!empty($revisionIDs)) $db->delete('pages_blocks', 'revision_id IN (' . implode(',', $revisionIDs) . ')');

				// delete page and the revisions
				if(!empty($revisionIDs)) $db->delete('pages', 'revision_id IN (' . implode(',', $revisionIDs) . ')');
			}
		}

		// delete search indexes
		$db->delete('search_index', 'module = ? AND language = ?', array('pages', $to));

		// get all active pages
		$ids = BackendModel::getDB()->getColumn(
			'SELECT id
			 FROM pages AS i
			 WHERE i.language = ? AND i.status = ?',
			array($from, 'active')
		);

		// loop
		foreach($ids as $id)
		{
			// get data
			$sourceData = BackendPagesModel::get($id, null, $from);

			// get and build meta
			$meta = $db->getRecord(
				'SELECT *
				 FROM meta
				 WHERE id = ?',
				array($sourceData['meta_id'])
			);

			// remove id
			unset($meta['id']);

			// build page record
			$page = array();
			$page['id'] = $sourceData['id'];
			$page['user_id'] = BackendAuthentication::getUser()->getUserId();
			$page['parent_id'] = $sourceData['parent_id'];
			$page['template_id'] = $sourceData['template_id'];
			$page['meta_id'] = (int) $db->insert('meta', $meta);
			$page['language'] = $to;
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

			// init var
			$blocks = array();
			$hasBlock = ($sourceData['has_extra'] == 'Y');

			// get the blocks
			$sourceBlocks = BackendPagesModel::getBlocks($id, null, $from);

			// loop blocks
			foreach($sourceBlocks as $sourceBlock)
			{
				// build block
				$block = $sourceBlock;
				$block['revision_id'] = $revisionId;
				$block['created_on'] = BackendModel::getUTCDate();
				$block['edited_on'] = BackendModel::getUTCDate();

				// add block
				$blocks[] = $block;
			}

			// insert the blocks
			BackendPagesModel::insertBlocks($blocks, $hasBlock);

			// check if the method exists
			if(method_exists('BackendSearchModel', 'saveIndex'))
			{
				// init var
				$text = '';

				// build search-text
				foreach($blocks as $block) $text .= ' ' . $block['html'];

				// add
				BackendSearchModel::saveIndex('pages', (int) $page['id'], array('title' => $page['title'], 'text' => $text), $to);
			}

			// get tags
			$tags = BackendTagsModel::getTags('pages', $id, 'string', $from);

			// save tags
			if($tags != '') BackendTagsModel::saveTags($page['id'], $tags, 'pages');
		}

		// build cache
		BackendPagesModel::buildCache($to);
	}
}
