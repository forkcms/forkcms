<?php

/**
 * BackendBlogModel
 *
 * In this file we store all generic functions that we will be using in the blog module
 *
 *
 * @package		backend
 * @subpackage	blog
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendBlogModel
{
	const QRY_DATAGRID_BROWSE_CATEGORIES = 'SELECT 
											c.id,
											c.name,
											COUNT(p.id) AS num_posts
											FROM blog_categories AS c
											LEFT OUTER JOIN blog_posts AS p ON p.category_id = c.id
											WHERE c.language = ?
											GROUP BY c.id';
	const QRY_DATAGRID_BROWSE_COMMENTS = 'SELECT id, UNIX_TIMESTAMP(created_on) AS created_on, author, text FROM blog_comments WHERE status = ?;';


	/**
	 * Checks the settings and optionally returns an array with warnings
	 *
	 * @return	array
	 */
	public static function checkSettings()
	{
		// init var
		$warnings = array();

		// blog rss title
		if(BackendModel::getSetting('blog', 'rss_title_'. BL::getWorkingLanguage(), null) == '')
		{
			// add warning
			$warnings[] = array('message' => sprintf(BL::getError('BlogRSSTitle'), BackendModel::createURLForAction('settings', 'blog')));
		}

		// blog rss description
		if(BackendModel::getSetting('blog', 'rss_description_'. BL::getWorkingLanguage(), null) == '')
		{
			// add warning
			$warnings[] = array('message' => sprintf(BL::getError('BlogRSSDescription'), BackendModel::createURLForAction('settings', 'blog')));
		}

		return $warnings;
	}
	
	
	/**
	 * Deletes a category
	 * 
	 * @return	void
	 * @param	int $id
	 */
	public static function deleteCategory($id)
	{
		// get db
		$db = BackendModel::getDB();
		
		// delete category
		$db->delete('blog_categories', 'id = ?', (int) $id);
		
		// default category
		$defaultCategoryId = BackendModel::getSetting('blog', 'default_category_'. BL::getWorkingLanguage(), null);
		
		// update category for the posts that might be in this category
		$db->update('blog_posts', array('category_id' => $defaultCategoryId), 'category_id = ?', (int) $defaultCategoryId);
	}


	/**
	 * Deletes one or more comments
	 *
	 * @return	void
	 * @param	array $ids
	 */
	public static function deleteComments(array $ids)
	{
		// get db
		$db = BackendModel::getDB();

		// update record
		$db->execute('DELETE FROM blog_comments WHERE id IN('. implode(',', $ids) .');');
	}
	
	
	public static function existsCategory($id)
	{
		// get db
		$db = BackendModel::getDB();
		
		// exists?
		return $db->getNumRows('SELECT id FROM blog_categories WHERE id = ?;', (int) $id);
	}
	
	
	/**
	 * Get all data for a given id
	 *
	 * @return	array
	 * @param	int $id
	 */
	public static function getCategory($id)
	{
		// redefine
		$id = (int) $id;

		// get db
		$db = BackendModel::getDB();

		// get record and return it
		return (array) $db->getRecord('SELECT * FROM blog_categories 
										WHERE id = ?;', (int) $id);
	}
	
	
	/**
	 * Retrieve the unique url for a category
	 * 
	 * @return	string
	 * @param	int[optional] $categoryId
	 */
	public static function getURLForCategory($URL, $categoryId = null)
	{
		// redefine URL
		$URL = SpoonFilter::urlise((string) $URL);
		
		// get db
		$db = BackendModel::getDB();
		
		// new category
		if($categoryId === null)
		{
			// get number of categories with this URL
			$number = (int) $db->getNumRows('SELECT c.id FROM blog_categories AS c WHERE c.language = ? AND c.url = ?;', array(BL::getWorkingLanguage(), $URL));
			
			// already exists
			if($number != 0)
			{
				// add  number
				$URL = BackendModel::addNumber($URL);
				
				// try again
				return self::getURLForCategory($URL);
			}
		}
		
		// current category should be excluded
		else
		{
			// get number of items with this url
			$number = (int) $db->getNumRows('SELECT c.id FROM blog_categories AS C WHERE c.language = ? AND c.url = ? AND c.id != ?;', array(BL::getWorkingLanguage(), $URL, $categoryId));

			// already exists
			if($number != 0)
			{
				// add  number
				$URL = BackendModel::addNumber($URL);
				
				// try again
				return self::getURLForCategory($URL, $categoryId);
			}
		}
		
		return $URL;
	}
	
	
	/**
	 * Inserts a new category into the database
	 * 
	 * @return	int
	 * @param	array $item
	 */
	public static function insertCategory(array $item)
	{
		// get db
		$db = BackendModel::getDB();
		
		// create category
		return $db->insert('blog_categories', $item);
	}
	
	
	/**
	 * Update an existing category
	 *
	 * @return	int
	 * @param	int $id
	 * @param	array $item
	 */
	public static function updateCategory($id, array $item)
	{
		// get db
		$db = BackendModel::getDB();
		
		// update category
		$db->update('blog_categories', $item, 'id = ?', (int) $id);
	}
	

	/**
	 * Updates one or more comments' status
	 *
	 * @return	void
	 * @param	array $ids
	 * @param	string $status
	 */
	public static function updateCommentStatuses(array $ids, $status)
	{
		// get db
		$db = BackendModel::getDB();

		// update record
		$db->execute('UPDATE blog_comments SET status = ? WHERE id IN('. implode(',', $ids) .');', $status);
	}
}

?>