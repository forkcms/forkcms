<?php

/**
 * This is the detail-action
 *
 * @package		frontend
 * @subpackage	events
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.0
 */
class FrontendEventsDetail extends FrontendBaseBlock
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
	private $frmComment, $frmSubscription;


	/**
	 * The item
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
	 * The subscriptions
	 *
	 * @var	array
	 */
	private $subscriptions;


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
		$this->loadForms();

		// validate form
		$this->validateForms();

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
			$this->record = FrontendEventsModel::getDraft($this->URL->getParameter(1), $this->URL->getParameter('draft', 'int'));

			// add no-index to meta-custom, so the draft won't get accidentally indexed
			$this->header->addMetaCustom('<meta name="robots" content="noindex" />');
		}

		// load revision
		elseif($this->URL->getParameter('revision', 'int') != 0)
		{
			// get data
			$this->record = FrontendEventsModel::getRevision($this->URL->getParameter(1), $this->URL->getParameter('revision', 'int'));

			// add no-index to meta-custom, so the draft won't get accidentally indexed
			$this->header->addMetaCustom('<meta name="robots" content="noindex" />');
		}

		// get by URL
		else $this->record = FrontendEventsModel::get($this->URL->getParameter(1));

		// anything found?
		if(empty($this->record)) $this->redirect(FrontendNavigation::getURL(404));

		// overwrite URLs
		$this->record['allow_comments'] = ($this->record['allow_comments'] == 'Y');
		$this->record['allow_subscriptions'] = ($this->record['allow_subscriptions'] == 'Y');

		// get tags
		$this->record['tags'] = FrontendTagsModel::getForItem('events', $this->record['revision_id']);

		// get comments
		$this->comments = FrontendEventsModel::getComments($this->record['id']);

		// get subscriptions
		$this->subscriptions = FrontendEventsModel::getSubscriptions($this->record['id']);

		// get settings
		$this->settings = FrontendModel::getModuleSettings('events');

		// reset allow comments
		if(!$this->settings['allow_comments']) $this->record['allow_comments'] = false;
		if(!$this->settings['allow_subscriptions']) $this->record['allow_subscriptions'] = false;
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForms()
	{
		// init vars
		$author = (SpoonCookie::exists('comment_author')) ? SpoonCookie::get('comment_author') : null;
		$email = (SpoonCookie::exists('comment_email')) ? SpoonCookie::get('comment_email') : null;
		$website = (SpoonCookie::exists('comment_website')) ? SpoonCookie::get('comment_website') : 'http://';

		// create from
		$this->frmSubscription = new FrontendForm('subscription');
		$this->frmSubscription->setAction($this->frmSubscription->getAction() . '#' . FL::act('Subscription'));

		// create elements
		$this->frmSubscription->addText('author', $author);
		$this->frmSubscription->addText('email', $email);

		// create form
		$this->frmComment = new FrontendForm('comment');
		$this->frmComment->setAction($this->frmComment->getAction() . '#' . FL::act('Comment'));

		// create elements
		$this->frmComment->addText('author', $author);
		$this->frmComment->addText('email', $email);
		$this->frmComment->addText('website', $website);
		$this->frmComment->addTextarea('message');
	}


	/**
	 * Parse the data into the template
	 *
	 * @return	void
	 */
	private function parse()
	{
		// get RSS-link
		$rssLink = FrontendModel::getModuleSetting('events', 'feedburner_url_' . FRONTEND_LANGUAGE);
		if($rssLink == '') $rssLink = FrontendNavigation::getURLForBlock('events', 'rss');

		// add RSS-feed into the metaCustom
		$this->header->addMetaCustom('<link rel="alternate" type="application/rss+xml" title="' . FrontendModel::getModuleSetting('events', 'rss_title_' . FRONTEND_LANGUAGE) . '" href="' . $rssLink . '" />');

		// get RSS-link for the comments
		$rssCommentsLink = FrontendNavigation::getURLForBlock('events', 'article_comments_rss') . '/' . $this->record['url'];

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
			$meta .= '<meta property="og:url" content="' . SITE_URL . FrontendNavigation::getURLForBlock('events', 'detail') . '/' . $this->record['url'] . '" />' . "\n";
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
		if($commentCount > 1) $this->tpl->assign('eventsCommentsMultiple', true);

		// count comments
		$subscriptionCount = count($this->subscriptions);

		// assign the comments
		$this->tpl->assign('subscriptionsCount', $subscriptionCount);
		$this->tpl->assign('subscriptions', $this->subscriptions);

		// options
		if($subscriptionCount > 1) $this->tpl->assign('eventsSubscriptionsMultiple', true);

		// parse the forms
		$this->frmComment->parse($this->tpl);
		$this->frmSubscription->parse($this->tpl);

		// some options
		if($this->URL->getParameter('comment', 'string') == 'moderation') $this->tpl->assign('commentIsInModeration', true);
		if($this->URL->getParameter('comment', 'string') == 'spam') $this->tpl->assign('commentIsSpam', true);
		if($this->URL->getParameter('comment', 'string') == 'true') $this->tpl->assign('commentIsAdded', true);

		if($this->URL->getParameter('subscription', 'string') == 'moderation') $this->tpl->assign('subscriptionIsInModeration', true);
		if($this->URL->getParameter('subscription', 'string') == 'spam') $this->tpl->assign('subscriptionIsSpam', true);
		if($this->URL->getParameter('subscription', 'string') == 'true') $this->tpl->assign('subscriptionIsAdded', true);

		if($this->record['num_subscriptions'] >= $this->record['max_subscriptions']) $this->tpl->assign('subscriptionsComplete', true);

		// assign settings
		$this->tpl->assign('settings', $this->settings);

		// assign navigation
		$this->tpl->assign('navigation', FrontendEventsModel::getNavigation($this->record['id']));
	}


	/**
	 * Validate the comment form
	 *
	 * @return	void
	 */
	private function validateFormComment()
	{
		// get settings
		$commentsAllowed = (isset($this->settings['allow_comments']) && $this->settings['allow_comments']);

		// comments aren't allowed so we don't have to validate
		if(!$commentsAllowed) return false;

		// is the form submitted
		if($this->frmComment->isSubmitted())
		{
			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frmComment->cleanupFields();

			// does the key exists?
			if(SpoonSession::exists('events_comment_' . $this->record['id']))
			{
				// calculate difference
				$diff = time() - (int) SpoonSession::get('events_comment_' . $this->record['id']);

				// calculate difference, it it isn't 10 seconds the we tell the user to slow down
				if($diff < 10 && $diff != 0) $this->frmComment->getField('message')->addError(FL::err('CommentTimeout'));
			}

			// validate required fields
			$this->frmComment->getField('author')->isFilled(FL::err('AuthorIsRequired'));
			$this->frmComment->getField('email')->isEmail(FL::err('EmailIsRequired'));
			$this->frmComment->getField('message')->isFilled(FL::err('MessageIsRequired'));

			// validate optional fields
			if($this->frmComment->getField('website')->isFilled() && $this->frmComment->getField('website')->getValue() != 'http://')
			{
				$var = $this->frmComment->getField('website')->isURL(FL::err('InvalidURL'));
			}

			// no errors?
			if($this->frmComment->isCorrect())
			{
				// get module setting
				$spamFilterEnabled = (isset($this->settings['spamfilter_comments']) && $this->settings['spamfilter_comments']);
				$moderationEnabled = (isset($this->settings['moderation_comments']) && $this->settings['moderation_comments']);

				// reformat data
				$author = $this->frmComment->getField('author')->getValue();
				$email = $this->frmComment->getField('email')->getValue();
				$website = $this->frmComment->getField('website')->getValue();
				if(trim($website) == '' || $website == 'http://') $website = null;
				$text = $this->frmComment->getField('message')->getValue();

				// build array
				$comment['event_id'] = $this->record['id'];
				$comment['language'] = FRONTEND_LANGUAGE;
				$comment['created_on'] = FrontendModel::getUTCDate();
				$comment['author'] = $author;
				$comment['email'] = $email;
				$comment['website'] = $website;
				$comment['text'] = $text;
				$comment['status'] = 'published';
				$comment['data'] = serialize(array('server' => $_SERVER));

				// get URL for article
				$permaLink = FrontendNavigation::getURLForBlock('events', 'detail') . '/' . $this->record['url'];
				$redirectLink = $permaLink;

				// is moderation enabled
				if($moderationEnabled)
				{
					// if the commenter isn't moderated before alter the comment status so it will appear in the moderation queue
					if(!FrontendEventsModel::isModerated($author, $email)) $comment['status'] = 'moderation';
				}

				// should we check if the item is spam
				if($spamFilterEnabled)
				{
					// if the comment is spam alter the comment status so it will appear in the spam queue
					if(FrontendModel::isSpam($text, SITE_URL . $permaLink, $author, $email, $website)) $comment['status'] = 'spam';
				}

				// insert comment
				$comment['id'] = FrontendEventsModel::insertComment($comment);

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
				$comment['event_title'] = $this->record['title'];
				$comment['event_url'] = $this->record['url'];

				// notify the admin
				FrontendEventsModel::notifyAdmin($comment);

				// store timestamp in session so we can block excesive usage
				SpoonSession::set('events_comment_' . $this->record['id'], time());

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


	/**
	 * Validate the form
	 *
	 * @return	void
	 */
	private function validateForms()
	{
		// validate subscription
		$this->validateFormSubscription();

		// validate comment
		$this->validateFormComment();
	}


	/**
	 * Validate the subscription form
	 *
	 * @return	void
	 */
	private function validateFormSubscription()
	{
		// get settings
		$subscriptionsAllowed = (isset($this->settings['allow_subscriptions']) && $this->settings['allow_subscriptions']);

		// comments aren't allowed so we don't have to validate
		if(!$subscriptionsAllowed) return false;

		// is the form submitted
		if($this->frmSubscription->isSubmitted())
		{
			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frmSubscription->cleanupFields();

			// does the key exists?
			if(SpoonSession::exists('events_subscription_' . $this->record['id']))
			{
				// calculate difference
				$diff = time() - (int) SpoonSession::get('events_subscription_' . $this->record['id']);

				// calculate difference, it it isn't 10 seconds the we tell the user to slow down
				if($diff < 10 && $diff != 0) $this->frmSubscription->getField('email')->addError(FL::err('SubscriptionTimeout'));
			}

			// validate required fields
			$this->frmSubscription->getField('author')->isFilled(FL::err('AuthorIsRequired'));
			$this->frmSubscription->getField('email')->isEmail(FL::err('EmailIsRequired'));

			// no errors?
			if($this->frmSubscription->isCorrect())
			{
				// get module setting
				$spamFilterEnabled = (isset($this->settings['spamfilter_subscriptions']) && $this->settings['spamfilter_subscriptions']);
				$moderationEnabled = (isset($this->settings['moderation_subscriptions']) && $this->settings['moderation_subscriptions']);

				// reformat data
				$author = $this->frmComment->getField('author')->getValue();
				$email = $this->frmComment->getField('email')->getValue();

				// build array
				$subscription['event_id'] = $this->record['id'];
				$subscription['language'] = FRONTEND_LANGUAGE;
				$subscription['created_on'] = FrontendModel::getUTCDate();
				$subscription['author'] = $author;
				$subscription['email'] = $email;
				$subscription['status'] = 'published';
				$subscription['data'] = serialize(array('server' => $_SERVER));

				// get URL for article
				$permaLink = FrontendNavigation::getURLForBlock('events', 'detail') . '/' . $this->record['url'];
				$redirectLink = $permaLink;

				// moderation enabled
				if($moderationEnabled)
				{
					$subscription['status'] = 'moderation';
				}

				// should we check if the item is spam
				if($spamFilterEnabled)
				{
					// if the subscription is spam alter the subscription status so it will appear in the spam queue
					if(FrontendModel::isSpam(null, SITE_URL . $permaLink, $author, $email, null)) $subscription['status'] = 'spam';
				}

				// insert subscription
				$subscription['id'] = FrontendEventsModel::insertSubscription($subscription);

				// append a parameter to the URL so we can show moderation
				if(strpos($redirectLink, '?') === false)
				{
					if($subscription['status'] == 'moderation') $redirectLink .= '?subscription=moderation#eventsSubscribeForm';
					if($subscription['status'] == 'spam') $redirectLink .= '?subscription=spam#eventsSubscribeForm';
					if($subscription['status'] == 'published') $redirectLink .= '?subscription=true#subscription-' . $subscription['id'];
				}
				else
				{
					if($subscription['status'] == 'moderation') $redirectLink .= '&subscription=moderation#eventsSubscribeForm';
					if($subscription['status'] == 'spam') $redirectLink .= '&subscription=spam#eventsSubscribeForm';
					if($subscription['status'] == 'published') $redirectLink .= '&subscription=true#subscription-' . $subscription['id'];
				}

				// set title
				$subscription['event_title'] = $this->record['title'];
				$subscription['event_url'] = $this->record['url'];

				// notify the admin
				FrontendEventsModel::notifyAdminOnSubscription($subscription);

				// store timestamp in session so we can block excesive usage
				SpoonSession::set('events_subscription_' . $this->record['id'], time());

				// store author-data in cookies
				try
				{
					// set cookies
					SpoonCookie::set('subscription_author', $author, (30 * 24 * 60 * 60), '/', '.' . $this->URL->getDomain());
					SpoonCookie::set('subscription_email', $email, (30 * 24 * 60 * 60), '/', '.' . $this->URL->getDomain());
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