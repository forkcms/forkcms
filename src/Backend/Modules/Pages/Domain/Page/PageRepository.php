<?php

namespace Backend\Modules\Pages\Domain\Page;

use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtra;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtraType;
use Backend\Modules\Pages\Domain\PageBlock\PageBlock;
use Common\Doctrine\Entity\Meta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Page|null find($id, $lockMode = null, $lockVersion = null)
 * @method Page|null findOneBy(array $criteria, array $orderBy = null)
 * @method Page[]    findAll()
 * @method Page[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }

    public function add(Page $page): void
    {
        $this->getEntityManager()->persist($page);
    }

    public function save(Page $page): void
    {
        $this->getEntityManager()->flush($page);
    }

    public function remove(Page $page): void
    {
        $this->getEntityManager()->remove($page);
        $this->getEntityManager()->flush($page);
    }

    public function archive(int $id, string $language): void
    {
        $this
            ->createQueryBuilder('p')
            ->set('p.status', Status::archive())
            ->where('p.id = :id')
            ->andWhere('p.language = :language')
            ->setParameters(
                [
                    'id' => $id,
                    'language' => $language,
                ]
            )
            ->getQuery()
            ->execute();
    }

    public function deleteByRevisionIds(array $ids): void
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->delete()
            ->where($qb->expr()->in('p.revisionId', ':ids'))
            ->setParameter('ids', $ids)
            ->getQuery()
            ->execute();

        $this->getEntityManager()->clear(Page::class);
    }

    public function deleteByIdAndUserIdAndStatusAndLanguage(
        int $id,
        int $userId,
        Status $status,
        string $language
    ): void {
        $qb = $this
            ->createQueryBuilder('p')
            ->delete()
            ->where('p.id = :id')
            ->andWhere('p.userId = :userId')
            ->andWhere('p.status = :status')
            ->andWhere('p.language = :language')
            ->setParameters(
                [
                    'id' => $id,
                    'userId' => $userId,
                    'status' => $status,
                    'language' => $language,
                ]
            )
            ->getQuery()
            ->execute();

        $this->getEntityManager()->clear(PageBlock::class);
    }

    public function get(int $id, int $revisionId = null, string $language = null): array
    {
        $qb = $this->buildGetQuery($id, $revisionId, $language);

        // @todo This wil not be necessary when we can return the entities instead of arrays
        $results = $qb->getQuery()->getScalarResult();
        foreach ($results as &$result) {
            $result = $this->processFields($result);
        }

        return $results;
    }

    public function getOne(int $id, int $revisionId = null, string $language = null): ?array
    {
        $qb = $this->buildGetQuery($id, $revisionId, $language);
        $qb->setMaxResults(1);

        // @todo This wil not be necessary when we can return the entities instead of arrays
        $results = $qb->getQuery()->getScalarResult();

        if (count($results) === 0) {
            return null;
        }

        return $this->processFields($results[0]);
    }

    public function getLatestVersion(int $id, string $language): ?int
    {
        $qb = $this->createQueryBuilder('p');

        $qb
            ->select('MAX(p.revisionId)')
            ->where('p.id = :id')
            ->andWhere('p.language = :language')
            ->andWhere('p.status != :status');

        $qb->setParameters(
            [
                'id' => $id,
                'language' => $language,
                'status' => Status::archive(),
            ]
        );

        return $qb
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getMaximumPageId(string $language, bool $isGodUser): int
    {
        $qb = $this->createQueryBuilder('p');

        $qb
            ->select('MAX(p.id)')
            ->where('p.language = :language');

        $qb->setParameters(
            [
                'language' => $language,
            ]
        );

        $max = $qb
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult();

        // pages created by a user that isn't a god should have an id higher then 1000
        // with this hack we can easily find which pages are added by a user
        if ($max < 1000 && !$isGodUser) {
            return $max + 1000;
        }

        return $max;
    }

    public function getMaximumSequence(int $parentId, string $language, string $type = null): int
    {
        $qb = $this->createQueryBuilder('p');

        $qb
            ->select('MAX(p.sequence)')
            ->where('p.language = :language')
            ->andWhere('p.parentId = :parentId');

        $parameters = [
            'language' => $language,
            'parentId' => $parentId,
        ];

        if ($type !== null) {
            $qb->andWhere('p.type = :type');
            $parameters['type'] = $type;
        }

        $qb->setParameters($parameters);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function getNewSequenceForMove(int $parentId, string $language): int
    {
        return $this
            ->createQueryBuilder('p')
            ->select('COALESCE(MAX(p.sequence), 0)')
            ->where('p.id = :parentId')
            ->andWhere('p.language = :language')
            ->andWhere('p.status = :status')
            ->setParameters(
                [
                    'parentId' => $parentId,
                    'language' => $language,
                    'status' => Status::active(),
                ]
            )
            ->getQuery()
            ->getSingleScalarResult() + 1;
    }

    public function incrementSequence(int $parentId, string $language, int $sequence): void
    {
        $this
            ->createQueryBuilder('p')
            ->set('p.sequence', 'p.sequence + 1')
            ->where('p.parentId = :parentId')
            ->andWhere('p.language = :language')
            ->andWhere('p.sequence > :sequence')
            ->setParameters(
                [
                    'parentId' => $parentId,
                    'language' => $language,
                    'sequence' => $sequence,
                ]
            )
            ->getQuery()
            ->execute();
    }

    public function getPageTree(array $parentIds, string $language, array $data = null, int $level = 1): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select('p.id')
            ->addSelect('p.title')
            ->addSelect('p.parentId as parent_id')
            ->addSelect('p.navigationTitle as navigation_title')
            ->addSelect('p.type')
            ->addSelect('p.hidden')
            ->addSelect('p.data')
            ->addSelect('m.url')
            ->addSelect('m.data as meta_data')
            ->addSelect('m.seoFollow as seo_follow')
            ->addSelect('m.seoIndex as seo_index')
            ->addSelect('p.allowChildren as allow_children')
            ->addSelect('ifelse(count(e.id) > 0, 1, 0) AS has_extra')
            ->addSelect('group_concat(b.extraId) AS extra_ids')
            ->addSelect('ifelse(count(p2.id) != 0, 1, 0) AS has_children')
        ;
        $qb
            ->from(Page::class, 'p', 'p.id')
            ->innerJoin(Meta::class, 'm', Join::WITH, 'p.meta = m.id')
            ->leftJoin(PageBlock::class, 'b', Join::WITH, 'b.revisionId = p.revisionId')
            ->leftJoin(ModuleExtra::class, 'e', Join::WITH, 'e.id = b.extraId AND e.type = :type')
            ->leftJoin(Page::class, 'p2', Join::WITH, 'p2.parentId = p.id AND p2.status = :active AND p2.hidden = :hidden AND p2.data NOT LIKE :data AND p2.language = :language')
            ->where($qb->expr()->in('p.parentId', ':parentIds'))
            ->andWhere('p.status = :status')
            ->andWhere('p.language = :language')
            ->groupBy('p.revisionId')
            ->orderBy('p.sequence', 'ASC')
        ;

        $qb->setParameters(
            [
                'active' => 'active',
                'data' => '%s:9:\"is_action\";b:1;%',
                'hidden' => 'N',
                'language' => $language,
                'parentIds' => $parentIds,
                'status' => Status::active(),
                'type' => 'block',
            ]
        );

        return $qb
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

    public function getFirstChild(int $pageId, Status $status, string $language): ?Page
    {
        return $this
            ->createQueryBuilder('p')
            ->where('p.parentId = :id')
            ->andWhere('p.status = :status')
            ->andWhere('p.language = :language')
            ->orderBy('p.sequence', 'ASC')
            ->setMaxResults(1)
            ->setParameters(
                [
                    'id' => $pageId,
                    'status' => $status,
                    'language' => $language,
                ]
            )
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getRevisionId(int $id, Status $status, string $language): int
    {
        return (int) $this
            ->createQueryBuilder('p')
            ->select('p.revisionId')
            ->where('p.id = :id')
            ->andWhere('p.status = :status')
            ->andWhere('p.language = :language')
            ->setParameters(
                [
                    'id' => $id,
                    'status' => $status,
                    'language' => $language,
                ]
            )
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getRevisionIdsToKeep(int $id, int $rowsToKeep): array
    {
        $result = $this
            ->createQueryBuilder('p')
            ->select('p.revisionId')
            ->where('p.id = :id')
            ->andWhere('p.status = :status')
            ->orderBy('p.editedOn', 'DESC')
            ->setMaxResults($rowsToKeep)
            ->setParameters(
                [
                    'id' => $id,
                    'status' => Status::archive(),
                ]
            )
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_SCALAR);

        return array_column($result, 'revisionId');
    }

    public function getRevisionIdsToDelete(int $id, Status $status, array $revisionsToKeep): array
    {
        $qb = $this
            ->createQueryBuilder('p')
            ->select('p.revisionId');
        $qb
            ->where('p.id = :id')
            ->andWhere('p.status = :status')
            ->andWhere($qb->expr()->notIn('p.revisionId', ':revisionsToKeep'));

        $qb->setParameters(
            [
                'id' => $id,
                'status' => $status,
                'revisionsToKeep' => $revisionsToKeep,
            ]
        );

        $result = $qb
            ->getQuery()
            ->getResult();

        return array_column($result, 'revisionId');
    }

    /**
     * @return Page[]
     */
    public function findActivePages(): array
    {
        return $this
            ->createQueryBuilder('p')
            ->where('p.status = :status')
            ->setParameter('status', Status::active())
            ->getQuery()
            ->getResult();
    }

    public function findOneByParentsAndUrlAndStatusAndLanguage(
        array $parentIds,
        string $url,
        Status $status,
        string $language
    ): ?Page {
        $qb = $this
            ->createQueryBuilder('p')
            ->join('p.meta', 'meta');

        $qb
            ->where($qb->expr()->in('p.parentId', ':parentIds'))
            ->andWhere('p.status = :status')
            ->andWhere('meta.url = :url')
            ->andWhere('p.language = :language');

        $qb->setParameters(
            [
                'parentIds' => $parentIds,
                'status' => $status,
                'url' => $url,
                'language' => $language,
            ]
        );

        return $qb
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByParentsAndUrlAndStatusAndLanguageExcludingId(
        array $parentIds,
        string $url,
        Status $status,
        string $language,
        int $excludedId
    ): ?Page {
        $qb = $this
            ->createQueryBuilder('p')
            ->join('p.meta', 'meta');

        $qb
            ->where($qb->expr()->in('p.parentId', ':parentIds'))
            ->andWhere('p.status = :status')
            ->andWhere('meta.url = :url')
            ->andWhere('p.id <> :id')
            ->andWhere('p.language = :language');

        $qb->setParameters(
            [
                'parentIds' => $parentIds,
                'status' => $status,
                'url' => $url,
                'id' => $excludedId,
                'language' => $language,
            ]
        );

        return $qb
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function pageExistsWithModuleBlockForLanguage(string $module, string $language): bool
    {
        $results = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('1')
            ->from(Page::class, 'p')
            ->innerJoin(PageBlock::class, 'b', Join::WITH, 'p.revisionId = b.revisionId')
            ->innerJoin(ModuleExtra::class, 'e', Join::WITH, 'e.id = b.extraId')
            ->where('e.module = :module')
            ->andWhere('p.language = :language')
            ->setMaxResults(1)
            ->setParameters(
                [
                    'module' => $module,
                    'language' => $language,
                ]
            )
            ->getQuery()
            ->getScalarResult();

        return count($results) === 1;
    }

    public function pageExistsWithModuleActionForLanguage(string $module, string $action, string $language): bool
    {
        $results = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('1')
            ->from(Page::class, 'p')
            ->innerJoin(PageBlock::class, 'b', Join::WITH, 'p.revisionId = b.revisionId')
            ->innerJoin(ModuleExtra::class, 'e', Join::WITH, 'e.id = b.extraId')
            ->where('e.module = :module')
            ->andWhere('e.action = :action')
            ->andWhere('p.language = :language')
            ->setMaxResults(1)
            ->setParameters(
                [
                    'module' => $module,
                    'action' => $action,
                    'language' => $language,
                ]
            )
            ->getQuery()
            ->getScalarResult();

        return count($results) === 1;
    }

    private function buildGetQuery(int $pageId, ?int $revisionId, ?string $language): QueryBuilder
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select('p')
            ->addSelect('m.id as meta_id')
            ->addSelect('unix_timestamp(p.publishOn) as publish_on')
            ->addSelect('unix_timestamp(p.createdOn) as created_on')
            ->addSelect('unix_timestamp(p.editedOn) as edited_on')
            ->addSelect('ifelse(count(e.id) > 0, 1, 0) as has_extra')
            ->addSelect('group_concat(b.extraId) as extra_ids')
            ->from(Page::class, 'p')
            ->leftJoin(Meta::class, 'm', Join::WITH, 'p.meta = m.id')
            ->leftJoin(PageBlock::class, 'b', Join::WITH, 'b.revisionId = p.revisionId AND b.extraId IS NOT NULL')
            ->leftJoin(ModuleExtra::class, 'e', Join::WITH, 'e.id = b.extraId AND e.type = :type')
            ->where('p.id = :id')
            ->groupBy('p.revisionId')
        ;

        $parameters = [
            'type' => ModuleExtraType::block(),
            'id' => $pageId,
        ];

        if ($revisionId !== null) {
            $qb->andWhere('p.revisionId = :revisionId');
            $parameters['revisionId'] = $revisionId;
        }

        if ($language !== null) {
            $qb->andWhere('p.language = :language');
            $parameters['language'] = $language;
        }

        $qb->setParameters($parameters);

        return $qb;
    }

    private function processFields($result): array
    {
        $result = self::removeTablePrefixes($result);

        $result['move_allowed'] = (bool) $result['allow_move'];
        $result['children_allowed'] = (bool) $result['allow_children'];
        $result['delete_allowed'] = (bool) $result['allow_delete'];

        if (Page::isForbiddenToDelete($result['id'])) {
            $result['allow_delete'] = false;
        }

        if (Page::isForbiddenToMove($result['id'])) {
            $result['allow_move'] = false;
        }

        if (Page::isForbiddenToHaveChildren($result['id'])) {
            $result['allow_children'] = false;
        }

        // convert into bools for use in template engine
        $result['edit_allowed'] = (bool) $result['allow_edit'];
        $result['has_extra'] = (bool) $result['has_extra'];

        // unserialize data
        if ($result['data'] !== null) {
            $result['data'] = unserialize($result['data'], ['allowed_classes' => false]);
        }

        return $result;
    }

    private static function removeTablePrefixes($result, string $prefix = 'p_'): array
    {
        foreach ($result as $key => $value) {
            if (strpos($key, $prefix) === 0) {
                unset($result[$key]);
                $key = self::convertToSnakeCase(substr($key, 2));
            }

            if (is_bool($value)) {
                if ($value === true) {
                    $value = '1';
                } else {
                    $value = '0';
                }
            }

            $result[$key] = $value;
        }

        return $result;
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
}
