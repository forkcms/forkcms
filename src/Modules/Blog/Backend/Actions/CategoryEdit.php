<?php

namespace ForkCMS\Modules\Blog\Backend\Actions;

use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessage;
use ForkCMS\Modules\Backend\Domain\Action\AbstractFormActionController;
use ForkCMS\Modules\Blog\Domain\Category\Category;
use ForkCMS\Modules\Blog\Domain\Category\CategoryType;
use ForkCMS\Modules\Blog\Domain\Category\Command\EditCategory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryEdit extends AbstractFormActionController
{
    protected function getFormResponse(Request $request): ?Response
    {
        /** @var Category $category */
        $category = $this->getEntityFromRequest($request, Category::class);

        $this->assign('category', $category);

        return $this->handleForm(
            request: $request,
            formType: CategoryType::class,
            formData: new EditCategory($category),
            flashMessage: FlashMessage::success('Edited'),
            redirectResponse: new RedirectResponse(CategoryIndex::getActionSlug()->generateRoute($this->router)),
        );
    }
}
