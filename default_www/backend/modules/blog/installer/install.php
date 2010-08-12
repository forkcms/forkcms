<?php

/**
 * BlogInstall
 * Installer for the blog module
 *
 * @package		installer
 * @subpackage	blog
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BlogInstall extends ModuleInstaller
{
	/**
	 * Install the module
	 *
	 * @return	void
	 */
	protected function execute()
	{
		// load install.sql
		$this->importSQL(PATH_WWW .'/backend/modules/blog/installer/data/install.sql');

		// add 'blog' as a module
		$this->addModule('blog', 'The blog module.');

		// general settings
		$this->setSetting('blog', 'allow_comments', true);
		$this->setSetting('blog', 'requires_akismet', true);
		$this->setSetting('blog', 'requires_google_maps', false);
		$this->setSetting('blog', 'spamfilter', false);
		$this->setSetting('blog', 'moderation', true);
		$this->setSetting('blog', 'ping_services', true);
		$this->setSetting('blog', 'overview_num_items', 10);
		$this->setSetting('blog', 'recent_articles_num_items', 5);
		$this->setSetting('blog', 'max_num_revisions', 20);

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
				$this->setSetting('blog', 'default_category_'. $language, $defaultCategoryId, true);
			}

			// category exists
			else
			{
				// current default categoryId
				$currentDefaultCategoryId = $this->getSetting('blog', 'default_category_'. $language);

				// does not exist
				if(!$this->existsCategory($language, $currentDefaultCategoryId))
				{
					// insert default category setting
					$this->setSetting('blog', 'default_category_'. $language, $currentCategoryId, true);
				}
			}

			// feedburner URL
			$this->setSetting('blog', 'feedburner_url_'. $language, '');

			// RSS settings
			$this->setSetting('blog', 'rss_meta_'. $language, true);
			$this->setSetting('blog', 'rss_title_'. $language, 'RSS');
			$this->setSetting('blog', 'rss_description_'. $language, '');

			// @todo voeg 2 blogposts toe, tenzij er al minstens 1 blogpost is in deze taal.

			// @todo voeg 2 comments toe voor die voorbeeldblogposts. Uiteraard enkel als de blogposts geinsert werden...
		}

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
		$this->setActionRights(1, 'blog', 'delete');
		$this->setActionRights(1, 'blog', 'edit_category');
		$this->setActionRights(1, 'blog', 'edit');
		$this->setActionRights(1, 'blog', 'import_blogger');
		$this->setActionRights(1, 'blog', 'index');
		$this->setActionRights(1, 'blog', 'mass_comment_action');
		$this->setActionRights(1, 'blog', 'settings');

		// add extra's
		$this->insertExtra('blog', 'block', 'Blog', null, null, 'N', 1000);
		$this->insertExtra('blog', 'widget', 'RecentComments', 'recent_comments', null, 'N', 1001);
		$this->insertExtra('blog', 'widget', 'Categories', 'categories', null, 'N', 1002);
		$this->insertExtra('blog', 'widget', 'Archive', 'archive', null, 'N', 1003);
		$this->insertExtra('blog', 'widget', 'RecentArticles', 'recent_articles', null, 'N',1004);


		// insert locale (nl)
		$this->insertLocale('nl', 'backend', 'blog', 'err', 'RSSDescription', 'Blog RSS beschrijving is nog niet geconfigureerd. <a href="%1$s">Configureer</a>');
		$this->insertLocale('nl', 'backend', 'blog', 'lbl', 'Add', 'artikel toevoegen');
		$this->insertLocale('nl', 'backend', 'blog', 'msg', 'Added', 'Het artikel "%1$s" werd toegevoegd.');
		$this->insertLocale('nl', 'backend', 'blog', 'msg', 'CommentOnWithURL', 'Reactie op: <a href="%1$s">%2$s</a>');
		$this->insertLocale('nl', 'backend', 'blog', 'msg', 'ConfirmDelete', 'Ben je zeker dat je het artikel "%1$s" wil verwijderen?');
		$this->insertLocale('nl', 'backend', 'blog', 'msg', 'Deleted', 'De geselecteerde artikels werden verwijderd.');
		$this->insertLocale('nl', 'backend', 'blog', 'msg', 'EditArticle', 'bewerk artikel "%1$s"');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'EditComment', 'bewerk reactie');
		$this->insertLocale('nl', 'backend', 'blog', 'msg', 'Edited', 'Het artikel "%1$s" werd opgeslagen.');
		$this->insertLocale('nl', 'backend', 'blog', 'msg', 'EditedComment', 'De reactie werd opgeslagen.');
		$this->insertLocale('nl', 'backend', 'blog', 'msg', 'HelpMeta', 'Toon de meta informatie van deze blogpost in de RSS feed (categorie, tags, ...)');
		$this->insertLocale('nl', 'backend', 'blog', 'msg', 'HelpPingServices', 'Laat verschillende blogservices weten wanneer je een nieuw bericht plaatst.');
		$this->insertLocale('nl', 'backend', 'blog', 'msg', 'HelpSummary', 'Maak voor lange artikels een inleiding of samenvatting. Die kan getoond worden op de homepage of het artikeloverzicht.');
		$this->insertLocale('nl', 'backend', 'blog', 'msg', 'HelpSpamFilter', 'Schakel de ingebouwde spam-filter (Akismet) in om spam-berichten in reacties te vermijden.');
		$this->insertLocale('nl', 'backend', 'blog', 'msg', 'NoItems', 'Er zijn nog geen artikels. <a href="%1$s">Schrijf het eerste artikel</a>.');
		$this->insertLocale('nl', 'backend', 'blog', 'msg', 'NumItemsInRecentArticles', 'Aantal items in recente artikels widget');
		$this->insertLocale('nl', 'frontend', 'core', 'msg', 'BlogNoComments', 'Reageer als eerste');
		$this->insertLocale('nl', 'frontend', 'core', 'msg', 'BlogNumberOfComments', 'Al %1$s reacties');
		$this->insertLocale('nl', 'frontend', 'core', 'msg', 'BlogOneComment', 'Al 1 reactie');
		$this->insertLocale('nl', 'frontend', 'core', 'msg', 'BlogCommentIsAdded', 'Je reactie werd toegevoegd.');
		$this->insertLocale('nl', 'frontend', 'core', 'msg', 'BlogCommentInModeration', 'Je reactie wacht op goedkeuring.');
		$this->insertLocale('nl', 'frontend', 'core', 'msg', 'BlogCommentIsSpam', 'Je reactie werd gemarkeerd als spam.');
		$this->insertLocale('nl', 'frontend', 'core', 'msg', 'BlogNoItems', 'Er zijn nog geen blogposts.');

		// insert locale (en)
		$this->insertLocale('en', 'backend', 'blog', 'err', 'RSSDescription', 'Blog RSS description is not yet provided. <a href="%1$s">Configure</a>');
		$this->insertLocale('en', 'backend', 'blog', 'lbl', 'Add', 'add article');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'Added', 'The article "%1$s" was added.');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'CommentOnWithURL', 'Comment on: <a href="%1$s">%2$s</a>');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'ConfirmDelete', 'Are your sure you want to delete the article "%1$s"?');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'Deleted', 'The selected articles were deleted.');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'EditArticle', 'edit article "%1$s"');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'EditComment', 'edit comment');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'Edited', 'The article "%1$s" was saved.');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'EditedComment', 'The comment was saved.');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'HelpMeta', 'Show the meta information for this blogpost in the RSS feed (category, tags, ...)');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'HelpPingServices', 'Let various blogservices know when you\'ve posted a new article.');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'HelpSummary', 'Write an introduction or summary for long articles. It will be shown on the homepage or the article overview.');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'HelpSpamFilter', 'Enable the built-in spamfilter (Akismet) to help avoid spam comments.');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'NoItems', 'There are no articles yet. <a href="%1$s">Write the first article</a>.');
		$this->insertLocale('en', 'backend', 'blog', 'msg', 'NumItemsInRecentArticles', 'Number of articles in the recent articles widget');
		$this->insertLocale('en', 'frontend', 'core', 'msg', 'BlogNoComments', 'Be the first to comment');
		$this->insertLocale('en', 'frontend', 'core', 'msg', 'BlogNumberOfComments', '%1$s comments');
		$this->insertLocale('en', 'frontend', 'core', 'msg', 'BlogOneComment', '1 comment already');
		$this->insertLocale('en', 'frontend', 'core', 'msg', 'BlogCommentIsAdded', 'Your comment was added.');
		$this->insertLocale('en', 'frontend', 'core', 'msg', 'BlogCommentInModeration', 'Your comment is awaiting moderation.');
		$this->insertLocale('en', 'frontend', 'core', 'msg', 'BlogCommentIsSpam', 'Your comment was marked as spam.');
		$this->insertLocale('en', 'frontend', 'core', 'msg', 'BlogNoItems', 'There are no articles yet.');
	}


	/**
	 * Add the default category for a language
	 *
	 * @return	int
	 * @param	string $language
	 * @param	string $name
	 * @param	string $url
	 */
	private function addCategory($language, $name, $url)
	{
		return (int) $this->getDB()->insert('blog_categories', array('language' => (string) $language, 'name' => (string) $name, 'url' => (string) $url));
	}


	/**
	 * Does the category with this id exist within this language.
	 *
	 * @return	bool
	 * @param	string $language
	 * @param	int $id
	 */
	private function existsCategory($language, $id)
	{
		return (bool) $this->getDB()->getNumRows('SELECT id FROM blog_categories WHERE id = ? AND language = ?;', array((int) $id, (string) $language));
	}


	/**
 	 * Fetch the id of the first category in this language we come across
 	 *
 	 * @return	int
 	 * @param	string $language
	 */
	private function getCategory($language)
	{
		return (int) $this->getDB()->getVar('SELECT id FROM blog_categories WHERE language = ?;', (string) $language);
	}
}

?>