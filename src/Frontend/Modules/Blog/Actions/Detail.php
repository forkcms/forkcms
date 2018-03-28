<?php

namespace Frontend\Modules\Blog\Actions;

use Common\Doctrine\Entity\Meta;
use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Form as FrontendForm;
use Frontend\Core\Header\MetaLink;
use Frontend\Core\Language\Language as FL;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Blog\Engine\Model as FrontendBlogModel;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Detail extends FrontendBaseBlock
{
    /**
     * The comments
     *
     * @var array
     */
    private $comments;

    /**
     * Form instance
     *
     * @var FrontendForm
     */
    private $form;

    /**
     * The blogpost
     *
     * @var array
     */
    private $blogPost;

    /**
     * The settings
     *
     * @var array
     */
    private $settings;

    public function execute(): void
    {
        parent::execute();
        $this->loadTemplate();
        $this->getData();
        $this->buildForm();
        $this->handleForm();
        $this->parse();
    }

    protected function setMeta(Meta $meta): void
    {
        parent::setMeta($meta);

        // Add no-index, so the draft won't get accidentally indexed
        if ($this->url->getParameter('revision', 'int') !== 0) {
            $this->header->addMetaData(['name' => 'robots', 'content' => 'noindex, nofollow'], true);
        }
    }

    private function getBlogPost(): array
    {
        if ($this->url->getParameter(1) === null) {
            throw new NotFoundHttpException();
        }

        if ($this->url->getParameter('revision', 'int') === null) {
            return $this->completeBlogPost(FrontendBlogModel::get($this->url->getParameter(1)));
        }

        return $this->completeBlogPost(
            FrontendBlogModel::getRevision(
                $this->url->getParameter(1),
                $this->url->getParameter('revision', 'int')
            )
        );
    }

    private function completeBlogPost(array $blogPost): array
    {
        if (empty($blogPost)) {
            throw new NotFoundHttpException();
        }

        $baseCategoryUrl = FrontendNavigation::getUrlForBlock($this->getModule(), 'Category');
        $blogPost['category_full_url'] = $baseCategoryUrl . '/' . $blogPost['category_url'];
        $baseDetailUrl = FrontendNavigation::getUrlForBlock($this->getModule(), 'Detail');
        $blogPost['full_url'] = $baseDetailUrl . '/' . $blogPost['url'];

        return $blogPost;
    }

    private function getBlogPostComments(): array
    {
        $comments = FrontendBlogModel::getComments($this->blogPost['id']);

        $this->blogPost['comments_count'] = count($comments);

        return $comments;
    }

    private function getModuleSettings(): array
    {
        $moduleSettings = $this->get('fork.settings')->getForModule($this->getModule());

        // Ignore the individual setting if the blog module doesn't allow comments
        if (!$moduleSettings['allow_comments']) {
            $this->blogPost['allow_comments'] = false;
        }

        return $moduleSettings;
    }

    private function getData(): void
    {
        $this->blogPost = $this->getBlogPost();
        $this->comments = $this->getBlogPostComments();
        $this->settings = $this->getModuleSettings();
    }

    private function addLinkToRssFeeds(): void
    {
        // General rss feed
        $this->header->addRssLink(
            $this->get('fork.settings')->get($this->getModule(), 'rss_title_' . LANGUAGE, SITE_DEFAULT_TITLE),
            FrontendNavigation::getUrlForBlock($this->getModule(), 'Rss')
        );

        // Rss feed for the comments of this blog post
        $this->header->addRssLink(
            vsprintf(FL::msg('CommentsOn'), [$this->blogPost['title']]),
            FrontendNavigation::getUrlForBlock($this->getModule(), 'ArticleCommentsRss') . '/' . $this->blogPost['url']
        );
    }

    private function addOpenGraphData(): void
    {
        // add specified image
        if (isset($this->blogPost['image']) && $this->blogPost['image'] !== '') {
            $this->header->addOpenGraphImage(
                FRONTEND_FILES_URL . '/Blog/images/source/' . $this->blogPost['image']
            );
        }
        $this->header->extractOpenGraphImages($this->blogPost['text']);

        // Open Graph-data: add additional OpenGraph data
        $this->header->addOpenGraphData('title', $this->blogPost['title'], true);
        $this->header->addOpenGraphData('type', 'article', true);
        $this->header->addOpenGraphData('url', SITE_URL . $this->blogPost['full_url'], true);
        $this->header->addOpenGraphData(
            'site_name',
            $this->get('fork.settings')->get('Core', 'site_title_' . LANGUAGE, SITE_DEFAULT_TITLE),
            true
        );

        /** @var Meta $meta */
        $meta = $this->blogPost['meta'];
        $this->header->addOpenGraphData(
            'description',
            $meta->isDescriptionOverwrite() ? $meta->getDescription() : $this->blogPost['title'],
            true
        );
    }

    private function addTwitterCard(): void
    {
        $this->header->setTwitterCard(
            $this->blogPost['title'],
            $this->blogPost['meta_description'],
            FRONTEND_FILES_URL . '/Blog/images/source/' . $this->blogPost['image']
        );
    }

    private function addBreadCrumbs(): void
    {
        // when there are 2 or more categories with at least one item in it,
        // the category will be added in the breadcrumb
        if (count(FrontendBlogModel::getAllCategories()) > 1) {
            $this->breadcrumb->addElement($this->blogPost['category_title'], $this->blogPost['category_full_url']);
        }

        $this->breadcrumb->addElement($this->blogPost['title']);
    }

    private function addNewCommentAlerts()
    {
        switch ($this->url->getParameter('comment')) {
            case 'moderation':
                $this->template->assign('commentIsInModeration', true);

                break;
            case 'spam':
                $this->template->assign('commentIsSpam', true);

                break;
            case 'true':
                $this->template->assign('commentIsAdded', true);

                break;
        }
    }

    private function addNavigation()
    {
        $navigation = FrontendBlogModel::getNavigation($this->blogPost['id']);

        // set previous and next link for usage with Flip ahead
        if (!empty($navigation['previous'])) {
            $this->header->addMetaLink(MetaLink::previous(SITE_URL . $navigation['previous']['url']));
        }
        if (!empty($navigation['next'])) {
            $this->header->addMetaLink(MetaLink::next(SITE_URL . $navigation['next']['url']));
        }

        $this->template->assign('navigation', $navigation);
    }

    private function parse(): void
    {
        $this->addLinkToRssFeeds();
        $this->addOpenGraphData();
        $this->addTwitterCard();
        $this->addBreadCrumbs();
        $this->addNewCommentAlerts();
        $this->addNavigation();
        $this->setMeta($this->blogPost['meta']);
        $this->header->setCanonicalUrl($this->blogPost['full_url']);

        $this->template->assign('settings', $this->settings);
        $this->template->assign('item', $this->blogPost);
        $this->template->assign('commentsCount', $this->blogPost['comments_count']);
        $this->template->assign('comments', $this->comments);
        if ($this->blogPost['comments_count'] > 1) {
            $this->template->assign('blogCommentsMultiple', true);
        }

        $this->form->parse($this->template);
    }

    private function buildForm(): void
    {
        $this->form = new FrontendForm('commentsForm');
        $this->form->setAction($this->form->getAction() . '#' . FL::act('Comment'));

        $cookie = FrontendModel::getContainer()->get('fork.cookie');
        $author = $cookie->get('comment_author');
        $email = ($cookie->has('comment_email') && filter_var($cookie->get('comment_email'), FILTER_VALIDATE_EMAIL))
            ? $cookie->get('comment_email') : null;
        $website = ($cookie->has('comment_website') && \SpoonFilter::isURL($cookie->get('comment_website')))
            ? $cookie->get('comment_website') : 'http://';

        $this->form->addText('author', $author)->setAttributes(['required' => null]);
        $this->form->addText('email', $email)->setAttributes(['required' => null, 'type' => 'email']);
        $this->form->addText('website', $website, null);
        $this->form->addTextarea('message')->setAttributes(['required' => null]);
    }

    private function isCommentingAllowed(): bool
    {
        return isset($this->settings['allow_comments']) && $this->settings['allow_comments'];
    }

    private function isSpamFilterEnabled(): bool
    {
        return isset($this->settings['spamfilter']) && $this->settings['spamfilter'];
    }

    private function isModerationFilterEnabled(): bool
    {
        return isset($this->settings['spamfilter']) && $this->settings['spamfilter'];
    }

    private function getSubmittedComment(): array
    {
        // reformat data
        $website = $this->form->getField('website')->getValue();
        if ($website === 'http://' || trim($website) === '') {
            $website = null;
        }

        return [
            'post_id' => $this->blogPost['id'],
            'language' => LANGUAGE,
            'created_on' => FrontendModel::getUTCDate(),
            'author' => $this->form->getField('author')->getValue(),
            'email' => $this->form->getField('email')->getValue(),
            'website' => $website,
            'text' => $this->form->getField('message')->getValue(),
            'status' => 'published',
            'data' => serialize(['server' => $_SERVER]),
        ];
    }

    private function handleForm(): void
    {
        if (!$this->isCommentingAllowed() || !$this->form->isSubmitted() || !$this->validateForm()) {
            return;
        }

        $comment = $this->getSubmittedComment();

        // flag the comment for moderation when enabled and the commenter hasn't been moderated before
        if ($this->isModerationFilterEnabled()
            && !FrontendBlogModel::isModerated($comment['author'], $comment['email'])) {
            $comment['status'] = 'moderation';
        }

        // should we check if the item is spam
        if ($this->isSpamFilterEnabled()) {
            // check for spam
            $result = FrontendModel::isSpam(
                $comment['text'],
                SITE_URL . $this->blogPost['full_url'],
                $comment['author'],
                $comment['email'],
                $comment['website']
            );

            // if the comment is spam alter the comment status so it will appear in the spam queue
            if ($result) {
                $comment['status'] = 'spam';
            } elseif ($result === 'unknown') {
                // if the status is unknown then we should moderate it manually
                $comment['status'] = 'moderation';
            }
        }

        $comment['id'] = FrontendBlogModel::insertComment($comment);

        $comment['post_title'] = $this->blogPost['title'];
        $comment['post_url'] = $this->blogPost['url'];
        FrontendBlogModel::notifyAdmin($comment);
        $this->storeAuthorDataInCookies($comment);

        // store timestamp in session so we can block excessive usage
        FrontendModel::getSession()->set('blog_comment_' . $this->blogPost['id'], time());


        $this->redirect($this->getRedirectLink($this->blogPost['full_url'], $comment));
    }

    private function getRedirectLink(string $blogPostLink, array $comment)
    {
        $redirectLink = $blogPostLink . (mb_strpos($blogPostLink, '?') === false ? '?' : '&');

        switch ($comment['status']) {
            case 'moderation':
                return $redirectLink . 'comment=moderation#' . FL::act('Comment');
            case 'spam':
                return $redirectLink . 'comment=spam#' . FL::act('Comment');
            case 'published':
                return $redirectLink . 'comment=true#comment-' . $comment['id'];
            default:
                return $redirectLink;
        }
    }

    private function storeAuthorDataInCookies(array $comment): void
    {
        try {
            $cookie = FrontendModel::getContainer()->get('fork.cookie');
            $cookie->set('comment_author', $comment['author']);
            $cookie->set('comment_email', $comment['email']);
            $cookie->set('comment_website', $comment['website']);
        } catch (\RuntimeException $e) {
            // settings cookies isn't allowed, but because this isn't a real problem we ignore the exception
        }
    }

    private function validateForm(): bool
    {
        $this->form->cleanupFields();

        // does the key exists?
        if (FrontendModel::getSession()->has('blog_comment_' . $this->blogPost['id'])) {
            // calculate difference
            $diff = time() - (int) FrontendModel::getSession()->get('blog_comment_' . $this->blogPost['id']);

            // calculate difference, it it isn't 10 seconds the we tell the user to slow down
            if ($diff < 10 && $diff !== 0) {
                $this->form->getField('message')->addError(FL::err('CommentTimeout'));
            }
        }

        // validate required fields
        $this->form->getField('author')->isFilled(FL::err('AuthorIsRequired'));
        $this->form->getField('email')->isEmail(FL::err('EmailIsRequired'));
        $this->form->getField('message')->isFilled(FL::err('MessageIsRequired'));

        // validate optional fields
        if ($this->form->getField('website')->isFilled()
            && $this->form->getField('website')->getValue() !== 'http://') {
            $this->form->getField('website')->isURL(FL::err('InvalidURL'));
        }

        return $this->form->isCorrect();
    }
}
