<?php

namespace Backend\Modules\Pages\Domain\PageBlock;

use Backend\Modules\Pages\Domain\Page\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @method PageBlock|null find($id, $lockMode = null, $lockVersion = null)
 * @method PageBlock|null findOneBy(array $criteria, array $orderBy = null)
 * @method PageBlock[]    findAll()
 * @method PageBlock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageBlockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageBlock::class);
    }

    public function add(PageBlock $pageBlock): void
    {
        // We don't flush here, see http://disq.us/p/okjc6b
        $this->getEntityManager()->persist($pageBlock);
    }

    public function save(PageBlock $pageBlock): void
    {
        $this->getEntityManager()->flush($pageBlock);
    }

    public function deleteByRevisionIds(array $ids): void
    {
        $this
            ->createQueryBuilder('em')
            ->delete()
            ->where('em.revisionId IN :ids')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->execute();
    }

    public function deleteByExtraId(int $extraId): void
    {
        $this
            ->createQueryBuilder('em')
            ->delete()
            ->where('em.extraId = :extraId')
            ->setParameter('extraId', $extraId)
            ->getQuery()
            ->execute();
    }

    public function clearExtraId(int $extraId): void
    {
        $this
            ->createQueryBuilder('em')
            ->where('em.extraId = :extraId')
            ->set('em.extraId', null)
            ->getQuery()
            ->execute();
    }

    public function getBlocksForPage(int $pageId, int $revisionId, string $language): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select('b')
            ->addSelect('unix_timestamp(b.createdOn) as created_on')
            ->addSelect('unix_timestamp(b.editedOn) as edited_on')
            ->from(PageBlock::class, 'b')
            // @todo Use relation when it exists
            ->innerJoin(Page::class, 'p', Join::WITH, 'b.revisionId = p.revisionId')
            ->where('p.id = :pageId')
            ->andWhere('p.revisionId = :revisionId')
            ->andWhere('p.language = :language')
            ->orderBy('b.sequence', 'ASC');

        $qb->setParameters(
            [
                'pageId' => $pageId,
                'revisionId' => $revisionId,
                'language' => $language,
            ]
        );

        $results = $qb
            ->getQuery()
            ->getScalarResult();

        // @todo This wil not be necessary when we can return the entities instead of arrays
        foreach ($results as &$result) {
            foreach ($result as $key => $value) {
                if (strpos($key, 'b_') === 0) {
                    unset($result[$key]);
                    $key = substr($key, 2);

                    preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $key, $matches);
                    $ret = $matches[0];
                    foreach ($ret as &$match) {
                        $match = ($match == strtoupper($match) ? strtolower($match) : lcfirst($match));
                    }
                    $key = implode('_', $ret);
                }
                $result[$key] = $value;
            }
        }

        return $results;
    }
}
