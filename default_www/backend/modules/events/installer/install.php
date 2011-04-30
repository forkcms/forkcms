<?php

/**
 * Installer for the events module
 *
 * @package		installer
 * @subpackage	events
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.0
 */
class EventsInstall extends ModuleInstaller
{
	/**
	 * Add the default category for a language
	 *
	 * @return	int
	 * @param	string $language	The language to use.
	 * @param	string $title		The title of the category.
	 * @param	string $url			The URL for the category.
	 */
	private function addCategory($language, $title, $url)
	{
		// build array
		$item['meta_id'] = $this->insertMeta($title, $title, $title, $url);
		$item['language'] = (string) $language;
		$item['title'] = (string) $title;

		return (int) $this->getDB()->insert('events_categories', $item);
	}


	/**
	 * Install the module
	 *
	 * @return	void
	 */
	protected function execute()
	{
		// load install.sql
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// add 'events' as a module
		$this->addModule('events', 'The events module.');

		// general settings
		$this->setSetting('events', 'allow_comments', true);
		$this->setSetting('events', 'allow_subscriptions', true);
		$this->setSetting('events', 'requires_akismet', true);
		$this->setSetting('events', 'spamfilter', false);
		$this->setSetting('events', 'moderation', true);
		$this->setSetting('events', 'ping_services', true);
		$this->setSetting('events', 'overview_num_items', 10);
		$this->setSetting('events', 'max_num_revisions', 20);
		$this->setSetting('events', 'notify_by_email_on_new_comment_to_moderate', true);
		$this->setSetting('events', 'notify_by_email_on_new_comment', true);
		$this->setSetting('events', 'notify_by_email_on_new_subscription', true);

		// make module searchable
		$this->makeSearchable('events');

		// module rights
		$this->setModuleRights(1, 'events');

		// action rights
		$this->setActionRights(1, 'events', 'categories');
		$this->setActionRights(1, 'events', 'add_category');
		$this->setActionRights(1, 'events', 'edit_category');
		$this->setActionRights(1, 'events', 'delete_category');
		$this->setActionRights(1, 'events', 'add_category');
		$this->setActionRights(1, 'events', 'index');
		$this->setActionRights(1, 'events', 'add');
		$this->setActionRights(1, 'events', 'edit');
		$this->setActionRights(1, 'events', 'delete');
		$this->setActionRights(1, 'events', 'comments');
		$this->setActionRights(1, 'events', 'edit_comment');
		$this->setActionRights(1, 'events', 'delete_spam');
		$this->setActionRights(1, 'events', 'mass_comment_action');
		$this->setActionRights(1, 'events', 'settings');

		// add extra's
		$eventsID = $this->insertExtra('events', 'block', 'Events', null, null, 'N', 5000);
		$this->insertExtra('events', 'widget', 'RecentComments', 'recent_comments', null, 'N', 5001);
		$this->insertExtra('events', 'widget', 'Categories', 'categories', null, 'N', 5002);
		$this->insertExtra('events', 'widget', 'Archive', 'archive', null, 'N', 5003);

		// loop languages
		foreach($this->getLanguages() as $language)
		{
			// fetch current categoryId
			$currentCategoryId = $this->getCategory($language);

			// no category exists
			if($currentCategoryId == 0)
			{
				// add default category
				$defaultCategoryId = $this->addCategory($language, 'Default', 'default');

				// insert default category setting
				$this->setSetting('events', 'default_category_' . $language, $defaultCategoryId, true);
			}

			// category exists
			else
			{
				// current default categoryId
				$currentDefaultCategoryId = $this->getSetting('events', 'default_category_' . $language);

				// does not exist
				if(!$this->existsCategory($language, $currentDefaultCategoryId))
				{
					// insert default category setting
					$this->setSetting('events', 'default_category_' . $language, $currentCategoryId, true);
				}
			}

			// feedburner URL
			$this->setSetting('events', 'feedburner_url_' . $language, '');

			// RSS settings
			$this->setSetting('events', 'rss_meta_' . $language, true);
			$this->setSetting('events', 'rss_title_' . $language, 'RSS');
			$this->setSetting('events', 'rss_description_' . $language, '');

			// check if a page for events already exists in this language
			if(!(bool) $this->getDB()->getVar('SELECT COUNT(p.id)
												FROM pages AS p
												INNER JOIN pages_blocks AS b ON b.revision_id = p.revision_id
												WHERE b.extra_id = ? AND p.language = ?',
												array($eventsID, $language)))
			{
				// insert page
				$this->insertPage(array('title' => 'Events',
										'language' => $language),
									null,
									array('extra_id' => $eventsID));
			}

			// install example data if requested
			if($this->installExample()) $this->installExampleData($language);
		}

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');
	}


