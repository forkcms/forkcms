<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaGroup;

use Doctrine\ORM\EntityRepository;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\Exception\MediaGroupNotFound;

final class MediaGroupRepository extends EntityRepository
{
    /**
     * Add a MediaGroup
     *
     * @param MediaGroup $mediaGroup
     *
     * We don't flush here, see http://disq.us/p/okjc6b
     */
    public function add(MediaGroup $mediaGroup)
    {
        $this->getEntityManager()->persist($mediaGroup);
    }

    /**
     * @param string $id
     * @return MediaGroup
     * @throws \Exception
     */
    public function getOneById(string $id)
    {
        if ($id === null) {
            throw MediaGroupNotFound::forEmptyId();
        }

        /** @var MediaGroup $mediaGroup */
        $mediaGroup = $this->findOneById($id);

        if ($mediaGroup === null) {
            throw MediaGroupNotFound::forId($id);
        }

        return $mediaGroup;
    }
}
