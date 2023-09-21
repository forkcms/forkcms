<?php

namespace ForkCMS\Modules\Blog\Domain\Category\Command;

use ForkCMS\Modules\Blog\Domain\Category\Category;
use ForkCMS\Modules\Blog\Domain\Category\CategoryRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateCategoryHandler
{
    public function __construct(private readonly CategoryRepository $categoryRepository)
    {
    }

    public function __invoke(CreateCategory $createCategory): void
    {
        $category = Category::fromDataTransferObject($createCategory);
        $this->categoryRepository->save($category);
    }
}
