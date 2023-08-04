<?php

namespace Frontend\Modules\Tags\Engine;

use Backend\Modules\Tags\Domain\ModuleTag\ModuleTag;
use Backend\Modules\Tags\Domain\ModuleTag\ModuleTagRepository;
use Backend\Modules\Tags\Domain\Tag\Tag;
use Backend\Modules\Tags\Domain\Tag\TagRepository;
use Common\Locale;
use Frontend\Core\Engine\Exception as FrontendException;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Core\Language\Language;
use Frontend\Core\Language\Locale as FrontendLocale;

/**
 * In this file we store all generic functions that we will be using in the tags module
 */
class Model
{
    /**
     * Calls a method that has to be implemented though the tags interface
     *
     * @param string $module The module wherein to search.
     * @param string $class The class that should contain the method.
     * @param string $method The method to call.
     * @param mixed $parameter The parameters to pass.
     *
     * @throws FrontendException When FrontendTagsInterface is not correctly implemented to the module model
     *
     * @return array|int
     */
    public static function callFromInterface(string $module, string $class, string $method, $parameter = null)
    {
        // check to see if the interface is implemented
        if (\in_array(TagsInterface::class, class_implements($class), true)) {
            // return result
            return \call_user_func([$class, $method], $parameter);
        }

        throw new FrontendException(
            'To use the tags module you need
            to implement the FrontendTagsInterface
            in the model of your module
            (' . $module . ').'
        );
    }

    public static function get(string $url, Locale $locale = null): array
    {
        $tag = self::getTagRepository()->findOneBy(
            ['url' => $url, 'locale' => $locale ?? FrontendLocale::frontendLanguage()]
        );

        if ($tag instanceof Tag) {
            return $tag->toArray();
        }

        return [];
    }

    /**
     * Fetch the list of all tags, ordered by their occurrence
     *
     * @return array
     */
    public static function getAll(): array
    {
        return self::tagsToArrays(self::getTagRepository()->findAllLinkedTags(FrontendLocale::frontendLanguage()));
    }

    public static function getMostUsed(int $limit): array
    {
        return self::tagsToArrays(self::getTagRepository()->findMostUsed(FrontendLocale::frontendLanguage(), $limit));
    }

    /**
     * @param string $module The module wherein the otherId occurs.
     * @param int $otherId The id of the item.
     * @param Locale|null $locale
     *
     * @return array
     */
    public static function getForItem(string $module, int $otherId, Locale $locale = null): array
    {
        return self::tagsToArrays(
            self::getTagRepository()->findTags($locale ?? FrontendLocale::frontendLanguage(), $module, $otherId)
        );
    }

    /**
     * Get tags for multiple items.
     *
     * @param string $module The module wherefore you want to retrieve the tags.
     * @param array $otherIds The ids for the items.
     * @param Locale|null $locale
     *
     * @return array
     */
    public static function getForMultipleItems(string $module, array $otherIds, Locale $locale = null): array
    {
        $tags = self::getTagRepository()->findTagsForMultipleItems(
            $locale ?? FrontendLocale::frontendLanguage(),
            $module,
            ...$otherIds
        );
        $groupedTags = [];
        $tagLink = FrontendNavigation::getUrlForBlock('Tags', 'Detail');

        foreach ($tags as $tagRecord) {
            $tag = $tagRecord['tag']->toArray();
            $tag['other_id'] = $tagRecord['moduleId'];
            $tag['full_url'] = $tagLink . '/' . $tag['url'];

            $groupedTags[$tagRecord['moduleId']][] = $tag;
        }

        return $groupedTags;
    }

    public static function getIdByUrl(string $url): int
    {
        return self::getTagRepository()->findIdByUrl($url);
    }

    public static function getModulesForTag(int $tagId): array
    {
        return self::getModuleTagRepository()->findModulesByTagId($tagId);
    }

    public static function getName(int $tagId): string
    {
        return self::getTagRepository()->find($tagId)->getTag();
    }

    /**
     * Get all related items
     *
     * @param int $id The id of the item in the source-module.
     * @param string $module The source module.
     * @param string $otherModule The module wherein the related items should appear.
     * @param int $limit The maximum of related items to grab.
     *
     * @return array
     */
    public static function getRelatedItemsByTags(int $id, string $module, string $otherModule, int $limit = 5): array
    {
        return self::getModuleTagRepository()->findRelatedModuleIdsByTags($id, $module, $otherModule, $limit);
    }

    public static function getAllForTag(string $tag, Locale $locale = null): array
    {
        return array_map(
            static function (ModuleTag $moduleTag): array {
                return $moduleTag->toArray();
            },
            self::getModuleTagRepository()->findByTagAndLocale($tag, $locale ?? FrontendLocale::frontendLanguage())
        );
    }

    public static function getItemsForTag(int $id): array
    {
        return array_map(
            function (string $module) use ($id) {
                return self::getItemsForTagAndModule($id, $module);
            },
            self::getModulesForTag($id)
        );
    }

    public static function getItemsForTagAndModule(int $id, string $module): array
    {
        // get the ids of the items linked to the tag
        $moduleTags = self::getModuleTagRepository()->findByModuleAndTag($module, $id);

        $class = 'Frontend\\Modules\\' . $module . '\\Engine\\Model';

        // get the items that are linked to the tags
        $items = (array) self::callFromInterface($module, $class, 'getForTags', \array_keys($moduleTags));

        if (empty($items)) {
            return $items;
        }

        return [
            'name' => $module,
            'label' => Language::lbl(\SpoonFilter::ucfirst($module)),
            'items' => $items,
        ];
    }

    private static function getTagRepository(): TagRepository
    {
        return FrontendModel::get(TagRepository::class);
    }

    private static function getModuleTagRepository(): ModuleTagRepository
    {
        return FrontendModel::get(ModuleTagRepository::class);
    }

    private static function tagsToArrays(array $tags): array
    {
        $tagLink = FrontendNavigation::getUrlForBlock('Tags', 'Detail');

        return array_values(
            array_map(
                function (Tag $tagEntity) use ($tagLink): array {
                    $tag = $tagEntity->toArray();
                    $tag['full_url'] = $tagLink . '/' . $tag['url'];

                    return $tag;
                },
                $tags
            )
        );
    }
}
