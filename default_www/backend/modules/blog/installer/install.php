<?php

/**
 * Installer for the blog module
 *
 * @package		installer
 * @subpackage	blog
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.0
 */
class BlogInstall extends ModuleInstaller
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

		return (int) $this->getDB()->insert('blog_categories', $item);
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

		// add 'blog' as a module
		$this->addModule('blog', 'The blog module.');

		// general settings
		$this->setSetting('blog', 'allow_comments', true);
		$this->setSetting('blog', 'requires_akismet', true);
		$this->setSetting('blog', 'spamfilter', false);
		$this->setSetting('blog', 'moderation', true);
		$this->setSetting('blog', 'ping_services', true);
		$this->setSetting('blog', 'overview_num_items', 10);
		$this->setSetting('blog', 'recent_articles_full_num_items', 3);
		$this->setSetting('blog', 'recent_articles_list_num_items', 5);
		$this->setSetting('blog', 'max_num_revisions', 20);

		// make module searchable
		$this->makeSearchable('blog');

		// module rights
		$this->setModuleRights(1, 'blog');

		// action rights
		$this->setActionRights(1, 'blog', 'add_category');
		$this->setActionRights(1, 'blog', 'add');
		$this->setActionRights(1, 'blog', 'categories');
		$this->setActionRights(1, 'blog', 'comments');
		$this->setActionRights(1, 'blog', 'delete_category');
		$this->setActionRights(1, 'blog', 'delete_spam');
		$this->setActionRights(1, 'blog', 'delete');
		$this->setActionRights(1, 'blog', 'edit_category');
		$this->setActionRights(1, 'blog', 'edit_comment');
		$this->setActionRights(1, 'blog', 'edit');
		$this->setActionRights(1, 'blog', 'import_blogger');
		$this->setActionRights(1, 'blog', 'index');
		$this->setActionRights(1, 'blog', 'mass_comment_action');
		$this->setActionRights(1, 'blog', 'settings');

		// add extra's
		$blogID = $this->insertExtra('blog', 'block', 'Blog', null, null, 'N', 1000);
		$this->insertExtra('blog', 'widget', 'RecentComments', 'recent_comments', null, 'N', 1001);
		$this->insertExtra('blog', 'widget', 'Categories', 'categories', null, 'N', 1002);
		$this->insertExtra('blog', 'widget', 'Archive', 'archive', null, 'N', 1003);
		$this->insertExtra('blog', 'widget', 'RecentArticlesFull', 'recent_articles_full', null, 'N', 1004);
		$this->insertExtra('blog', 'widget', 'RecentArticlesList', 'recent_articles_list', null, 'N', 1005);

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
				$this->setSetting('blog', 'default_category_' . $language, $defaultCategoryId, true);
			}

			// category exists
			else
			{
				// current default categoryId
				$currentDefaultCategoryId = $this->getSetting('blog', 'default_category_' . $language);

				// does not exist
				if(!$this->existsCategory($language, $currentDefaultCategoryId))
				{
					// insert default category setting
					$this->setSetting('blog', 'default_category_' . $language, $currentCategoryId, true);
				}
			}

			// feedburner URL
			$this->setSetting('blog', 'feedburner_url_' . $language, '');

			// RSS settings
			$this->setSetting('blog', 'rss_meta_' . $language, true);
			$this->setSetting('blog', 'rss_title_' . $language, 'RSS');
			$this->setSetting('blog', 'rss_description_' . $language, '');


			// check if a page for blog already exists in this language
			if(!(bool) $this->getDB()->getVar('SELECT COUNT(p.id)
												FROM pages AS p
												INNER JOIN pages_blocks AS b ON b.revision_id = p.revision_id
												WHERE b.extra_id = ? AND p.language = ?',
												array($blogID, $language)))
			{
				// insert page
				$this->insertPage(array('title' => 'Blog',
										'language' => $language),
									null,
									array('extra_id' => $blogID));
			}

			// install example data if requested
			if($this->installExample()) $this->installExampleData($language);
		}


		// insert locale (nl)
		$this->insertLocale('nl', 'backend', 'blog', 'err', 'RSSDescription', 'Blog RSS beschrijving is nog niet geconfigureerd. <a href="%1$s">Configureer</a>');
		$this->insertLocale('nl', 'backend', 'blog', 'lbl', 'Add', 'artikel toevoegen');
		$this->insertLocale('nl', 'backend', 'blog', 'msg', 'Added', 'Het artikel "%1$s" werd toegevoegd.');
		$this->insertLocale('nl', 'backend', 'blog', 'msg', 'CommentOnWithURL', 'Reactie op: <a href="%1$s">%2$s</a>');
		$this->insertLocale('nl', 'backend', 'blog', 'msg', 'ConfirmDelete', 'Ben je zeker dat je het artikel "%1$s" wil verwijderen?');
		$this->insertLocale('nl', 'backend', 'blog', 'msg', 'Deleted', 'De geselecteerde artikels werden verwijderd.');
		$this->insertLocale('nl', 'backend', 'blog', 'msg', 'DeletedSpam', 'Alle spamberichten werden verwijderd.');
		$this->insertLocale('nl', 'backend', 'blog', 'msg', 'DeleteAllSpam', 'Alle spam verwijderen:');
		$this->insertLocale('nl', 'backend', 'blog', 'msg', 'EditArticle', 'bewerk artikel "%1$s"');
		$this->insertLocale('nl', 'backend', 'blog', 'msg', 'EditCommentOn', 'bewerk reactie op "%1$s"');
		$this->insertLocale('nl', 'backend', 'blog', 'msg', 'Edited', 'Het artikel "%1$s" werd opgeslagen.');
		$this->insertLocale('nl', 'backend', 'blog', 'msg', 'EditedComment', 'De reactie werd opgeslagen.');
		$this->insertLocale('nl', 'backend', 'blog', 'msg', 'FollowAllCommentsInRSS', 'Volg alle reacties in een RSS feed: <a href="%1$s">%1$s</a>.');
		$this->insertLocale('nl', 'backend', 'blog', 'msg', 'HelpMeta', 'Toon de meta informatie van deze blogpost in de RSS feed (categorie, tags, ...)');
		$this->insertLocale('nl', 'backend', 'blog', 'msg', 'HelpPingServices', 'Laat verschillende blogservices weten wanneer je een nieuw bericht plaatst.');
		$this->insertLocale('nl', 'backend', 'blog', 'msg', 'HelpSummary', 'Maak voor lange artikels een inleiding of samenvatting. Die kan getoond worden op de homepage of het artikeloverzicht.');
		$this->insertLocale('nl', 'backend', 'blog', 'msg', 'HelpSpamFilter', 'Schakel de ingebouwde spam-filter (Akismet) in om spam-berichten in reacties te vermijden.');
		$this->insertLocale('nl', 'backend', 'blog', 'msg', 'MakeDefaultCategory', 'Maak van deze categorie de standaardcategorie (de huidige standaardcategorie is %1$s).');
		$this->insertLocale('nl', 'backend', 'blog', 'msg', 'NoItems', 'Er zijn nog geen artikels. <a href="%1$s">Schrijf het eerste artikel</a>.');
		$this->insertLocale('nl', 'backend', 'blog', 'msg', 'NotifyByEmailOnNewComment', 'Verwittig via email als er een nieuwe reactie is.');
		$this->insertLocale('nl', 'backend', 'blog', 'msg', 'NotifyByEmailOnNewCommentToModerate', 'Verwittig via email als er een nieuwe reactie te modereren is.');
		$this->insertLocale('nl', 'backend', 'blog', 'msg', 'NumItemsInRecentArticlesFull', 'Aantal items in recente artikels (volledig) widget');
		$this->insertLocale('nl', 'backend', 'blog', 'msg', 'NumItemsInRecentArticlesList', 'Aantal items in recente artikels (lijst) widget');
		$this->insertLocale('nl', 'frontend', 'core', 'act', 'ArticleCommentsRss', 'reacties-op-rss');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'ArticlesInCategory', 'artikels in categorie');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'InTheCategory', 'in de categorie');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'SubscribeToTheRSSFeed', 'schrijf je in op de RSS-feed');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'BlogArchive', 'blogarchief');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'NextArticle', 'volgend bericht');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'PreviousArticle', 'vorig bericht');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'RecentArticles', 'recente artikels');
		$this->insertLocale('nl', 'frontend', 'core', 'lbl', 'Wrote', 'schreef');
		$this->insertLocale('nl', 'frontend', 'core', 'msg', 'BlogAllComments', 'Alle reacties op je blog.');
		$this->insertLocale('nl', 'frontend', 'core', 'msg', 'BlogNoComments', 'Reageer als eerste');
		$this->insertLocale('nl', 'frontend', 'core', 'msg', 'BlogNumberOfComments', 'Al %1$s reacties');
		$this->insertLocale('nl', 'frontend', 'core', 'msg', 'BlogOneComment', 'Al 1 reactie');
		$this->insertLocale('nl', 'frontend', 'core', 'msg', 'BlogCommentIsAdded', 'Je reactie werd toegevoegd.');
		$this->insertLocale('nl', 'frontend', 'core', 'msg', 'BlogCommentInModeration', 'Je reactie wacht op goedkeuring.');
		$this->insertLocale('nl', 'frontend', 'core', 'msg', 'BlogCommentIsSpam', 'Je reactie werd gemarkeerd als spam.');
		$this->insertLocale('nl', 'frontend', 'core', 'msg', 'BlogEmailNotificationsNewComment', '%1$s reageerde op <a href="%2$s">%3$s</a>.');
		$this->insertLocale('nl', 'frontend', 'core', 'msg', 'BlogEmailNotificationsNewCommentToModerate', '%1$s reageerde op <a href="%2$s">%3$s</a>. <a href="%4$s">Modereer</a> deze reactie om ze zichtbaar te maken op de website.');
		$this->insertLocale('nl', 'frontend', 'core', 'msg', 'BlogNoItems', 'Er zijn nog geen blogposts.');

		// insert locale (en)
		$this->insertLocale('en', 'backend', 'blog', 'err', 'RSSDescription', 'Blog RSS description is not yet provided. <a href="%1$s">Configure</a>');
		$this->insertLocale('en', 'backend', 'blog', 'lbl', 'Add', 'add article');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'Added', 'The article "%1$s" was added.');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'CommentOnWithURL', 'Comment on: <a href="%1$s">%2$s</a>');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'ConfirmDelete', 'Are your sure you want to delete the article "%1$s"?');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'Deleted', 'The selected articles were deleted.');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'DeletedSpam', 'All spam-comments were deleted.');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'DeleteAllSpam', 'Delete all spam:');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'EditArticle', 'edit article "%1$s"');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'EditCommentOn', 'edit comment on "%1$s"');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'Edited', 'The article "%1$s" was saved.');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'EditedComment', 'The comment was saved.');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'FollowAllCommentsInRSS', 'Follow all comments in a RSS feed: <a href="%1$s">%1$s</a>.');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'HelpMeta', 'Show the meta information for this blogpost in the RSS feed (category, tags, ...)');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'HelpPingServices', 'Let various blogservices know when you\'ve posted a new article.');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'HelpSummary', 'Write an introduction or summary for long articles. It will be shown on the homepage or the article overview.');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'HelpSpamFilter', 'Enable the built-in spamfilter (Akismet) to help avoid spam comments.');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'NoItems', 'There are no articles yet. <a href="%1$s">Write the first article</a>.');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'NotifyByEmailOnNewComment', 'Notify by email when there is a new comment.');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'NotifyByEmailOnNewCommentToModerate', 'Notify by email when there is a new comment to moderate.');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'NumItemsInRecentArticlesFull', 'Number of articles in the recent articles (full) widget');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'NumItemsInRecentArticlesList', 'Number of articles in the recent articles (list) widget');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'MakeDefaultCategory', 'Make default category (current default category is: %1$s).');
		$this->insertLocale('en', 'frontend', 'core', 'act', 'ArticleCommentsRss', 'comments-on-rss');
		$this->insertLocale('en', 'frontend', 'core', 'lbl', 'ArticlesInCategory', 'articles in category');
		$this->insertLocale('en', 'frontend', 'core', 'lbl', 'InTheCategory', 'in category');
		$this->insertLocale('en', 'frontend', 'core', 'lbl', 'SubscribeToTheRSSFeed', 'subscribe to the RSS feed');
		$this->insertLocale('en', 'frontend', 'core', 'lbl', 'BlogArchive', 'blog archive');
		$this->insertLocale('en', 'frontend', 'core', 'lbl', 'NextArticle', 'next article');
		$this->insertLocale('en', 'frontend', 'core', 'lbl', 'PreviousArticle', 'previous article');
		$this->insertLocale('en', 'frontend', 'core', 'lbl', 'RecentArticles', 'recent articles');
		$this->insertLocale('en', 'frontend', 'core', 'lbl', 'Wrote', 'wrote');
		$this->insertLocale('en', 'frontend', 'core', 'msg', 'BlogAllComments', 'All comments on your blog.');
		$this->insertLocale('en', 'frontend', 'core', 'msg', 'BlogNoComments', 'Be the first to comment');
		$this->insertLocale('en', 'frontend', 'core', 'msg', 'BlogNumberOfComments', '%1$s comments');
		$this->insertLocale('en', 'frontend', 'core', 'msg', 'BlogOneComment', '1 comment already');
		$this->insertLocale('en', 'frontend', 'core', 'msg', 'BlogCommentIsAdded', 'Your comment was added.');
		$this->insertLocale('en', 'frontend', 'core', 'msg', 'BlogCommentInModeration', 'Your comment is awaiting moderation.');
		$this->insertLocale('en', 'frontend', 'core', 'msg', 'BlogCommentIsSpam', 'Your comment was marked as spam.');
		$this->insertLocale('en', 'frontend', 'core', 'msg', 'BlogEmailNotificationsNewComment', '%1$s commented on <a href="%2$s">%3$s</a>.');
		$this->insertLocale('en', 'frontend', 'core', 'msg', 'BlogEmailNotificationsNewCommentToModerate', '%1$s commented on <a href="%2$s">%3$s</a>. <a href="%4$s">Moderate</a> the comment to publish it.');
		$this->insertLocale('en', 'frontend', 'core', 'msg', 'BlogNoItems', 'There are no articles yet.');
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
		return (bool) $this->getDB()->getVar('SELECT COUNT(id) FROM blog_categories WHERE id = ? AND language = ?', array((int) $id, (string) $language));
	}


	/**
	 * Fetch the id of the first category in this language we come across
	 *
	 * @return	int
	 * @param	string $language	The language to use.
	 */
	private function getCategory($language)
	{
		return (int) $this->getDB()->getVar('SELECT id FROM blog_categories WHERE language = ?', array((string) $language));
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

		// check if blogposts already exist in this language
		if(!(bool) $db->getVar('SELECT COUNT(id) FROM blog_posts WHERE language = ?', array($language)))
		{
			// insert sample blogpost 1
			$db->insert('blog_posts', array('id' => 1,
											'category_id' => $this->getSetting('blog', 'default_category_' . $language),
											'user_id' => $this->getDefaultUserID(),
											'meta_id' => $this->insertMeta('Nunc sediam est', 'Nunc sediam est', 'Nunc sediam est', 'nunc-sediam-est'),
											'language' => $language,
											'title' => 'Nunc sediam est',
											'introduction' => SpoonFile::getContent(PATH_WWW . '/backend/modules/blog/installer/data/' . $language . '/sample1.txt'),
											'text' => SpoonFile::getContent(PATH_WWW . '/backend/modules/blog/installer/data/' . $language . '/sample1.txt'),
											'status' => 'active',
											'publish_on' => gmdate('Y-m-d H:i:00'),
											'created_on' => gmdate('Y-m-d H:i:00'),
											'edited_on' => gmdate('Y-m-d H:i:00'),
											'hidden' => 'N',
											'allow_comments' => 'Y',
											'num_comments' => '3'));

			// insert sample blogpost 2
			$db->insert('blog_posts', array('id' => 2,
											'category_id' => $this->getSetting('blog', 'default_category_' . $language),
											'user_id' => $this->getDefaultUserID(),
											'meta_id' => $this->insertMeta('Lorem ipsum', 'Lorem ipsum', 'Lorem ipsum', 'lorem-ipsum'),
											'language' => $language,
											'title' => 'Lorem ipsum',
											'introduction' => SpoonFile::getContent(PATH_WWW . '/backend/modules/blog/installer/data/' . $language . '/sample1.txt'),
											'text' => SpoonFile::getContent(PATH_WWW . '/backend/modules/blog/installer/data/' . $language . '/sample1.txt'),
											'status' => 'active',
											'publish_on' => gmdate('Y-m-d H:i:00', (time() - 60)),
											'created_on' => gmdate('Y-m-d H:i:00', (time() - 60)),
											'edited_on' => gmdate('Y-m-d H:i:00', (time() - 60)),
											'hidden' => 'N',
											'allow_comments' => 'Y',
											'num_comments' => '0'));

			// insert example comment 1
			$db->insert('blog_comments', array('post_id' => 1,
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
			$db->insert('blog_comments', array('post_id' => 1,
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
			$db->insert('blog_comments', array('post_id' => 1,
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
