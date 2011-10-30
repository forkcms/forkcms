<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using in the faq module
 *
 * @author Lester Lievens <lester@netlash.com>
 * @author Matthias Mullie <matthias@mullie.eu>
 */
class BackendFaqModel
{
	const QRY_DATAGRID_BROWSE =
		'SELECT i.id, i.category_id, i.question, i.hidden, i.sequence
		 FROM faq_questions AS i
		 WHERE i.language = ? AND i.category_id = ?
		 ORDER BY i.sequence ASC';

	const QRY_DATAGRID_BROWSE_CATEGORIES =
		'SELECT i.id, i.name, i.sequence
		 FROM faq_categories AS i
		 WHERE i.language = ?
		 ORDER BY i.sequence ASC';

	/**
	 * Delete a category
	 *
	 * @param int $id The id of the category to be deleted.
	 */
	public static function deleteCategory($id)
	{
		$db = BackendModel::getDB(true);
		$item = self::getCategory($id);

		// build extra
		$extra = array(
			'id' => $item['extra_id'],
			'module' => 'faq',
			'type' => 'block',
			'action' => 'category'
		);

		// delete extra
		$db->delete('pages_extras', 'id = ? AND module = ? AND type = ? AND action = ?', array($extra['id'], $extra['module'], $extra['type'], $extra['action']));

		// delete the record
		$db->delete('faq_categories', 'id = ?', array((int) $id));
	}

	/**
	 * Delete a question
	 *
	 * @param int $id The id of the question to be deleted.
	 */
	public static function deleteQuestion($id)
	{
		BackendModel::getDB(true)->delete('faq_questions', 'id = ?', array((int) $id));
	}

	/**
	 * Checks if a category exists
	 *
	 * @param int $id The id of the category to check for existence.
	 * @return bool
	 */
	public static function existsCategory($id)
	{
		return (bool) BackendModel::getDB()->getVar(
			'SELECT COUNT(i.id)
			 FROM faq_categories AS i
			 WHERE i.id = ? AND i.language = ?',
			array((int) $id, BL::getWorkingLanguage())
		);
	}

	/**
	 * Checks if a question exists
	 *
	 * @param int $id The id of the question to check for existence.
	 * @return bool
	 */
	public static function existsQuestion($id)
	{
		return (bool) BackendModel::getDB()->getVar(
			'SELECT COUNT(i.id)
			 FROM faq_questions AS i
			 WHERE i.id = ? AND i.language = ?',
			array((int) $id, BL::getWorkingLanguage())
		);
	}

	/**
	 * Get all categories
	 *
	 * @return array
	 */
	public static function getCategories()
	{
		return (array) BackendModel::getDB()->getRecords(
			'SELECT i.*
			 FROM faq_categories AS i
			 WHERE i.language = ?
			 ORDER BY i.sequence ASC',
			array(BL::getWorkingLanguage())
		);
	}

	/**
	 * Get all category names for dropdown
	 *
	 * @return array
	 */
	public static function getCategoriesForDropdown()
	{
		return (array) BackendModel::getDB()->getPairs(
			'SELECT i.id, i.name
			 FROM faq_categories AS i
			 WHERE i.language = ?
			 ORDER BY i.sequence ASC',
			array(BL::getWorkingLanguage())
		);
	}

	/**
	 * Get category by id
	 *
	 * @param int $id The id of the category.
	 * @return array
	 */
	public static function getCategory($id)
	{
		return (array) BackendModel::getDB()->getRecord(
			'SELECT i.*
			 FROM faq_categories AS i
			 WHERE i.id = ?',
			array((int) $id)
		);
	}

	/**
	 * Get the max sequence id for category
	 *
	 * @return int
	 */
	public static function getMaximumCategorySequence()
	{
		return (int) BackendModel::getDB()->getVar(
			'SELECT MAX(i.sequence) FROM faq_categories AS i'
		);
	}

