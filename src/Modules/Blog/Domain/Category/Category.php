<?php

namespace ForkCMS\Modules\Blog\Domain\Category;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ForkCMS\Modules\Blog\Domain\BlogPost\BlogPost;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: 'blog__categories')]
#[ORM\HasLifecycleCallbacks]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'language', type: 'string', length: 5, enumType: Locale::class)]
    private Locale $locale;

    #[ORM\Column(type: 'string')]
    private string $title;

    // TODO meta

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: BlogPost::class)]
    private Collection $posts;
}
