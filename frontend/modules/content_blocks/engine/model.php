<?php

/**
 * In this file we store all generic functions that we will be using in the content_blocks module
 *
 * @author Dave Lens <dave.lens@netlash.com>
 * @author Tijs verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 */
class FrontendContentBlocksModel
{
	/**
	 * Get an item.
	 *
	 * @param string $id The id of the item to fetch.
	 * @return array
	 */
	public static function get($id)
	{
		return (array) FrontendModel::getContainer()->get('database')->getRecord(
			'SELECT i.*
			 FROM content_blocks AS i
			 WHERE i.id = ? AND i.status = ? AND i.hidden = ? AND i.language = ?',
			array((int) $id, 'active', 'N', FRONTEND_LANGUAGE)
		);
	}
}
