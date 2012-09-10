<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the createwidgets-action, it will create a widget for already existing categories
 *
 * @author John Poelman <john.poelman@bloobz.be>
 */
class BackendBlogCreateWidgets extends BackendBaseActionIndex
{
	/*
	 * get the data
	 */
	private function createExtras()
	{
		// get all available categories
		$this->categories = BackendBlogModel::getCategories();
		
		// category IDs
		$category_ids = array_keys($this->categories);
		
		// loop the categories and add the extras
		foreach($category_ids AS $id)
		{
			// get all category data
			$cat = BackendBlogModel::getCategory($id);			

			if($cat['extra_id'] === NULL)
			{
				// db connection
				$db = BackendModel::getDB(true);

				// build widget stuff
				$extra = array(
					'module' => 'blog', 
					'type' => 'widget', 
					'label' => 'blog', 
					'action' => 'posts_per_category', 
					'data' => serialize(array(
								'id' => $cat['id'], 
								'extra_label' => $cat['title'], 
								'language' => BL::getWorkingLanguage(), 
								'edit_url' => BackendModel::createURLForAction('edit') . '&id=' . $cat['id']
					)),
					'hidden' => 'N',
					'sequence' => $db->getVar(
						'SELECT MAX(i.sequence) + 1
						 FROM modules_extras AS i
						 WHERE i.module = ?', 
						array('blog')
				));

				// insert the widget
				$item['extra_id'] = $db->insert('modules_extras', $extra);

				// build category item
				$item['id'] = $cat['id'];
				$item['meta_id'] = $cat['meta_id'];
				$item['language'] = $cat['language'];
				$item['title'] = $cat['title'];

				// update the category
				BackendBlogModel::updateCategory($item);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_edit_category', array('item' => $item));
			}

			else 
			{
				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('categories') . '&error=something-went-wrong');
			}
		}

		// blog is updated
		$this->redirect(BackendModel::createURLForAction('categories') . '&report=edited-category');
	}

	/**
	 * Execute the action
	 */
	public function execute()
	{
		// execute the parent
		parent::execute();
		
		// update the database
		$this->updateDB();
		
		// create the extras
		$this->createExtras();
		
		// parse
		$this->parse();
	}

	/*
	 * update the database
	 */
	private function updateDB()
	{	
		// get array with all fields
		$columns = BackendModel::getDB()->retrieve('SHOW COLUMNS FROM blog_categories');
		
		// loop $columns and add fieldnames to $fields
		foreach($columns as $c)
		{
			$fields[] = $c['Field'];
		}
		
		// create the table if it doesn't exist
		if(!in_array('extra_id', $fields))
		{
			// alter the blog_categories table
			BackendModel::getDB()->execute('ALTER TABLE blog_categories ADD extra_id int(11) AFTER id');
		}
	}
}