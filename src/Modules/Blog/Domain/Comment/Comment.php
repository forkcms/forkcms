<?php

namespace ForkCMS\Modules\Blog\Domain\Comment;

use Doctrine\ORM\Mapping as ORM;
use DateTime;
use ForkCMS\Modules\Blog\Domain\BlogPost\BlogPost;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\Table(name: 'blog__comments')]
#[ORM\HasLifecycleCallbacks]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'integer')]
    private int $postId;

    #[ORM\Column(name: 'language', type: 'string', length: 5, enumType: Locale::class)]
    private Locale $locale;

    #[ORM\Column(type: 'string')]
    private string $author;

    #[ORM\Column(type: 'string')]
    private string $email;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $website;

    #[ORM\Column(type: 'text')]
    private string $text;

    #[ORM\Column(type: 'text')]
    private string $data;

    #[ORM\Column(type: 'string', enumType: Status::class, options: ['default' => 'moderation'])]
    private Status $status;

    #[ORM\Column(type: 'datetime')]
    private DateTime $createdOn;
}
