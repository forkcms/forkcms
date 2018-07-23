<?php

namespace Backend\Modules\Tags\Engine;

use Backend\Core\Language\Locale;
use Backend\Modules\Tags\Domain\ModuleTag\ModuleTag;
use Backend\Modules\Tags\Domain\ModuleTag\ModuleTagRepository;
use Backend\Modules\Tags\Domain\Tag\Tag;
use Backend\Modules\Tags\Domain\Tag\TagRepository;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;
use Common\Uri;

/**
 * In this file we store all generic functions that we will be using in the TagsModule
 */
class Model
{
    const QUERY_DATAGRID_BROWSE =
        'SELECT i.id, i.tag, i.numberOfTimesLinked AS num_tags
         FROM TagsTag AS i
         WHERE i.locale = ?
         GROUP BY i.id';

    /**
     * Delete one or more tags.
     *
     * @param int|int[] $ids The ids to delete.
     */
    public static function delete($ids): void
    {
        $tagRepository = self::getTagRepository();
        $ids = array_filter((array) $ids, '\is_numeric');

        $tagRepository->remove(...$tagRepository->findByIds(...$ids));
    }

    public static function exists(int $id): bool
    {
        return self::getTagRepository()->find($id) instanceof Tag;
    }

    public static function existsTag(string $tag): bool
    {
        return self::getTagRepository()->findOneByTag($tag) instanceof Tag;
    }

    public static function get(int $id): array
    {
        $tag = self::getTagRepository()->find($id);

        return ['name' => $tag->getTag()];
    }

    public static function getAll(string $language = null): array
    {
        return array_map(
            function (Tag $tag): array {
                return ['name' => $tag->getTag()];
            },
            self::getTagRepository()->findByLocale(self::getLocale($language))
        );
    }

    public static function getTagNames(string $language = null): array
    {
        return array_map(
            function (Tag $tag): string {
                return $tag->getTag();
            },
            self::getTagRepository()->findByLocale(self::getLocale($language))
        );
    }

    /**
     * Get tags that start with the given string
     *
     * @param string $term The searchstring.
     * @param string $language The language to use, if not provided use the working language.
     *
     * @return array
     */
    public static function getStartsWith(string $term, string $language = null): array
    {
        return array_map(
            function (Tag $tag): array {
                return [
                    'name' => $tag->getTag(),
                    'value' => $tag->getTag(),
                ];
            },
            self::getTagRepository()->findByTagStartingWith($term, self::getLocale($language))
        );
    }

    /**
     * Get tags for an item
     *
     * @param string $module The module wherein will be searched.
     * @param int $otherId The id of the record.
     * @param string $type The type of the returnvalue, possible values are: array, string (tags will be joined by ,).
     * @param string $language The language to use, if not provided the working language will be used.
     *
     * @return mixed
     */
    public static function getTags(string $module, int $otherId, string $type = 'string', string $language = null)
    {
        $type = (string) \SpoonFilter::getValue($type, ['string', 'array'], 'string');

        $tags = array_map(
            function (Tag $tag): string {
                return $tag->getTag();
            },
            self::getTagRepository()->findTags(self::getLocale($language), $module, $otherId)
        );

        // return as an imploded string
        if ($type === 'string') {
            return implode(',', $tags);
        }

        // return as array
        return array_values($tags);
    }

    /**
     * Get a unique URL for a tag
     *
     * @param string $url The URL to use as a base.
     * @param int|null $id The ID to ignore.
     *
     * @return string
     */
    public static function getUrl(string $url, int $id = null): string
    {
        return self::getTagRepository()->getUrl($url, self::getLocale(), $id);
    }

    /**
     * Insert a new tag
     *
     * @param string $tag The data for the tag.
     * @param string $language The language wherein the tag will be inserted,
     *                         if not provided the workinglanguage will be used.
     *
     * @return int
     */
    public static function insert(string $tag, string $language = null): int
    {
        $locale = self::getLocale($language);
        $tagRepository = self::getTagRepository();

        $tagEntity = new Tag($locale, $tag, $tagRepository->getUrl($tag, $locale));

        $tagRepository->add($tagEntity);

        return $tagEntity->getId();
    }

