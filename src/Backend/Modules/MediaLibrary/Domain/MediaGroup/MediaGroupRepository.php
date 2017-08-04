<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaGroup;

use Doctrine\ORM\EntityRepository;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\Exception\MediaGroupNotFound;

final class MediaGroupRepository extends EntityRepository
{
    public function add(MediaGroup $mediaGroup)
    {
        // We don't flush here, see http://disq.us/p/okjc6b
        $this->getEntityManager()->persist($mediaGroup);
    }

    public function findOneById(string $id = null): MediaGroup
    {
        if ($id === null) {
            throw MediaGroupNotFound::forEmptyId();
        }

        /** @var MediaGroup $mediaGroup */
        $mediaGroup = parent::findOneById($id);

        if ($mediaGroup === null) {
            throw MediaGroupNotFound::forId($id);
        }

        return $mediaGroup;
    }
}
