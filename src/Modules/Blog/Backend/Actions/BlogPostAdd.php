<?php

namespace ForkCMS\Modules\Blog\Backend\Actions;

use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessage;
use ForkCMS\Modules\Backend\Domain\Action\AbstractFormActionController;
use ForkCMS\Modules\Blog\Domain\Article\ArticleType;
use ForkCMS\Modules\Blog\Domain\Article\Command\CreateArticle;
use ForkCMS\Modules\ContentBlocks\Backend\Actions\ContentBlockIndex;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BlogPostAdd extends AbstractFormActionController
{
    protected function getFormResponse(Request $request): ?Response
    {
        $createBlogPost = new CreateArticle();
        $createBlogPost->locale = Locale::from($this->translator->getLocale());

        return $this->handleForm(
            request: $request,
            formType: ArticleType::class,
            formData: $createBlogPost,
            flashMessage: FlashMessage::success('Added'),
            redirectResponse: new RedirectResponse(BlogIndex::getActionSlug()->generateRoute($this->router))
        );
    }
}
