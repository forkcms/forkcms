<?php

/**
 * In this file we store all generic functions that we will be using in the testimonials module
 *
 * @package		frontend
 * @subpackage	testimonials
 *
 * @author		Jan Moesen <jan@netlash.com>
 * @since		2.1
 */
class FrontendTestimonialsModel
{
	/**
	 * Get the visible testimonial with the given ID.
	 *
	 * @return	array
	 * @param	int $id		The id of the item to fetch.
	 */
	public static function get($id)
	{
		return (array) FrontendModel::getDB()->getRecord('SELECT name, testimonial
															FROM testimonials
															WHERE id = ? AND hidden = ?
															LIMIT 1',
															array((int) $id, 'N'));
	}


	/**
	 * Get a random visible testimonial.
	 *
	 * @return	array
	 */
	public static function getRandom()
	{
		// get a random ID
		$allIds = FrontendModel::getDB()->getColumn('SELECT id
														FROM testimonials
														WHERE hidden = ?',
														array('N'));

		// return an empty array when there are no visible testimonials
		if(empty($allIds)) return array();

		// return the testimonial with a random ID
		return self::get($allIds[array_rand($allIds)]);
	}


	/**
	 * Get all testimonials.
	 *
	 * @return	array
	 */
	public static function getAll()
	{
		return (array) FrontendModel::getDB()->getRecords('SELECT *
															FROM testimonials
															WHERE hidden = ?
															ORDER BY sequence',
															array('N'));
	}
}

?>