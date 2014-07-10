<?php

namespace Backend\Modules\Multisite\Engine;

use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Language;
use Backend\Core\Installer\ModuleInstaller;
use Backend\Core\Modules\Pages\Model as PagesModel;

/**
 * In this file we store all generic functions that we will be using
 * in the multisite module
 *
 * @author Wouter Sioen <wouter@wijs.be>
 */
class Model
{
    const QRY_ALL_SITES =
        'SELECT id, domain, is_active, is_viewable, is_main_site
         FROM sites'
    ;

    /**
     * @param array $item
     * @return int The ID the item got in storage.
     */
    public static function add(array $item)
    {
        $languages = $item['languages'];
        unset($item['languages']);

        $item['id'] = BackendModel::get('database')->insert('sites', $item);
        self::saveLanguages($languages, $item['id']);

        return $item['id'];
    }

    /**
     * Makes sure all site/language combinations have a homepage
     *
     * @param array $languages
     * @param int $siteId
     */
    public static function createHomepages($languages, $siteId)
    {
        // @todo: uncomment this when the pages module has been made multisite aware
        /*foreach ($languages as $language) {
            // check if a homepages exists for this site/language combination
            if (!PagesModel::exists(1, $language['language'], $siteId)) {
                // create a moduleInstaller instance.
                // this saves us some duplicate code.
                $installer = new ModuleInstaller(
                    BackendModel::get('database'),
                    Language::getActiveLanguages(),
                    array_keys(Language::getInterfaceLanguages())
                );

                // insert a new page.
                $installer->insertPage(
                    array(
                        'id' => 1,
                        'parent_id' => 0,
                        'template_id' => $installer->getTemplateId('home'),
                        'title' => \SpoonFilter::ucfirst(
                            $installer->getLocale(
                                'Home', 'Core', $language['language'], 'lbl', 'Backend'
                            )
                        ),
                        'language' => $language['language'],
                        'site_id' => $siteId,
                        'allow_move' => 'N',
                        'allow_delete' => 'N',
                    )
                );
            }
        }*/
    }

    /**
     * @param int $id ID of the site to delete.
     * @return int The number of affected rows.
     */
    public static function delete($id)
    {
        $db = BackendModel::get('database');
        $db->delete('sites_languages', 'site_id = ?', array((int) $id));
        return $db->delete('sites', 'id = ?', array((int) $id));
    }

    /**
     * @param int ID of an item to check for existance.
     * @return bool Whether or not an item with that ID exists.
     */
    public static function exists($id)
    {
        return (bool) BackendModel::get('database')->getVar(
            'SELECT i.id
             FROM sites i
             WHERE i.id = ?',
            array((int) $id)
        );
    }

    /**
     * @param ID in the database of an item to fetch.
     * @return array The record representing that item.
     */
    public static function get($id)
    {
        return (array) BackendModel::get('database')->getRecord(
            'SELECT i.*
             FROM sites i
            WHERE i.id = ?',
            array((int) $id)
        );
    }

    /**
     * @param int $siteId ID of the site to get the languages for.
     * @return array List of site_languages records for the given site ID.
     */
    public static function getLanguages($siteId)
    {
        return (array) BackendModel::get('database')->getRecords(
            'SELECT *
             FROM sites_languages
             WHERE site_id = ?',
            array((int) $siteId)
        );
    }

    /**
     * Fetches the working languages for the current site dropdown ready
     *
     * @return array
     */
    public static function getWorkingLanguagesForDropdown()
    {
        $languages = BackendModel::get('current_site')->getWorkingLanguages();
        foreach ($languages as &$language) {
            $language = array(
                'value' => $language,
                'label' => \SpoonFilter::ucfirst(Language::lbl(strtoupper($language)))
            );
        }

        return $languages;
    }

    /**
     * @param array $languages List of languages for a site.
     * @param int $siteId ID of the site in the db where we are saving languages
     *        for.
     */
    private static function saveLanguages($languages, $siteId)
    {
        $db = BackendModel::get('database');
        $db->delete('sites_languages', 'site_id = ?', array((int) $siteId));
        foreach ($languages as $siteLanguage) {
            $siteLanguage['site_id'] = $siteId;
            $db->insert('sites_languages', $siteLanguage);
        }

        self::createHomepages($languages, $siteId);
    }

    /**
     * @param array $item
     * @param int $id
     * @return int Number of affected rows.
     */
    public static function update(array $item, $id)
    {
        $languages = $item['languages'];
        unset($item['languages']);
        self::saveLanguages($languages, $id);

        return (int) BackendModel::get('database')->update(
            'sites',
            $item,
            'id = ?',
            array((int) $id)
        );
    }
}
