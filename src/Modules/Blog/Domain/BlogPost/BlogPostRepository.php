<?php

namespace ForkCMS\Modules\Blog\Domain\BlogPost;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;

class BlogPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry) {
        parent::__construct($managerRegistry, BlogPost::class);
    }

    public function save(BlogPost $blogPost): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($blogPost);
        $entityManager->flush();
    }

    public function remove(BlogPost $blogPost): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($blogPost);
        $entityManager->flush();
    }
}
