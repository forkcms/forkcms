<?php

namespace Backend\Modules\Pages\Domain\PageBlock;

use Backend\Modules\Pages\Domain\Page\Page;
use Backend\Modules\Pages\Domain\Page\Status;
use Common\Locale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\AbstractQuery;
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
        $this->getEntityManager()->persist($pageBlock);
    }

    public function save(PageBlock $pageBlock): void
    {
        $this->getEntityManager()->flush($pageBlock);
    }

    public function deleteByRevisionIds(array $ids): void
    {
        $qb = $this->createQueryBuilder('b');
        $qb
            ->delete()
            ->where($qb->expr()->in('b.revisionId', ':ids'))
            ->setParameter('ids', $ids)
            ->getQuery()
            ->execute();

        $this->getEntityManager()->clear(PageBlock::class);
    }

    public function deleteByExtraId(int $extraId): void
    {
        $this
            ->createQueryBuilder('b')
            ->delete()
            ->where('b.extraId = :extraId')
            ->setParameter('extraId', $extraId)
            ->getQuery()
            ->execute();

        $this->getEntityManager()->clear(PageBlock::class);
    }

    public function clearExtraId(int $extraId): void
    {
        $this
            ->createQueryBuilder('b')
            ->where('b.extraId = :extraId')
            ->set('b.extraId', null)
            ->setParameter('extraId', $extraId)
            ->getQuery()
            ->execute();
    }

    /**
     * @return PageBlock[]
     */
    public function getBlocksForPage(int $pageId, int $revisionId, Locale $locale): array
    {
        $qb = $this->createQueryBuilder('b');

        $qb
            ->select('b')
            ->addSelect('unix_timestamp(b.createdOn) as created_on')
            ->addSelect('unix_timestamp(b.editedOn) as edited_on')
            // @todo Use relation when it exists
            ->innerJoin(Page::class, 'p', Join::WITH, 'b.revisionId = p.revisionId')
            ->where('p.id = :pageId')
            ->andWhere('p.revisionId = :revisionId')
            ->andWhere('p.locale = :locale')
            ->orderBy('b.sequence', 'ASC');

        $qb->setParameters(
            [
                'pageId' => $pageId,
                'revisionId' => $revisionId,
                'locale' => $locale,
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
                    $key = self::convertToSnakeCase(substr($key, 2));
                }
                $result[$key] = $value;
            }
        }

        return $results;
    }

    private static function convertToSnakeCase(string $key): string
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $key, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = ($match === strtoupper($match) ? strtolower($match) : lcfirst($match));
        }
        unset($match);
        $key = implode('_', $ret);

        return $key;
    }

    public function moduleExtraExistsForLocale(int $moduleExtraId, Locale $locale): bool
    {
        $qb = $this->createQueryBuilder('b');

        $qb->select('1')
            ->innerJoin(
                Page::class,
                'p',
                Join::WITH,
                'b.revisionId = p.revisionId AND b.extraId = :extraId AND p.locale = :locale AND p.status = :active'
            )
            ->setMaxResults(1);

        $qb->setParameters(
            [
                'extraId' => $moduleExtraId,
                'active' => Status::active(),
                'locale' => $locale,
            ]
        );

        return (bool) $qb->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);
    }
}
