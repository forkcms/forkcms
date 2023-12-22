<?php

namespace ForkCMS\Modules\Frontend\Domain\Meta;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use RuntimeException;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * @method Meta|null find($id, $lockMode = null, $lockVersion = null)
 * @method Meta|null findOneBy(array $criteria, array $orderBy = null)
 * @method Meta[] findAll()
 * @method Meta[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends ServiceEntityRepository<Meta>
 */
final class MetaRepository extends ServiceEntityRepository
{
    /** @param ServiceLocator<MetaCallbackService> $metaCallbacks */
    public function __construct(ManagerRegistry $registry, private readonly ServiceLocator $metaCallbacks)
    {
        parent::__construct($registry, Meta::class);
    }

    /**
     * Generate an url, using the predefined callback.
     *
     * @param string $url the base-url to start from
     * @param string $class The Fully Qualified Class Name or service name
     * @param string $method The method that needs to be called
     * @param array<string, mixed> $parameters The parameters for the callback
     *
     * @throws Exception When the function does not exist
     */
    public function generateSlug(string $url, string $class, string $method, array $parameters = []): string
    {
        // check if the class is a service
        if ($this->metaCallbacks->has($class)) {
            $class = $this->metaCallbacks->get($class);
        }

        // validate (check if the function exists)
        if (!is_callable([$class, $method])) {
            throw new RuntimeException('The callback-method doesn\'t exist.');
        }

        $actualParameters = [];
        // build parameters for use in the callback
        $actualParameters[] = $url;

        // add parameters set by user
        if (!empty($parameters)) {
            foreach ($parameters as $parameter) {
                $actualParameters[] = $parameter;
            }
        }

        // get the real url
        return call_user_func_array([$class, $method], $actualParameters);
    }

    public function save(Meta $meta): void
    {
        $this->getEntityManager()->persist($meta);
        $this->getEntityManager()->flush();
    }

    public function remove(Meta $meta): void
    {
        $this->getEntityManager()->remove($meta);
        $this->getEntityManager()->flush();
    }
}
