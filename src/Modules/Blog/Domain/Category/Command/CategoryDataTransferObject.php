<?php

namespace ForkCMS\Modules\Blog\Domain\Category\Command;

use ForkCMS\Modules\Blog\Domain\Category\Category;
use ForkCMS\Modules\Frontend\Domain\Meta\Meta;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use Symfony\Component\Validator\Constraints as Assert;

class CategoryDataTransferObject
{
    protected ?Category $categoryEntity;

    public int $id;

    public Locale $locale;

    public ?Meta $meta = null;

    #[Assert\NotBlank()]
    public string $title;

    public function __construct(?Category $categoryEntity = null)
    {
        $this->categoryEntity = $categoryEntity;

        if (!$categoryEntity instanceof Category) {
            return;
        }

        $this->id = $categoryEntity->getId();
        $this->locale = $categoryEntity->getLocale();
        $this->title = $categoryEntity->getTitle();
        $this->meta = clone $categoryEntity->getMeta();
    }

    public function getEntity(): ?Category
    {
        return $this->categoryEntity;
    }
}
