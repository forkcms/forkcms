<?php

namespace Backend\Modules\MediaLibrary\Builder;

use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolder;
use Backend\Modules\MediaLibrary\Domain\MediaFolder\MediaFolderRepository;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroupRepository;
use Psr\Cache\CacheItemPoolInterface;
use stdClass;

/**
 * In this file we store all generic functions that we will be using in the Media module
 */
class CacheBuilder
{
    /**
     * @var CacheItemPoolInterface|stdClass
     */
    protected $cache;

    /**
     * @var MediaGroupRepository
     */
    protected $mediaFolderRepository;

    /**
     * @var MediaGroupRepository
     */
    protected $mediaGroupRepository;

    /**
     * @param CacheItemPoolInterface|stdClass $cache
     * @param MediaFolderRepository $mediaFolderRepository
     * @param MediaGroupRepository $mediaGroupRepository
     */
    public function __construct(
        $cache,
        MediaFolderRepository $mediaFolderRepository,
        MediaGroupRepository $mediaGroupRepository
    ) {
        $this->cache = $cache;
        $this->mediaFolderRepository = $mediaFolderRepository;
        $this->mediaGroupRepository = $mediaGroupRepository;
    }

    /**
     * Create cache
     */
    public function createCache()
    {
        if (!$this->cache instanceof CacheItemPoolInterface) {
            return false;
        }

        $items = [
            $this->getDropdownCachePath() => $this->getFoldersForDropdown(),
            $this->getDropdownCachePath(true) => $this->getFoldersForDropdown(true),
            $this->getDropdownCachePath(true, true) => $this->getFoldersForDropdown(true, true),
            $this->getDropdownCachePath(false, true) => $this->getFoldersForDropdown(false, true),
        ];

        foreach ($items as $key => $data) {
            // Save dropdowns
            $item = $this->cache->getItem($key);
            $item->set($data);
            $this->cache->save($item);
        }
    }

    /**
     * Delete cache
     */
    public function deleteCache()
    {
        if (!$this->cache instanceof CacheItemPoolInterface) {
            return false;
        }

        $keys = [
            $this->getDropdownCachePath(),
            $this->getDropdownCachePath(true),
            $this->getDropdownCachePath(true, true),
            $this->getDropdownCachePath(false, true),
        ];

        $this->cache->deleteItems($keys);
    }

    /**
     * Get dropdown cache path
     *
     * @param bool $includeCount When calling from AJAX, the count should be added
     * @param bool $includeKeys When calling from AJAX, the keys are needed to sort the array
     * @return string
     */
    public function getDropdownCachePath(
        bool $includeCount = false,
        bool $includeKeys = false
    ) : string {
        $extras = '';

        if ($includeCount) {
            $extras = '-include-count';
        }
        if ($includeKeys) {
            $extras .= '-include-keys';
        }

        return 'media_library-media_folders' . $extras;
    }

    /**
     * Get the folders for usage in a dropdown menu
     *
     * @param bool $includeCount When calling from AJAX, the count should be added
     * @param bool $includeKeys When calling from AJAX, the keys are needed to sort the array
     * @return array
     */
    public function getFoldersForDropdown(
        bool $includeCount = false,
        bool $includeKeys = false
    ) : array {
        if ($this->cache instanceof CacheItemPoolInterface) {
            $item = $this->cache->getItem($this->getDropdownCachePath($includeCount, $includeKeys));
            if ($item->isHit()) {
                return $item->get();
            }
        }

        // get tree
        $levels = $this->getFoldersTree(null, null, 1);

        // init var
        $names = [];
        $sequences = [];
        $keys = [];
        $counts = [];
        $return = [];

        // loop levels
        foreach ($levels as $level => $folders) {
            // loop all items on this level
            /**
             * @var int $folderID
             * @var MediaFolder $folder
             */
            foreach ($folders as $folderID => $folder) {
                // init var
                $parentID = (int) ($folder->hasParent()) ? $folder->getParent()->getId() : 0;

                // get URL for parent
                $URL = (isset($keys[$parentID])) ? $keys[$parentID] : '';

                // add it
                $keys[$folderID] = trim($URL . '/' . $folder->getName(), '/');

                // add to counts
                $counts[$folderID] = $folder->getItems()->count();

                // add to sequences
                $sequences[(string) trim($URL . '/' . $folder->getName(), '/')] = $folderID;

                // get URL for parent
                $name = (isset($names[$parentID])) ? $names[$parentID] : '';

                // add it
                $names[$folderID] = trim($name . '/' . $folder->getName(), '/');
            }
        }

        if (isset($sequences)) {
            // sort the sequences
            ksort($sequences);

            // loop to add the names in the correct order
            foreach ($sequences as $URL => $id) {
                if (isset($names[$id])) {
                    if ($includeCount) {
                        $name = $names[$id] . ' (' . $counts[$id] . ') ';
                    } else {
                        $name = $names[$id];
                    }

                    if ($includeKeys) {
                        $return[$id] = [
                            'id' => $id,
                            'name' => $name,
                            'numMedia' => $counts[$id],
                        ];
                    } else {
                        $return[$id] = $name;
                    }
                }
            }
        }

        if (!$includeKeys) {
            // sort the array values alphabetically
            asort($return);
        }

        if (!$this->cache instanceof CacheItemPoolInterface) {
            return $return;
        }

        // We must set the cache item
        $item->set($return);
        $this->cache->save($item);

        return $item->get();
    }

    /**
     * Get folders tree
     *
     * @param array $ids The parentIds.
     * @param array $data A holder for the generated data.
     * @param int $level The counter for the level.
     * @return array
     */
    public function getFoldersTree(
        array $ids = null,
        array $data = null,
        int $level = 1
    ) : array {
        // redefine
        $level = $level;

        // get data
        $queryBuilder = $this->mediaFolderRepository->createQueryBuilder('i');

        if ($ids !== null) {
            $queryBuilder
                ->leftJoin(MediaFolder::class, 'p', 'WITH', 'i.parent = p.id')
                ->where('p.id IN(:ids)')
                ->setParameter('ids', $ids);
        } else {
            $queryBuilder->where('i.parent IS NULL');
        }

        $results = $queryBuilder
            ->groupBy('i.id')
            ->orderBy('i.name', 'ASC')
            ->getQuery()
            ->getResult();

        /** @var MediaFolder $mediaFolder */
        foreach ($results as $mediaFolder) {
            $data[$level][$mediaFolder->getId()] = $mediaFolder;
        }

        // build array
        if (!empty($data[$level])) {
            return $this->getFoldersTree(
                array_keys($data[$level]),
                $data,
                ++$level
            );
        // cleanup
        } else {
            unset($data[$level]);
        }

        // return
        return $data;
    }
}
