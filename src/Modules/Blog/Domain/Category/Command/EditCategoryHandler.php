<?php

namespace ForkCMS\Modules\Blog\Domain\Category\Command;

use ForkCMS\Modules\Blog\Domain\Category\Category;
use ForkCMS\Modules\Blog\Domain\Category\CategoryRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class EditCategoryHandler
{
    public function __construct(private readonly CategoryRepository $categoryRepository)
    {
    }

    public function __invoke(EditCategory $editCategory): void
    {
        /** @var Category $category */
        $category = $editCategory->getEntity();

        $category->update($editCategory);
        $this->categoryRepository->save($category);
    }
}
