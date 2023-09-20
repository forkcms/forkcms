<?php

namespace ForkCMS\Modules\Blog\Domain\Comment;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry) {
        parent::__construct($managerRegistry, Comment::class);
    }

    public function save(Comment $comment): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($comment);
        $entityManager->flush();
    }

    public function remove(Comment $comment): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($comment);
        $entityManager->flush();
    }
}
