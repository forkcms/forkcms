<?php

namespace ForkCMS\Modules\Blog\Backend\Actions;

use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessage;
use ForkCMS\Modules\Backend\Domain\Action\AbstractFormActionController;
use ForkCMS\Modules\Blog\Domain\Article\Article;
use ForkCMS\Modules\Blog\Domain\Article\ArticleType;
use ForkCMS\Modules\Blog\Domain\Article\Command\EditArticle;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BlogPostEdit extends AbstractFormActionController
{
    protected function getFormResponse(Request $request): ?Response
    {
        /** @var Article $article */
        $article = $this->getEntityFromRequest($request, Article::class);
        $this->assign('article', $article);

        return $this->handleForm(
            request: $request,
            formType: ArticleType::class,
            formData: new EditArticle($article),
            flashMessage: FlashMessage::success('Edited'),
            redirectResponse: new RedirectResponse(BlogIndex::getActionSlug()->generateRoute($this->router)),
        );
    }
}