	/**
	 * Does the category with this id exist within this language.
	 *
	 * @return	bool
	 * @param	string $language	The langauge to use.
	 * @param	int $id				The id to exclude.
	 */
	private function existsCategory($language, $id)
	{
		return (bool) $this->getDB()->getVar('SELECT COUNT(id) FROM events_categories WHERE id = ? AND language = ?', array((int) $id, (string) $language));
	}


	/**
	 * Fetch the id of the first category in this language we come across
	 *
	 * @return	int
	 * @param	string $language	The language to use.
	 */
	private function getCategory($language)
	{
		return (int) $this->getDB()->getVar('SELECT id FROM events_categories WHERE language = ?', array((string) $language));
	}


	/**
	 * Install example data
	 *
	 * @return	void
	 * @param	string $language	The language to use.
	 */
	private function installExampleData($language)
	{
		// get db instance
		$db = $this->getDB();

		// check if eventsposts already exist in this language
		if(!(bool) $db->getVar('SELECT COUNT(id) FROM events WHERE language = ?', array($language)))
		{
			// insert sample eventspost 1
			$db->insert('events', array('id' => 1,
											'category_id' => $this->getSetting('events', 'default_category_' . $language),
											'user_id' => $this->getDefaultUserID(),
											'meta_id' => $this->insertMeta('Nunc sediam est', 'Nunc sediam est', 'Nunc sediam est', 'nunc-sediam-est'),
											'language' => $language,
											'starts_on' => gmdate('Y-06-20 11:24:00'),
											'title' => 'Nunc sediam est',
											'introduction' => SpoonFile::getContent(PATH_WWW . '/backend/modules/events/installer/data/' . $language . '/sample1.txt'),
											'text' => SpoonFile::getContent(PATH_WWW . '/backend/modules/events/installer/data/' . $language . '/sample1.txt'),
											'status' => 'active',
											'publish_on' => gmdate('Y-m-d H:i:00'),
											'created_on' => gmdate('Y-m-d H:i:00'),
											'edited_on' => gmdate('Y-m-d H:i:00'),
											'hidden' => 'N',
											'allow_comments' => 'Y',
											'num_comments' => '0',
											'allow_subscriptions' => 'N',
											'max_subscriptions' => null
										));

			// insert sample eventspost 2
			$db->insert('events', array('id' => 2,
											'category_id' => $this->getSetting('events', 'default_category_' . $language),
											'user_id' => $this->getDefaultUserID(),
											'meta_id' => $this->insertMeta('Lorem ipsum', 'Lorem ipsum', 'Lorem ipsum', 'lorem-ipsum'),
											'language' => $language,
											'starts_on' => gmdate('Y-10-11 09:i:00'),
											'ends_on' => gmdate('Y-10-11 18:i:00'),
											'title' => 'Lorem ipsum',
											'introduction' => SpoonFile::getContent(PATH_WWW . '/backend/modules/events/installer/data/' . $language . '/sample1.txt'),
											'text' => SpoonFile::getContent(PATH_WWW . '/backend/modules/events/installer/data/' . $language . '/sample1.txt'),
											'status' => 'active',
											'publish_on' => gmdate('Y-m-d H:i:00', (time() - 60)),
											'created_on' => gmdate('Y-m-d H:i:00', (time() - 60)),
											'edited_on' => gmdate('Y-m-d H:i:00', (time() - 60)),
											'hidden' => 'N',
											'allow_comments' => 'Y',
											'num_comments' => '0',
											'allow_subscriptions' => 'N',
											'max_subscriptions' => null
										));

			// insert example comment 1
			$db->insert('events_comments', array('event_id' => 1,
												'language' => $language,
												'created_on' => gmdate('Y-m-d H:i:00'),
												'author' => 'Matthias Mullie',
												'email' => 'matthias@spoon-library.com',
												'website' => 'http://www.anantasoft.com',
												'text' => 'cool!',
												'type' => 'comment',
												'status' => 'published',
												'data' => null));

			// insert example comment 2
			$db->insert('events_comments', array('event_id' => 1,
												'language' => $language,
												'created_on' => gmdate('Y-m-d H:i:00'),
												'author' => 'Davy Hellemans',
												'email' => 'davy@spoon-library.com',
												'website' => 'http://www.spoon-library.com',
												'text' => 'awesome!',
												'type' => 'comment',
												'status' => 'published',
												'data' => null));

			// insert example comment 3
			$db->insert('events_comments', array('event_id' => 1,
												'language' => $language,
												'created_on' => gmdate('Y-m-d H:i:00'),
												'author' => 'Tijs Verkoyen',
												'email' => 'tijs@spoon-library.com',
												'website' => 'http://www.sumocoders.be',
												'text' => 'wicked!',
												'type' => 'comment',
												'status' => 'published',
												'data' => null));
		}
	}
}

?>