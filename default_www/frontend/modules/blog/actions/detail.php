<?php

/**
 * This is the detail-action
 *
 * @package		frontend
 * @subpackage	blog
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class FrontendBlogDetail extends FrontendBaseBlock
{
	/**
	 * The comments
	 *
	 * @var	array
	 */
	private $comments;


	/**
	 * Form instance
	 *
	 * @var FrontendForm
	 */
	private $frm;


	/**
	 * The blogpost
	 *
	 * @var	array
	 */
	private $record;


	/**
	 * The settings
	 *
	 * @var	array
	 */
	private $settings;


	/**
	 * Execute the extra
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call the parent
		parent::execute();

		// hide contenTitle, in the template the title is wrapped with an inverse-option
		$this->tpl->assign('hideContentTitle', true);

		// load template
		$this->loadTemplate();

		// load the data
		$this->getData();

		// load form
		$this->loadForm();

		// validate form
		$this->validateForm();

		// parse
		$this->parse();
	}


	/**
	 * Load the data, don't forget to validate the incoming data
	 *
	 * @return	void
	 */
	private function getData()
	{
		// validate incoming parameters
		if($this->URL->getParameter(1) === null) $this->redirect(FrontendNavigation::getURL(404));

		// load draft
		if($this->URL->getParameter('draft', 'int') != 0)
		{
			// get data
			$this->record = FrontendBlogModel::getDraft($this->URL->getParameter(1), $this->URL->getParameter('draft', 'int'));

			// add no-index to meta-custom, so the draft won't get accidentally indexed
			$this->header->addMetaCustom('<meta name="robots" content="noindex" />');
		}

		// load revision
		elseif($this->URL->getParameter('revision', 'int') != 0)
		{
			// get data
			$this->record = FrontendBlogModel::getRevision($this->URL->getParameter(1), $this->URL->getParameter('revision', 'int'));

			// add no-index to meta-custom, so the draft won't get accidentally indexed
			$this->header->addMetaCustom('<meta name="robots" content="noindex" />');
		}

		// get by URL
		else $this->record = FrontendBlogModel::get($this->URL->getParameter(1));

		// anything found?
		if(empty($this->record)) $this->redirect(FrontendNavigation::getURL(404));

		// overwrite URLs
		$this->record['category_full_url'] = FrontendNavigation::getURLForBlock('blog', 'category') . '/' . $this->record['category_url'];
		$this->record['full_url'] = FrontendNavigation::getURLForBlock('blog', 'detail') . '/' . $this->record['url'];
		$this->record['allow_comments'] = ($this->record['allow_comments'] == 'Y');

		// get tags
		$this->record['tags'] = FrontendTagsModel::getForItem('blog', $this->record['revision_id']);

		// get comments
		$this->comments = FrontendBlogModel::getComments($this->record['id']);

		// get settings
		$this->settings = FrontendModel::getModuleSettings('blog');

		// reset allow comments
		if(!$this->settings['allow_comments']) $this->record['allow_comments'] = false;
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new FrontendForm('comment');
		$this->frm->setAction($this->frm->getAction() . '#' . FL::act('Comment'));

		// init vars
		$author = (SpoonCookie::exists('comment_author')) ? SpoonCookie::get('comment_author') : null;
		$email = (SpoonCookie::exists('comment_email')) ? SpoonCookie::get('comment_email') : null;
		$website = (SpoonCookie::exists('comment_website')) ? SpoonCookie::get('comment_website') : 'http://';

		// create elements
		$this->frm->addText('author', $author);
		$this->frm->addText('email', $email);
		$this->frm->addText('website', $website);
		$this->frm->addTextarea('message');
	}


	/**
	 * Parse the data into the template
	 *
	 * @return	void
	 */
	private function parse()
	{
		// get RSS-link
		$rssLink = FrontendModel::getModuleSetting('blog', 'feedburner_url_' . FRONTEND_LANGUAGE);
		if($rssLink == '') $rssLink = FrontendNavigation::getURLForBlock('blog', 'rss');

		// add RSS-feed into the metaCustom
		$this->header->addMetaCustom('<link rel="alternate" type="application/rss+xml" title="' . FrontendModel::getModuleSetting('blog', 'rss_title_' . FRONTEND_LANGUAGE) . '" href="' . $rssLink . '" />');

		// get RSS-link for the comments
		$rssCommentsLink = FrontendNavigation::getURLForBlock('blog', 'article_comments_rss') . '/' . $this->record['url'];

		// add RSS-feed into the metaCustom
		$this->header->addMetaCustom('<link rel="alternate" type="application/rss+xml" title="' . vsprintf(FL::msg('CommentsOn'), array($this->record['title'])) . '" href="' . $rssCommentsLink . '" />');

		// build Facebook Open Graph-data
		if(FrontendModel::getModuleSetting('core', 'facebook_admin_ids', null) !== null)
		{
			// default image
			$image = SITE_URL . '/facebook.png';

			// try to get an image in the content
			$matches = array();
			preg_match('/<img.*src="(.*)".*\/>/iU', $this->record['text'], $matches);

			// found an image?
			if(isset($matches[1]))
			{
				$image = $matches[1];
				if(substr($image, 0, 7) != 'http://') $image = SITE_URL . $image;
			}

			$meta = '<!-- openGraph meta-data -->' . "\n";
			$meta .= '<meta property="og:title" content="' . $this->record['title'] . '" />' . "\n";
			$meta .= '<meta property="og:type" content="article" />' . "\n";
			$meta .= '<meta property="og:image" content="' . $image . '" />' . "\n";
			$meta .= '<meta property="og:url" content="' . SITE_URL . FrontendNavigation::getURLForBlock('blog', 'detail') . '/' . $this->record['url'] . '" />' . "\n";
			$meta .= '<meta property="og:site_name" content="' . FrontendModel::getModuleSetting('core', 'site_title_' . FRONTEND_LANGUAGE, SITE_DEFAULT_TITLE) . '" />' . "\n";
			$meta .= '<meta property="fb:admins" content="' . FrontendModel::getModuleSetting('core', 'facebook_admin_ids') . '" />' . "\n";
			$meta .= '<meta property="og:description" content="' . $this->record['title'] . '" />' . "\n";

			// add
			$this->header->addMetaCustom($meta);
		}

		// add into breadcrumb
		$this->breadcrumb->addElement($this->record['title']);

		// set meta
		$this->header->setPageTitle($this->record['title']);
		$this->header->setMetaDescription($this->record['meta_description'], ($this->record['meta_description_overwrite'] == 'Y'));
		$this->header->setMetaKeywords($this->record['meta_keywords'], ($this->record['meta_keywords_overwrite'] == 'Y'));

		// assign article
		$this->tpl->assign('item', $this->record);

		// count comments
		$commentCount = count($this->comments);

		// assign the comments
		$this->tpl->assign('commentsCount', $commentCount);
		$this->tpl->assign('comments', $this->comments);

		// options
		if($commentCount > 1) $this->tpl->assign('blogCommentsMultiple', true);

		// parse the form
		$this->frm->parse($this->tpl);

		// some options
		if($this->URL->getParameter('comment', 'string') == 'moderation') $this->tpl->assign('commentIsInModeration', true);
		if($this->URL->getParameter('comment', 'string') == 'spam') $this->tpl->assign('commentIsSpam', true);
		if($this->URL->getParameter('comment', 'string') == 'true') $this->tpl->assign('commentIsAdded', true);

		// assign settings
		$this->tpl->assign('settings', $this->settings);

		// assign navigation
		$this->tpl->assign('navigation', FrontendBlogModel::getNavigation($this->record['id']));
	}


	/**
	 * Validate the form
	 *
	 * @return	void
	 */
	private function validateForm()
	{
		// get settings
		$commentsAllowed = (isset($this->settings['allow_comments']) && $this->settings['allow_comments']);

		// comments aren't allowed so we don't have to validate
		if(!$commentsAllowed) return false;

		// is the form submitted
		if($this->frm->isSubmitted())
		{
			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// does the key exists?
			if(SpoonSession::exists('blog_comment_' . $this->record['id']))
			{
				// calculate difference
				$diff = time() - (int) SpoonSession::get('blog_comment_' . $this->record['id']);

				// calculate difference, it it isn't 10 seconds the we tell the user to slow down
				if($diff < 10 && $diff != 0) $this->frm->getField('message')->addError(FL::err('CommentTimeout'));
			}

			// validate required fields
			$this->frm->getField('author')->isFilled(FL::err('AuthorIsRequired'));
			$this->frm->getField('email')->isEmail(FL::err('EmailIsRequired'));
			$this->frm->getField('message')->isFilled(FL::err('MessageIsRequired'));

			// validate optional fields
			if($this->frm->getField('website')->isFilled() && $this->frm->getField('website')->getValue() != 'http://')
			{
				$var = $this->frm->getField('website')->isURL(FL::err('InvalidURL'));
			}

			// no errors?
			if($this->frm->isCorrect())
			{
				// get module setting
				$spamFilterEnabled = (isset($this->settings['spamfilter']) && $this->settings['spamfilter']);
				$moderationEnabled = (isset($this->settings['moderation']) && $this->settings['moderation']);

				// reformat data
				$author = $this->frm->getField('author')->getValue();
				$email = $this->frm->getField('email')->getValue();
				$website = $this->frm->getField('website')->getValue();
				if(trim($website) == '' || $website == 'http://') $website = null;
				$text = $this->frm->getField('message')->getValue();

				// build array
				$comment['post_id'] = $this->record['id'];
				$comment['language'] = FRONTEND_LANGUAGE;
				$comment['created_on'] = FrontendModel::getUTCDate();
				$comment['author'] = $author;
				$comment['email'] = $email;
				$comment['website'] = $website;
				$comment['text'] = $text;
				$comment['status'] = 'published';
				$comment['data'] = serialize(array('server' => $_SERVER));

				// get URL for article
				$permaLink = FrontendNavigation::getURLForBlock('blog', 'detail') . '/' . $this->record['url'];
				$redirectLink = $permaLink;

				// is moderation enabled
				if($moderationEnabled)
				{
					// if the commenter isn't moderated before alter the comment status so it will appear in the moderation queue
					if(!FrontendBlogModel::isModerated($author, $email)) $comment['status'] = 'moderation';
				}

				// should we check if the item is spam
				if($spamFilterEnabled)
				{
					// if the comment is spam alter the comment status so it will appear in the spam queue
					if(FrontendModel::isSpam($text, SITE_URL . $permaLink, $author, $email, $website)) $comment['status'] = 'spam';
				}

				// insert comment
				$comment['id'] = FrontendBlogModel::insertComment($comment);

				// append a parameter to the URL so we can show moderation
				if(strpos($redirectLink, '?') === false)
				{
					if($comment['status'] == 'moderation') $redirectLink .= '?comment=moderation#' . FL::act('Comment');
					if($comment['status'] == 'spam') $redirectLink .= '?comment=spam#' . FL::act('Comment');
					if($comment['status'] == 'published') $redirectLink .= '?comment=true#comment-' . $comment['id'];
				}
				else
				{
					if($comment['status'] == 'moderation') $redirectLink .= '&comment=moderation#' . FL::act('Comment');
					if($comment['status'] == 'spam') $redirectLink .= '&comment=spam#' . FL::act('Comment');
					if($comment['status'] == 'published') $redirectLink .= '&comment=true#comment-' . $comment['id'];
				}

				// set title
				$comment['post_title'] = $this->record['title'];
				$comment['post_url'] = $this->record['url'];

				// notify the admin
				FrontendBlogModel::notifyAdmin($comment);

				// store timestamp in session so we can block excesive usage
				SpoonSession::set('blog_comment_' . $this->record['id'], time());

				// store author-data in cookies
				try
				{
					// set cookies
					SpoonCookie::set('comment_author', $author, (30 * 24 * 60 * 60), '/', '.' . $this->URL->getDomain());
					SpoonCookie::set('comment_email', $email, (30 * 24 * 60 * 60), '/', '.' . $this->URL->getDomain());
					SpoonCookie::set('comment_website', $website, (30 * 24 * 60 * 60), '/', '.' . $this->URL->getDomain());
				}
				catch(Exception $e)
				{
					// settings cookies isn't allowed, but because this isn't a real problem we ignore the exception
				}

				// redirect
				$this->redirect($redirectLink);
			}
		}
	}
}

?>