	/**
	 * Get the max sequence id for question
	 *
	 * @param int $id The category id.
	 * @return int
	 */
	public static function getMaximumQuestionSequence($id)
	{
		return (int) BackendModel::getDB()->getVar(
			'SELECT MAX(i.sequence)
			 FROM faq_questions AS i
			 WHERE i.category_id = ?',
			array((int) $id)
		);
	}

	/**
	 * Get a question by id
	 *
	 * @param int $id The question id.
	 * @return array
	 */
	public static function getQuestion($id)
	{
		return (array) BackendModel::getDB()->getRecord(
			'SELECT i.*
			 FROM faq_questions AS i
			 WHERE i.id = ?',
			array((int) $id)
		);
	}

	/**
	 * Add a new category.
	 *
	 * @param array $item The data to insert.
	 * @return int
	 */
	public static function insertCategory(array $item)
	{
		$db = BackendModel::getDB(true);

		// build extra
		$extra = array(
			'module' => 'faq',
			'type' => 'block',
			'label' => 'Faq',
			'action' => 'category',
			'data' => null,
			'hidden' => 'N',
			'sequence' => $db->getVar(
				'SELECT MAX(i.sequence) + 1
				 FROM pages_extras AS i
				 WHERE i.module = ?', array('faq')
			)
		);
		if(is_null($extra['sequence'])) $extra['sequence'] = $db->getVar(
			'SELECT CEILING(MAX(i.sequence) / 1000) * 1000
			 FROM pages_extras AS i'
		);

		// insert extra
		$item['extra_id'] = $db->insert('pages_extras', $extra);
		$extra['id'] = $item['extra_id'];

		// insert and return the new id
		$item['id'] = $db->insert('faq_categories', $item);

		// update extra (item id is now known)
		$extra['data'] = serialize(array(
			'id' => $item['id'],
			'extra_label' => ucfirst(BL::lbl('Faq', 'core')) . ': ' . $item['name'],
			'language' => $item['language'],
			'edit_url' => BackendModel::createURLForAction('edit') . '&id=' . $item['id'])
		);
		$db->update('pages_extras', $extra, 'id = ? AND module = ? AND type = ? AND action = ?', array($extra['id'], $extra['module'], $extra['type'], $extra['action']));

		// return the new id
		return $item['id'];
	}

	/**
	 * Add a new question.
	 *
	 * @param array $item The data to insert.
	 * @return int
	 */
	public static function insertQuestion(array $item)
	{
		return BackendModel::getDB(true)->insert('faq_questions', $item);
	}

	/**
	 * Is this category allowed to be deleted?
	 *
	 * @param int $id The category id to check.
	 * @return bool
	 */
	public static function isCategoryAllowedToBeDeleted($id)
	{
		return !(bool) BackendModel::getDB()->getVar(
			'SELECT COUNT(i.id)
			 FROM faq_questions AS i
			 WHERE i.category_id = ?',
			array((int) $id)
		);
	}

	/**
	 * Update a category item
	 *
	 * @param array $item The updated values.
	 * @return int
	 */
	public static function updateCategory(array $item)
	{
		$db = BackendModel::getDB(true);

		// build extra
		$extra = array(
			'id' => $item['extra_id'],
			'module' => 'faq',
			'type' => 'block',
			'label' => 'Faq',
			'action' => 'category',
			'data' => serialize(array(
				'id' => $item['id'],
				'extra_label' => ucfirst(BL::lbl('Faq', 'core')) . ': ' . $item['name'],
				'language' => $item['language'],
				'edit_url' => BackendModel::createURLForAction('edit') . '&id=' . $item['id'])
				),
			'hidden' => 'N'
		);

		// update extra
		$db->update('pages_extras', $extra, 'id = ? AND module = ? AND type = ? AND action = ?', array($extra['id'], $extra['module'], $extra['type'], $extra['action']));

		// update category
		return $db->update('faq_categories', $item, 'id = ? AND language = ?', array($item['id'], $item['language']));
	}

	/**
	 * Update a question item
	 *
	 * @param array $item The updated item.
	 * @return int
	 */
	public static function updateQuestion(array $item)
	{
		return BackendModel::getDB(true)->update('faq_questions', $item, 'id = ?', array((int) $item['id']));
	}
}

