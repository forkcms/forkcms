<?php

/**
 * All model functions for the testimonials module.
 *
 * @package		backend
 * @subpackage	testimonials
 *
 * @author		Jan Moesen <jan@netlash.com>
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.1
 */
class BackendTestimonialsModel
{
	/**
	 * Overview of the items.
	 *
	 * @var	string
	 */
	const QRY_BROWSE = 'SELECT id, name, sequence
	                    FROM testimonials
	                    WHERE language = ?
	                    ORDER BY sequence';


	/**
	 * Delete a testimonial.
	 *
	 * @return	void
	 * @param	int $id			The id of the testimonial to delete.
	 */
	public static function delete($id)
	{
		BackendModel::getDB(true)->delete('testimonials', 'id = ?', array((int) $id));
	}


	/**
	 * Does the testimonial exist?
	 *
	 * @return	bool
	 * @param	int $id			The id of the testimonial to check for existence.
	 */
	public static function exists($id)
	{
		return (bool) BackendModel::getDB()->getVar('SELECT COUNT(id)
														FROM testimonials
														WHERE id = ?',
														array((int) $id));
	}


	/**
	 * Get all data for the testimonial with the given ID.
	 *
	 * @return	array
	 * @param	int $id			The id for the testimonial to get.
	 */
	public static function get($id)
	{
		return (array) BackendModel::getDB()->getRecord('SELECT *, UNIX_TIMESTAMP(created_on) AS created_on, UNIX_TIMESTAMP(edited_on) AS edited_on
		                                                 FROM testimonials
		                                                 WHERE id = ?
		                                                 LIMIT 1', array((int) $id));
	}


	/**
	 * Get the max sequence id for a testimonial
	 *
	 * @return	int
	 */
	public static function getMaximumSequence()
	{
		return (int) BackendModel::getDB()->getVar('SELECT MAX(sequence)
													FROM testimonials');
	}


	/**
	 * Add a new testimonial.
	 *
	 * @return	int				The ID of the newly inserted testimonial.
	 * @param	array $item		The data to insert.
	 */
	public static function insert(array $item)
	{
		// return the ID
		return BackendModel::getDB(true)->insert('testimonials', $item);
	}


	/**
	 * Update an existing testimonial.
	 *
	 * @return	int
	 * @param	array $item		The new data.
	 */
	public static function update(array $item)
	{
		// update the record
		return BackendModel::getDB(true)->update('testimonials', $item, 'id = ?', array((int) $item['id']));
	}
}

?>