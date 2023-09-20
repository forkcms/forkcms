<?php

namespace ForkCMS\Modules\Blog\Domain\BlogPost;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ForkCMS\Modules\Backend\Domain\User\Blameable;
use ForkCMS\Modules\Blog\Domain\Category\Category;
use ForkCMS\Modules\Blog\Domain\Comment\Comment;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use Pageon\DoctrineDataGridBundle\Attribute\DataGrid;

#[ORM\Entity(repositoryClass: BlogPostRepository::class)]
#[ORM\Table(name: 'blog__posts')]
#[DataGrid('BlogPost')]
#[ORM\HasLifecycleCallbacks]
final class BlogPost
{
    use Blameable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'revision_id', type: 'integer')]
    private int $revisionId;

    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'posts')]
    private Category $category;

    // TDDO meta

    #[ORM\Column(name: 'language', type: 'string', length: 5, enumType: Locale::class)]
    private Locale $locale;

    #[ORM\Column(type: 'string')]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $introduction;

    #[ORM\Column(type: 'text')]
    private string $text;

    // TODO image

    #[ORM\Column(type: 'string', enumType: Status::class, options: ['default' => 'draft'])]
    private Status $status;

    #[ORM\Column(type: 'boolean')]
    private bool $hidden;

    #[ORM\Column(type: 'boolean')]
    private bool $allowComments;

    #[ORM\Column(type: 'integer')]
    private int $numberOfComments;
}
