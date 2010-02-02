<?php

/**
 * FrontendBlogDetail
 *
 * This is the detail-action
 *
 * @package		frontend
 * @subpackage	blog
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
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

		// show contenTitle
		$this->tpl->assign('hideContentTitle', true);

		// load template
		$this->loadTemplate();

		// load the data
		$this->getData();

		// load form
		$this->loadForm();

		// validate form
		$this->validate();

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

		// get by url
		$this->record = FrontendBlogModel::get($this->URL->getParameter(1));

		// anything found?
		if(empty($this->record)) $this->redirect(FrontendNavigation::getURL(404));

		// overwrite URLs
		$this->record['category_full_url'] = FrontendNavigation::getURLForBlock('blog', 'category') .'/'. $this->record['category_url'];
		$this->record['full_url'] = FrontendNavigation::getURLForBlock('blog', 'detail') .'/'. $this->record['url'];

		// get tags
		$this->record['tags'] = FrontendTagsModel::getForItem('blog', $this->record['id']);

		// get comments
		$this->comments = FrontendBlogModel::getComments($this->record['id']);

		// get settings
		$this->settings = FrontendModel::getModuleSettings('blog');
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new FrontendForm('react');

		// init vars
		$author = (SpoonCookie::exists('comment_author')) ? SpoonCookie::get('comment_author') : null;
		$email = (SpoonCookie::exists('comment_email')) ? SpoonCookie::get('comment_email') : null;
		$website = (SpoonCookie::exists('comment_website')) ? SpoonCookie::get('comment_website') : null;

		// create elements
		$this->frm->addTextField('author', $author);
		$this->frm->addTextField('email', $email);
		$this->frm->addTextField('website', $website);
		$this->frm->addTextArea('text');
		$this->frm->addButton('react', ucfirst(FL::lbl('React')));
	}


	/**
	 * Parse the data into the template
	 *
	 * @return	void
	 */
	private function parse()
	{
		// @todo	find a decent way to decide which block should be removed... I hate blocks.

		// get RSS-link
		$rssLink = FrontendModel::getModuleSetting('blog', 'feedburner_url_'. FRONTEND_LANGUAGE);
		if($rssLink == '') $rssLink = FrontendNavigation::getURLForBlock('blog', 'rss');

		// add RSS-feed into the metaCustom
		$this->header->addMetaCustom('<link rel="alternate" type="application/rss+xml" title="'. FrontendModel::getModuleSetting('blog', 'rss_title_'. FRONTEND_LANGUAGE) .'" href="'. $rssLink .'" />');

		// add into breadcrumb
		$this->breadcrumb->addElement($this->record['title']);

		// set meta
		$this->header->setPageTitle($this->record['title']);
		$this->header->setMetaDescription($this->record['meta_description'], ($this->record['meta_description_overwrite'] == 'Y'));
		$this->header->setMetaKeywords($this->record['meta_keywords'], ($this->record['meta_keywords_overwrite'] == 'Y'));

		// assign article
		// loop values @todo	we should do this in a decent way...
		foreach($this->record as $key => $value) if($value !== null) $this->tpl->assign('blogArticle'. SpoonFilter::toCamelCase($key), $value);
		$this->tpl->assign('blogArticle', $this->record);

		// count comments
		$commentCount = count($this->comments);

		// assign the comments
		$this->tpl->assign('blogCommentsCount', $commentCount);
		$this->tpl->assign('blogComments', $this->comments);

		// options
		if($commentCount > 1) $this->tpl->assign('blogCommentsMultiple', true);

		// parse the form
		$this->frm->parse($this->tpl);

		// some options
		if($this->URL->getParameter('comment', 'string') == 'moderation') $this->tpl->assign('commentIsInModeration', true);
		if($this->URL->getParameter('comment', 'string') == 'spam') $this->tpl->assign('commentIsSpam', true);
		if($this->URL->getParameter('comment', 'string') == 'true') $this->tpl->assign('commentIsAdded', true);

		// assign settings
		// loop values @todo	we should do this in a decent way...
		foreach($this->settings as $key => $value) if($value !== null) $this->tpl->assign('blogSettings'. SpoonFilter::toCamelCase($key), $value);
		$this->tpl->assign('blogSettings', $this->settings);
	}


	/**
	 * Validate the form
	 *
	 * @return	void
	 */
	private function validate()
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

			// validate required fields
			$this->frm->getField('author')->isFilled(FL::err('AuthorIsRequired'));
			$this->frm->getField('email')->isEmail(FL::err('EmailIsRequired'));
			$this->frm->getField('text')->isFilled(FL::err('MessageIsRequired'));

			// validate optional fields
			if($this->frm->getField('website')->isFilled()) $this->frm->getField('website')->isURL(FL::err('InvalidURL'));

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
				if($website == '' || $website == 'http://') $website = null;
				$text = $this->frm->getField('text')->getValue();

				// build array
				$comment['post_id'] = $this->record['id'];
				$comment['created_on'] = FrontendModel::getUTCDate();
				$comment['author'] = $author;
				$comment['email'] = $email;
				$comment['website'] = $website;
				$comment['text'] = $text;
				$comment['status'] = 'published';
				$comment['data'] = serialize(array('server' => $_SERVER));

				// get url for article
				$permaLink = FrontendNavigation::getURLForBlock('blog', 'detail') .'/'. $this->record['url'];
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
				$commentId = FrontendBlogModel::addComment($comment);

				// append a parameter to the url so we can show moderation
				if($comment['status'] == 'moderation') $redirectLink .= '?comment=moderation#'.FL::getAction('React');
				if($comment['status'] == 'spam') $redirectLink .= '?comment=spam#'.FL::getAction('React');
				if($comment['status'] == 'published') $redirectLink .= '?comment=true#'. FL::getAction('Comment') .'-'. $commentId;

				// store author-data in cookies
				try
				{
					// set cookies
					SpoonCookie::set('comment_author', $author, (7 * 24 * 60 * 60), '/', '.'. $this->URL->getDomain());
					SpoonCookie::set('comment_email', $email, (7 * 24 * 60 * 60), '/', '.'. $this->URL->getDomain());
					SpoonCookie::set('comment_website', $website, (7 * 24 * 60 * 60), '/', '.'. $this->URL->getDomain());
				}
				catch(Exception $e)
				{
					// ignore
				}

				// redirect
				$this->redirect($redirectLink);
			}
		}
	}
}
?>