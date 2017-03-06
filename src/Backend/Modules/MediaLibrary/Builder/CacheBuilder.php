<?php

namespace Backend\Modules\MediaLibrary\Builder;

use Psr\Cache\CacheItemPoolInterface;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroup;
use Backend\Modules\MediaLibrary\Domain\MediaItem\MediaItem;
use Backend\Modules\MediaLibrary\Domain\MediaGroup\MediaGroupRepository;

/**
 * In this file we store all generic functions that we will be using in the Media module
 */
class CacheBuilder
{
    /**
     * @var CacheItemPoolInterface
     */
    protected $cache;

    /**
     * @var \SpoonDatabase
     */
    protected $database;

    /**
     * @var MediaGroupRepository
     */
    protected $mediaGroupRepository;

    /**
     * @param \SpoonDatabase $database
     * @param CacheItemPoolInterface $cache
     * @param MediaGroupRepository $mediaGroupRepository
     */
    public function __construct(
        \SpoonDatabase $database,
        CacheItemPoolInterface $cache,
        MediaGroupRepository $mediaGroupRepository
    ) {
        $this->database = $database;
        $this->cache = $cache;
        $this->mediaGroupRepository = $mediaGroupRepository;
    }

    /**
     * Create cache
     */
    public function createCache()
    {
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
     * Get the folder counts for a group.
     *
     * @param MediaGroup $mediaGroup
     * @return array
     */
    public function getFolderCountsForGroup(MediaGroup $mediaGroup): array
    {
        // Init counts
        $counts = array();

        // Loop all connected items
        foreach ($mediaGroup->getConnectedItems() as $connectedItem) {
            /** @var MediaItem $mediaItem */
            $mediaItem = $connectedItem->getItem();

            /** @var int $folderId */
            $folderId = $mediaItem->getFolder()->getId();

            // Counts for folder doesn't exist
            if (!array_key_exists($folderId, $counts)) {
                // Init counts
                $counts[$folderId] = 1;

                continue;
            }

            // Bump counts
            $counts[$folderId] += 1;
        }

        return $counts;
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
        $item = $this->cache->getItem($this->getDropdownCachePath($includeCount, $includeKeys));
        if ($item->isHit()) {
            return $item->get();
        }

        // get tree
        $levels = $this->getFoldersTree(null, null, 1);

        // init var
        $names = array();
        $sequences = array();
        $keys = array();
        $counts = array();
        $return = array();

        // loop levels
        foreach ($levels as $level => $folders) {
            // loop all items on this level
            foreach ($folders as $folderID => $folder) {
                // init var
                $parentID = (int) $folder['parentMediaFolderId'];

                // get URL for parent
                $URL = (isset($keys[$parentID])) ? $keys[$parentID] : '';

                // add it
                $keys[$folderID] = trim($URL . '/' . $folder['name'], '/');

                // add to counts
                $counts[$folderID] = $folder['count'];

                // add to sequences
                $sequences[(string) trim($URL . '/' . $folder['name'], '/')] = $folderID;

                // get URL for parent
                $name = (isset($names[$parentID])) ? $names[$parentID] : '';

                // add it
                $names[$folderID] = trim($name . '/' . $folder['name'], '/');
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
                        $return[$id] = array(
                            'id' => $id,
                            'name' => $name,
                            'numMedia' => $counts[$id],
                        );
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
        $level = (int) $level;

        $whereIds = (!empty($ids)) ?
            'IN ("' . implode('", "', $ids) . '")' : 'IS NULL';

        // get data
        $data[$level] = (array) $this->database->getRecords(
            'SELECT i.id, i.parentMediaFolderId, i.name, COUNT(p.mediaFolderId) as count
                 FROM MediaFolder AS i
                 LEFT OUTER JOIN MediaItem AS p ON i.id = p.mediaFolderId
                 WHERE i.parentMediaFolderId ' . $whereIds . '
                 GROUP BY i.id
                 ORDER BY i.name ASC',
            null,
            'id'
        );

        // get the childIDs
        $childIds = array_keys($data[$level]);

        // build array
        if (!empty($data[$level])) {
            return $this->getFoldersTree(
                $childIds,
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
