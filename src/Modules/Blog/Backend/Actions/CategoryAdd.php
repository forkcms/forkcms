<?php

namespace ForkCMS\Modules\Blog\Backend\Actions;

use ForkCMS\Modules\Backend\Domain\Action\AbstractFormActionController;
use ForkCMS\Modules\Blog\Domain\Category\CategoryType;
use ForkCMS\Modules\Blog\Domain\Category\Command\CreateCategory;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryAdd extends AbstractFormActionController
{

    protected function getFormResponse(Request $request): ?Response
    {
        $createCategory = new CreateCategory();
        $createCategory->locale = Locale::from($this->translator->getLocale());

        return $this->handleForm(
            request: $request,
            formType: CategoryType::class,
            formData: $createCategory,
            redirectResponse: new RedirectResponse(CategoryIndex::getActionSlug()->generateRoute($this->router))
        );
    }
}
