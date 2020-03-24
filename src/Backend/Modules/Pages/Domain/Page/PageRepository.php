<?php

namespace Backend\Modules\Pages\Domain\Page;

use Backend\Core\Engine\Model;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtra;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtraType;
use Backend\Modules\Pages\Domain\PageBlock\PageBlock;
use Backend\Modules\Pages\Engine\Model as BackendPagesModel;
use Common\Doctrine\Entity\Meta;
use Common\Locale;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use ForkCMS\App\ForkController;
use Frontend\Core\Language\Language;

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

    public function archive(int $id, Locale $locale): void
    {
        $this
            ->createQueryBuilder('p')
            ->set('p.status', Status::archive())
            ->where('p.id = :id')
            ->andWhere('p.locale = :locale')
            ->setParameters(
                [
                    'id' => $id,
                    'locale' => $locale,
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

        $this->getEntityManager()->clear(PageBlock::class);
        $this->getEntityManager()->clear(Page::class);
    }

    public function deleteByIdAndUserIdAndStatusAndLocale(
        int $id,
        int $userId,
        Status $status,
        Locale $locale
    ): void {
        $this
            ->createQueryBuilder('p')
            ->delete()
            ->where('p.id = :id')
            ->andWhere('p.userId = :userId')
            ->andWhere('p.status = :status')
            ->andWhere('p.locale = :locale')
            ->setParameters(
                [
                    'id' => $id,
                    'userId' => $userId,
                    'status' => $status,
                    'locale' => $locale,
                ]
            )
            ->getQuery()
            ->execute();

        $this->getEntityManager()->clear(PageBlock::class);
        $this->getEntityManager()->clear(Page::class);
    }

    public function get(int $id, int $revisionId = null, Locale $locale = null): array
    {
        $qb = $this->buildGetQuery($id, $revisionId, $locale);

        // @todo This wil not be necessary when we can return the entities instead of arrays
        $results = $qb->getQuery()->getScalarResult();
        foreach ($results as &$result) {
            $result = $this->processFields($result);
        }

        return $results;
    }

    public function getOne(int $id, int $revisionId = null, Locale $locale = null): ?array
    {
        $qb = $this->buildGetQuery($id, $revisionId, $locale);
        $qb->setMaxResults(1);

        // @todo This wil not be necessary when we can return the entities instead of arrays
        $results = $qb->getQuery()->getScalarResult();

        if (count($results) === 0) {
            return null;
        }

        return $this->processFields($results[0]);
    }

    public function getLatestForApi(int $id, Locale $locale = null): ?array
    {
        $qb = $this->buildGetQuery($id, null, $locale);
        $qb
            ->select('p, m')
            ->andWhere('p.status = :active')
            ->setParameter('active', Status::active())
            ->addOrderBy('p.revisionId', 'DESC')
            ->setMaxResults(1);

        $results = $qb->getQuery()->getArrayResult();

        if (count($results) === 0) {
            return null;
        }

        $result = [];

        foreach ($results[0] as $pageItemKey => $pageItemValue) {
            $result[$pageItemKey] = $pageItemValue;
        }

        // Unserialize data
        if (array_key_exists('data', $results)) {
            $result['data'] = unserialize($result['data'], ['allowed_classes' => false]);
        }

        $qb = $this
            ->getEntityManager()->createQueryBuilder()
            ->select('b')
            ->from(PageBlock::class, 'b')
            ->innerJoin('b.page', 'p')
            ->where('p.id = :pageId')
            ->andWhere('p.revisionId = :revisionId')
            ->andWhere('p.locale = :locale')
            ->orderBy('b.sequence', 'ASC');

        $qb->setParameters(
            [
                'pageId' => $id,
                'revisionId' => $result['revisionId'],
                'locale' => $locale,
            ]
        );

        $blocks = $qb
            ->getQuery()
            ->getArrayResult();

        $pageData = $result['data'] ?? [];
        if (!empty($pageData)) {
            $result['data'] = unserialize($pageData, ['allowed_classes' => false]);
        }
        $result['blocks'] = array_map(
            static function (array $block): array {
                $decodedHtml = json_decode($block['html'] ?? null, false);
                if ($decodedHtml !== null) {
                    $block['html'] = $decodedHtml;
                }

                return $block;
            },
            $blocks
        );

        $qb = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('e')
            ->from(ModuleExtra::class, 'e')
            ->where('e.id IN (:extraIds)')
            ->setParameters(
                [
                    'extraIds' => array_map(
                        function (array $block) {
                            return $block['extraId'];
                        },
                        $blocks
                    ),
                ]
            );

        $result['extras'] = array_map(
            static function (array $extra): array {
                $extraData = $extra['data'] ?? [];
                if (!empty($extraData)) {
                    $extra['data'] = unserialize($extraData, ['allowed_classes' => false]);
                }

                return $extra;
            },
            $qb->indexBy('e', 'e.id')->getQuery()->getArrayResult();
        );

        return $result;
    }

    public function getSubPagesForApi(int $parentId, Locale $locale): array
    {
        $subPageIds = $this
            ->createQueryBuilder('p')
            ->select('p.id')
            ->distinct()
            ->where('p.parentId = :parentId')
            ->andWhere('p.status = :active')
            ->andWhere('p.hidden = :false')
            ->andWhere('p.locale = :locale')
            ->andWhere('p.publishOn <= :now')
            ->setParameters(
                [
                    'parentId' => $parentId,
                    'active' => Status::active(),
                    'false' => false,
                    'locale' => $locale,
                    'now' => new DateTime(),
                ]
            )
            ->orderBy('p.sequence', 'ASC')
            ->getQuery()
            ->getScalarResult();

        $subPages = [];
        foreach ($subPageIds as $subPageIdArray) {
            $subPages[] = $this->getLatestForApi($subPageIdArray['id'], $locale);
        }

        return $subPages;
    }

    public function getLatestVersion(int $id, Locale $locale): ?int
    {
        $qb = $this->createQueryBuilder('p');

        $qb
            ->select('MAX(p.revisionId)')
            ->where('p.id = :id')
            ->andWhere('p.locale = :locale')
            ->andWhere('p.status != :status');

        $qb->setParameters(
            [
                'id' => $id,
                'locale' => $locale,
                'status' => Status::archive(),
            ]
        );

        return $qb
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getMaximumPageId(Locale $locale, bool $isGodUser): int
    {
        $qb = $this->createQueryBuilder('p');

        $qb
            ->select('MAX(p.id)')
            ->where('p.locale = :locale');

        $qb->setParameters(
            [
                'locale' => $locale,
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

    public function getMaximumSequence(int $parentId, Locale $locale, string $type = null): int
    {
        $qb = $this->createQueryBuilder('p');

        $qb
            ->select('MAX(p.sequence)')
            ->where('p.locale = :locale')
            ->andWhere('p.parentId = :parentId');

        $parameters = [
            'locale' => $locale,
            'parentId' => $parentId,
        ];

        if ($type !== null) {
            $qb->andWhere('p.type = :type');
            $parameters['type'] = $type;
        }

        $qb->setParameters($parameters);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function getNewSequenceForMove(int $parentId, Locale $locale): int
    {
        return $this
                   ->createQueryBuilder('p')
                   ->select('COALESCE(MAX(p.sequence), 0)')
                   ->where('p.id = :parentId')
                   ->andWhere('p.locale = :locale')
                   ->andWhere('p.status = :status')
                   ->setParameters(
                       [
                           'parentId' => $parentId,
                           'locale' => $locale,
                           'status' => Status::active(),
                       ]
                   )
                   ->getQuery()
                   ->getSingleScalarResult() + 1;
    }

    public function incrementSequence(int $parentId, Locale $locale, int $sequence): void
    {
        $this
            ->createQueryBuilder('p')
            ->set('p.sequence', 'p.sequence + 1')
            ->where('p.parentId = :parentId')
            ->andWhere('p.locale = :locale')
            ->andWhere('p.sequence > :sequence')
            ->setParameters(
                [
                    'parentId' => $parentId,
                    'locale' => $locale,
                    'sequence' => $sequence,
                ]
            )
            ->getQuery()
            ->execute();
    }

    public function getPageTree(array $parentIds, Locale $locale): array
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
            ->addSelect('p.allowMove as allow_move')
            ->addSelect('ifelse(count(e.id) > 0, 1, 0) AS has_extra')
            ->addSelect('group_concat(b.extraId) AS extra_ids')
            ->addSelect('ifelse(count(p2.id) != 0, 1, 0) AS has_children');
        $qb
            ->from(Page::class, 'p', 'p.id')
            ->innerJoin('p.meta', 'm')
            ->leftJoin('p.blocks', 'b')
            ->leftJoin(ModuleExtra::class, 'e', Join::WITH, 'e.id = b.extraId AND e.type = :type')
            ->leftJoin(
                Page::class,
                'p2',
                Join::WITH,
                'p2.parentId = p.id AND p2.status = :status ' .
                'AND p2.hidden = :hidden AND (p2.data NOT LIKE :data OR p2.data IS NULL) AND p2.locale = :locale'
            )
            ->where($qb->expr()->in('p.parentId', ':parentIds'))
            ->andWhere('p.status = :status')
            ->andWhere('p.locale = :locale')
            ->groupBy('p.revisionId')
            ->orderBy('p.sequence', 'ASC');

        $qb->setParameters(
            [
                'data' => '%s:9:\"is_action\";b:1;%',
                'hidden' => false,
                'locale' => $locale,
                'parentIds' => $parentIds,
                'status' => Status::active(),
                'type' => 'block',
            ]
        );

        return $qb
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

    public function getFirstChild(int $pageId, Status $status, Locale $locale): ?Page
    {
        return $this
            ->createQueryBuilder('p')
            ->where('p.parentId = :id')
            ->andWhere('p.status = :status')
            ->andWhere('p.locale = :locale')
            ->orderBy('p.sequence', 'ASC')
            ->setMaxResults(1)
            ->setParameters(
                [
                    'id' => $pageId,
                    'status' => $status,
                    'locale' => $locale,
                ]
            )
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getRevisionId(int $id, Status $status, Locale $locale): int
    {
        return (int) $this
            ->createQueryBuilder('p')
            ->select('p.revisionId')
            ->where('p.id = :id')
            ->andWhere('p.status = :status')
            ->andWhere('p.locale = :locale')
            ->setParameters(
                [
                    'id' => $id,
                    'status' => $status,
                    'locale' => $locale,
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

    public function getNavigationTitles(array $pageIds, Locale $locale, Status $status): array
    {
        $qb = $this->createQueryBuilder('p', 'p.id');

        $results = $qb
            ->select('p.id')
            ->addSelect('p.navigationTitle')
            ->where($qb->expr()->in('p.id', ':pageIds'))
            ->andWhere('p.locale = :locale')
            ->andWhere('p.status = :status')
            ->setParameters(
                [
                    'pageIds' => $pageIds,
                    'status' => $status,
                    'locale' => $locale,
                ]
            )
            ->getQuery()
            ->getScalarResult();

        return array_combine(array_column($results, 'id'), array_column($results, 'navigationTitle'));
    }

    public function getCacheExpirationDate(): ?DateTime
    {
        $result = $this
            ->createQueryBuilder('p')
            ->select('min(least(coalesce(p.publishOn, :now), coalesce(p.publishUntil, :future))) as min')
            ->where('p.status = :active')
            ->andWhere('p.publishOn > :now')
            ->setParameters(
                [
                    'now' => new DateTime(),
                    'future' => new DateTime('2099-12-31 23:59'),
                    'active' => Status::active(),
                ]
            )
            ->setMaxResults(1)
            ->getQuery()
            ->getScalarResult();

        if (count($result) !== 1) {
            return null;
        }

        $min = array_column($result, 'min')[0];

        if ($min === null) {
            return null;
        }

        return new DateTime($min);
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

    public function findOneByParentsAndUrlAndStatusAndLocale(
        array $parentIds,
        string $url,
        Status $status,
        Locale $locale,
        int $excludedId = null
    ): ?Page {
        $qb = $this
            ->createQueryBuilder('p')
            ->join('p.meta', 'meta');

        $qb
            ->where($qb->expr()->in('p.parentId', ':parentIds'))
            ->andWhere('p.status = :status')
            ->andWhere('meta.url = :url')
            ->andWhere('p.locale = :locale');

        $qb->setParameters(
            [
                'parentIds' => $parentIds,
                'status' => $status,
                'url' => $url,
                'locale' => $locale,
            ]
        );

        if ($excludedId !== null) {
            $qb
                ->andWhere('p.id <> :excludedId')
                ->setParameter('excludedId', $excludedId);
        }

        return $qb
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param int $extraId
     *
     * @return Page[]
     */
    public function findPagesWithoutExtra(int $extraId): array
    {
        $qb = $this->createQueryBuilder('p');

        $subQuery = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->from(PageBlock::class, 'b')
            ->where('b.extraId = :extraId')
            ->groupBy('b.revisionId');

        $qb
            ->select('p')
            ->where($qb->expr()->notIn('p.revisionId', $subQuery->getDQL()))
            ->setParameters(['extraId' => $extraId]);

        return $qb
            ->getQuery()
            ->getResult();
    }

    public function pageExistsWithModuleBlockForLocale(string $module, Locale $locale): bool
    {
        $results = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('1')
            ->from(Page::class, 'p')
            ->innerJoin('p.blocks', 'b')
            ->innerJoin(ModuleExtra::class, 'e', Join::WITH, 'e.id = b.extraId')
            ->where('e.module = :module')
            ->andWhere('p.locale = :locale')
            ->setMaxResults(1)
            ->setParameters(
                [
                    'module' => $module,
                    'locale' => $locale,
                ]
            )
            ->getQuery()
            ->getScalarResult();

        return count($results) === 1;
    }

    public function pageExistsWithModuleActionForLocale(string $module, string $action, Locale $locale): bool
    {
        $results = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('1')
            ->from(Page::class, 'p')
            ->innerJoin('p.blocks', 'b')
            ->innerJoin(ModuleExtra::class, 'e', Join::WITH, 'e.id = b.extraId')
            ->where('e.module = :module')
            ->andWhere('e.action = :action')
            ->andWhere('p.locale = :locale')
            ->setMaxResults(1)
            ->setParameters(
                [
                    'module' => $module,
                    'action' => $action,
                    'locale' => $locale,
                ]
            )
            ->getQuery()
            ->getScalarResult();

        return count($results) === 1;
    }

    private function buildGetQuery(int $pageId, ?int $revisionId, ?Locale $locale): QueryBuilder
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
            ->leftJoin('p.meta', 'm')
            ->leftJoin('p.blocks', 'b', Join::WITH, 'b.extraId IS NOT NULL')
            ->leftJoin(ModuleExtra::class, 'e', Join::WITH, 'e.id = b.extraId AND e.type = :type')
            ->where('p.id = :id')
            ->groupBy('p.revisionId');

        $parameters = [
            'type' => ModuleExtraType::block(),
            'id' => $pageId,
        ];

        if ($revisionId !== null) {
            $qb->andWhere('p.revisionId = :revisionId');
            $parameters['revisionId'] = $revisionId;
        }

        if ($locale !== null) {
            $qb->andWhere('p.locale = :locale');
            $parameters['locale'] = $locale;
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

    private function getParentIds(int $parentId = null): array
    {
        if (in_array($parentId, Page::TOP_LEVEL_IDS, true)) {
            return Page::TOP_LEVEL_IDS;
        }

        return [$parentId];
    }

    public function getUrl(
        string $url,
        Locale $locale,
        int $excludedId = null,
        int $parentId = null,
        bool $isAction = false
    ): string {
        $parentId = $parentId ?? Page::NO_PARENT_PAGE_ID;
        $parentIds = $this->getParentIds($parentId);

        $page = $this->findOneByParentsAndUrlAndStatusAndLocale(
            $parentIds,
            $url,
            Status::active(),
            $locale,
            $excludedId
        );

        if ($page instanceof Page) {
            return $this->getUrl(Model::addNumber($url), $locale, $excludedId, $parentId, $isAction);
        }

        // get full URL
        $fullUrl = BackendPagesModel::getFullUrl($parentId ?? Page::NO_PARENT_PAGE_ID) . '/' . $url;

        // get info about parent page
        $parentPageInfo = $this->get($parentId, null, $locale);

        // does the parent have extras?
        if (!$isAction && ($parentPageInfo['has_extra'] ?? false)) {
            Language::setLocale($locale, true);

            // if the new URL conflicts with an action we should rebuild the URL
            if (in_array($url, Language::getActions(), true)) {
                // recall this method, but with a new URL
                return $this->getUrl(Model::addNumber($url), $locale, $excludedId, $parentId, $isAction);
            }
        }

        // check if folder exists or is a reserved route
        if (is_dir(PATH_WWW . '/' . $fullUrl) || is_file(PATH_WWW . '/' . $fullUrl)
            || array_key_exists(trim($fullUrl, '/'), ForkController::getRoutes())) {
            // recall this method, but with a new URL
            return $this->getUrl(Model::addNumber($url), $locale, $excludedId, $parentId, $isAction);
        }

        return $url;
    }
}
