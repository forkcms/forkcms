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
 */
class FrontendFaqModel
{
	/**
	 * Get all categories
	 *
	 * @return array
	 */
	public static function getCategories()
	{
		return (array) FrontendModel::getDB()->getRecords(
			'SELECT i.*
			 FROM faq_categories AS i
			 WHERE i.language = ?
			 ORDER BY i.sequence',
			array(FRONTEND_LANGUAGE)
		);
	}

	/**
	 * Get all items in a category
	 *
	 * @param int $categoryId The id of the category.
	 * @return array
	 */
	public static function getQuestions($categoryId)
	{
		return (array) FrontendModel::getDB()->getRecords(
			'SELECT i.*
			 FROM faq_questions AS i
			 WHERE i.category_id = ? AND i.language = ? AND i.hidden = ?
			 ORDER BY i.sequence',
			array((int) $categoryId, FRONTEND_LANGUAGE, 'N')
		);
	}
}