    /**
     * Save the tags
     *
     * @param int $otherId The id of the item to tag.
     * @param mixed $tags The tags for the item.
     * @param string $module The module wherein the item is located.
     * @param string|null $language The language wherein the tags will be inserted,
     *                         if not provided the workinglanguage will be used.
     */
    public static function saveTags(int $otherId, $tags, string $module, string $language = null): void
    {
        $locale = self::getLocale($language);
        $tagRepository = self::getTagRepository();
        $moduleTagRepository = self::getModuleTagRepository();

        // redefine the tags as an array
        if (!\is_array($tags)) {
            $tags = (array) explode(',', (string) $tags);
        }

        foreach ($tags as $key => $tag) {
            // cleanup
            $tags[$key] = mb_strtolower(trim($tag));

            // unset if the tag is empty
            if ($tags[$key] === '') {
                unset($tags[$key]);
            }
        }

        // make sure the list of tags contains only unique and non-empty elements in a case insensitive way
        $tags = array_filter(array_intersect_key($tags, array_unique(array_map('strtolower', $tags))));

        $currentTags = $tagRepository->findTags($locale, $module, $otherId);

        $moduleTagRepository->remove(
            ...array_map(
                function (Tag $tag) use ($module, $otherId, $moduleTagRepository): ModuleTag {
                    return $moduleTagRepository->findOneBy(
                        [
                            'tag' => $tag,
                            'moduleName' => $module,
                            'moduleId' => $otherId,
                        ]
                    );
                },
                array_values(array_diff_key($currentTags, array_flip($tags)))
            )
        );

        $newTags = array_diff($tags, array_keys($currentTags));

        if (!empty($newTags)) {
            $moduleTagRepository->add(
                ...array_map(
                    function (string $tagName) use ($module, $otherId, $locale): ModuleTag {
                        return new ModuleTag($module, $otherId, self::getTagForTagName($tagName, $locale));
                    },
                    $newTags
                )
            );
        }

        // add to search index
        BackendSearchModel::saveIndex($module, $otherId, ['tags' => implode(' ', (array) $tags)], $language);

        // remove all tags that don't have anything linked
        $tagRepository->removeUnused();
    }

    /**
     * Update a tag
     * Remark: $tag['id'] should be available.
     *
     * @param array $tag The new data for the tag.
     */
    public static function update(array $tag): void
    {
        $tagRepository = self::getTagRepository();
        /** @var Tag $tagEntity */
        $tagEntity = $tagRepository->find($tag['id']);
        $locale = $tagEntity->getLocale();

        $tag['tag'] = $tag['tag'] ?? $tagEntity->getTag();
        $tag['url'] = $tag['url'] ?? $tagRepository->getUrl(Uri::getUrl($tag['tag']), $locale, $tag['id']);

        $tagEntity->update($tag['tag'], $tag['url']);

        $tagRepository->flush();
    }

    private static function getTagRepository(): TagRepository
    {
        return BackendModel::get(TagRepository::class);
    }

    private static function getModuleTagRepository(): ModuleTagRepository
    {
        return BackendModel::get(ModuleTagRepository::class);
    }

    private static function getLocale(string $language = null): Locale
    {
        return Locale::fromString($language ?? BL::getWorkingLanguage());
    }

    private static function getTagForTagName(string $tagName, Locale $locale): Tag
    {
        $tagRepository = self::getTagRepository();

        $tag = $tagRepository->findOneBy(['tag' => $tagName, 'locale' => $locale]);

        if ($tag instanceof Tag) {
            return $tag;
        }

        $tag = new Tag($locale, $tagName, $tagRepository->getUrl($tagName, $locale));

        $tagRepository->add($tag);

        return $tag;
    }
}
