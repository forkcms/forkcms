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
}

?>