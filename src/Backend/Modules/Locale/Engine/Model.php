<?php

namespace Backend\Modules\Locale\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\Uri as CommonUri;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;

/**
 * In this file we store all generic functions that we will be using in the locale module
 */
class Model
{
    /**
     * @var array The possible locale types
     */
    public const TYPES = [
        'act',
        'err',
        'lbl',
        'msg',
    ];

    public static function buildCache(string $language, string $application): void
    {
        $cacheBuilder = new CacheBuilder(BackendModel::get('database'));
        $cacheBuilder->buildCache($language, $application);
    }

    public static function buildUrlQueryByFilter(array $filter): string
    {
        $query = http_build_query($filter, null, '&', PHP_QUERY_RFC3986);
        if ($query != '') {
            $query = '&' . $query;
        }

        return $query;
    }

    public static function createXMLForExport(array $items): string
    {
        $charset = BackendModel::getContainer()->getParameter('kernel.charset');
        $xml = new \DOMDocument('1.0', $charset);

        // set some properties
        $xml->preserveWhiteSpace = false;
        $xml->formatOutput = true;

        // locale root element
        $root = $xml->createElement('locale');
        $xml->appendChild($root);

        // loop applications
        foreach ($items as $application => $modules) {
            // create application element
            $applicationElement = $xml->createElement($application);
            $root->appendChild($applicationElement);

            // loop modules
            foreach ($modules as $module => $types) {
                // create application element
                $moduleElement = $xml->createElement($module);
                $applicationElement->appendChild($moduleElement);

                // loop types
                foreach ($types as $type => $items) {
                    // loop items
                    foreach ($items as $name => $translations) {
                        // create application element
                        $itemElement = $xml->createElement('item');
                        $moduleElement->appendChild($itemElement);

                        // attributes
                        $itemElement->setAttribute('type', self::getTypeName($type));
                        $itemElement->setAttribute('name', $name);

                        // loop translations
                        foreach ($translations as $translation) {
                            // create translation
                            $translationElement = $xml->createElement('translation');
                            $itemElement->appendChild($translationElement);

                            // attributes
                            $translationElement->setAttribute('language', $translation['language']);

                            // set content
                            $translationElement->appendChild(new \DOMCdataSection($translation['value']));
                        }
                    }
                }
            }
        }

        return $xml->saveXML();
    }

    /**
     * Delete (multiple) items from locale
     *
     * @param int[] $ids The id(s) to delete.
     */
    public static function delete(array $ids): void
    {
        // loop and cast to integers
        foreach ($ids as &$id) {
            $id = (int) $id;
        }

        // create an array with an equal amount of questionmarks as ids provided
        $idPlaceHolders = array_fill(0, count($ids), '?');

        // delete records
        BackendModel::getContainer()->get('database')->delete(
            'locale',
            'id IN (' . implode(', ', $idPlaceHolders) . ')',
            $ids
        );

        // rebuild cache
        self::buildCache(BL::getWorkingLanguage(), 'Backend');
        self::buildCache(BL::getWorkingLanguage(), 'Frontend');
    }

    public static function exists(int $id): bool
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM locale
             WHERE id = ?
             LIMIT 1',
            [$id]
        );
    }

    public static function existsByName(
        string $name,
        string $type,
        string $module,
        string $language,
        string $application,
        int $excludedId = null
    ): bool {
        // get database
        $database = BackendModel::getContainer()->get('database');

        // return
        if ($excludedId !== null) {
            return (bool) $database->getVar(
                'SELECT 1
                 FROM locale
                 WHERE name = ? AND type = ? AND module = ? AND language = ? AND application = ? AND id != ?
                 LIMIT 1',
                [$name, $type, $module, $language, $application, $excludedId]
            );
        }

        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM locale
             WHERE name = ? AND type = ? AND module = ? AND language = ? AND application = ?
             LIMIT 1',
            [$name, $type, $module, $language, $application]
        );
    }

    public static function get(int $id): array
    {
        // fetch record from database
        $record = (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT * FROM locale WHERE id = ?',
            [$id]
        );

        // actions are urlencoded
        if ($record['type'] === 'act') {
            $record['value'] = urldecode($record['value']);
        }

        return $record;
    }

    public static function getByName(
        string $name,
        string $type,
        string $module,
        string $language,
        string $application
    ): int {
        return BackendModel::getContainer()->get('database')->getVar(
            'SELECT l.id
             FROM locale AS l
             WHERE name = ? AND type = ? AND module = ? AND language = ? AND application = ?',
            [$name, $type, $module, $language, $application]
        );
    }

    public static function getLanguagesForMultiCheckbox(bool $includeInterfaceLanguages = false): array
    {
        // get working languages
        $aLanguages = BL::getWorkingLanguages();

        // add the interface languages if needed
        if ($includeInterfaceLanguages) {
            $aLanguages = array_merge($aLanguages, BL::getInterfaceLanguages());
        }

        // create a new array to redefine the languages for the multicheckbox
        $languages = [];

        // loop the languages
        foreach ($aLanguages as $key => $lang) {
            // add to array
            $languages[$key]['value'] = $key;
            $languages[$key]['label'] = $lang;
        }

        return $languages;
    }

    public static function getTranslations(
        $application,
        string $module,
        array $types,
        array $languages,
        string $name,
        string $value
    ): array {
        // create an array for the languages, surrounded by quotes (example: 'en')
        $aLanguages = [];
        foreach ($languages as $key => $val) {
            $aLanguages[$key] = '\'' . $val . '\'';
        }

        // surround the types with quotes
        foreach ($types as $key => $val) {
            $types[$key] = '\'' . $val . '\'';
        }

        // get database
        $database = BackendModel::getContainer()->get('database');

        // build the query
        $query =
            'SELECT l.id, l.application, l.module, l.type, l.name, l.value, l.language, UNIX_TIMESTAMP(l.edited_on) as edited_on
             FROM locale AS l
             WHERE
                 l.language IN (' . implode(',', $aLanguages) . ') AND
                 l.name LIKE ? AND
                 l.value LIKE ? AND
                 l.type IN (' . implode(',', $types) . ')';

        // add the parameters
        $parameters = ['%' . $name . '%', '%' . $value . '%'];

        // add module to the query if needed
        if ($module) {
            $query .= ' AND l.module = ?';
            $parameters[] = $module;
        }

        // add module to the query if needed
        if ($application) {
            $query .= ' AND l.application = ?';
            $parameters[] = $application;
        }

        // get the translations
        $translations = (array) $database->getRecords($query, $parameters);

        // create an array for the sorted translations
        $sortedTranslations = [];

        // loop translations
        foreach ($translations as $translation) {
            // add to the sorted array
            $sortedTranslations[$translation['type']][$translation['name']][$translation['module']][$translation['language']] = [
                'id' => $translation['id'],
                'value' => $translation['value'],
                'edited_on' => $translation['edited_on'],
                'application' => $translation['application'],
            ];
        }

        // create an array to use in the datagrid
        $dataGridTranslations = [];

        // an id that is used for in the datagrid, this is not the id of the translation!
        $id = 0;

        // save the number of languages so this has not to be executed x number of times
        $numberOfLanguages = count($languages);

        // loop the sorted translations
        foreach ($sortedTranslations as $type => $references) {
            // create array for each type
            $dataGridTranslations[$type] = [];

            foreach ($references as $reference => $translation) {
                // loop modules
                foreach ($translation as $module => $t) {
                    // create translation (and increase id)
                    // we init the application here so it appears in front of the datagrid
                    $trans = [
                        'application' => '',
                        'module' => $module,
                        'name' => $reference,
                        'id' => $id++,
                    ];

                    // reset this var for every language
                    $edited_on = '';

                    foreach ($languages as $lang) {
                        // if the translation exists the for this language, fill it up
                        // else leave a space for the empty field
                        if (isset($t[$lang])) {
                            $trans[$lang] = $t[$lang]['value'];
                            $trans['application'] = $t[$lang]['application'];

                            // only alter edited_on if the date of a previously added date of another
                            // language is smaller
                            if ($edited_on < $t[$lang]['edited_on']) {
                                $edited_on = $t[$lang]['edited_on'];
                            }

                            if ($numberOfLanguages == 1) {
                                $trans['translation_id'] = $t[$lang]['id'];
                            } else {
                                $trans['translation_id_' . $lang] = $t[$lang]['id'];
                            }
                        } else {
                            $trans[$lang] = '';

                            if ($numberOfLanguages == 1) {
                                $trans['translation_id'] = '';
                            } else {
                                $trans['translation_id_' . $lang] = '';
                            }
                        }
                    }
                    // at the end of the array, add the generated edited_on date
                    $trans['edited_on'] = $edited_on;

                    // add the translation to the array
                    $dataGridTranslations[$type][] = $trans;
                }
            }
        }

        return $dataGridTranslations;
    }

    public static function getTypeName(string $type): string
    {
        // get full type name
        switch ($type) {
            case 'act':
                $type = 'action';
                break;
            case 'err':
                $type = 'error';
                break;
            case 'lbl':
                $type = 'label';
                break;
            case 'msg':
                $type = 'message';
                break;
        }

        return $type;
    }

    public static function getTypesForDropDown(): array
    {
        $labels = static::TYPES;

        // loop and build labels
        foreach ($labels as &$row) {
            $row = \SpoonFilter::ucfirst(BL::msg(mb_strtoupper($row), 'Core'));
        }

        // build array
        return array_combine(static::TYPES, $labels);
    }

    public static function getTypesForMultiCheckbox(): array
    {
        $labels = static::TYPES;

        // loop and build labels
        foreach ($labels as &$row) {
            $row = \SpoonFilter::ucfirst(BL::msg(mb_strtoupper($row), 'Core'));
        }

        // build array
        $aTypes = array_combine(static::TYPES, $labels);

        // create a new array to redefine the types for the multicheckbox
        $types = [];

        // loop the languages
        foreach ($aTypes as $key => $type) {
            // add to array
            $types[$key]['value'] = $key;
            $types[$key]['label'] = $type;
        }

        // return the redefined array
        return $types;
    }

    public static function importXML(
        \SimpleXMLElement $xml,
        bool $overwriteConflicts = false,
        array $frontendLanguages = null,
        array $backendLanguages = null,
        int $userId = null,
        string $date = null
    ): array {
        $statistics = [
            'total' => 0,
            'imported' => 0,
        ];

        // set defaults if necessary
        // we can't simply use these right away, because this function is also calls by the installer,
        // which does not have Backend-functions
        if ($frontendLanguages === null) {
            $frontendLanguages = array_keys(BL::getWorkingLanguages());
        }
        if ($backendLanguages === null) {
            $backendLanguages = array_keys(BL::getInterfaceLanguages());
        }
        if ($userId === null) {
            $userId = BackendAuthentication::getUser()->getUserId();
        }
        if ($date === null) {
            $date = BackendModel::getUTCDate();
        }

        // get database instance
        $database = BackendModel::getContainer()->get('database');

        // possible values
        $possibleApplications = ['Frontend', 'Backend'];
        $possibleModules = (array) $database->getColumn('SELECT m.name FROM modules AS m');

        // types
        $possibleTypes = [];
        foreach (static::TYPES as $type) {
            $possibleTypes[$type] = self::getTypeName($type);
        }

        // install English translations anyhow, they're fallback
        $possibleLanguages = [
            'Frontend' => array_unique(array_merge(['en'], $frontendLanguages)),
            'Backend' => array_unique(array_merge(['en'], $backendLanguages)),
        ];

        // current locale items (used to check for conflicts)
        $currentLocale = (array) $database->getColumn(
            'SELECT CONCAT(application, module, type, language, name)
             FROM locale'
        );

        // applications
        foreach ($xml as $application => $modules) {
            // application does not exist
            if (!in_array($application, $possibleApplications, true)) {
                continue;
            }

            // modules
            foreach ($modules as $module => $items) {
                // module does not exist
                if (!in_array($module, $possibleModules, true)) {
                    continue;
                }

                // items
                foreach ($items as $item) {
                    // attributes
                    $attributes = $item->attributes();
                    $type = \SpoonFilter::getValue($attributes['type'], $possibleTypes, '');
                    $name = ucfirst(\SpoonFilter::getValue($attributes['name'], null, ''));

                    // missing attributes
                    if ($type == '' || $name == '') {
                        continue;
                    }

                    // real type (shortened)
                    $type = array_search($type, $possibleTypes);

                    // translations
                    foreach ($item->translation as $translation) {
                        // statistics
                        ++$statistics['total'];

                        // attributes
                        $attributes = $translation->attributes();
                        $language = \SpoonFilter::getValue(
                            $attributes['language'],
                            $possibleLanguages[$application],
                            ''
                        );

                        // language does not exist
                        if ($language == '') {
                            continue;
                        }

                        // the actual translation
                        $translation = (string) $translation;

                        // locale item
                        $locale = [];
                        $locale['user_id'] = $userId;
                        $locale['language'] = $language;
                        $locale['application'] = $application;
                        $locale['module'] = $module;
                        $locale['type'] = $type;
                        $locale['name'] = $name;
                        $locale['value'] = $translation;
                        $locale['edited_on'] = $date;

                        // check if translation does not yet exist, or if the translation can be overridden
                        if (!in_array($application . $module . $type . $language . $name, $currentLocale)
                            || $overwriteConflicts
                        ) {
                            $database->execute(
                                'INSERT INTO locale (user_id, language, application, module, type, name, value, edited_on)
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                                 ON DUPLICATE KEY UPDATE user_id = ?, value = ?, edited_on = ?',
                                [
                                    $locale['user_id'],
                                    $locale['language'],
                                    $locale['application'],
                                    $locale['module'],
                                    $locale['type'],
                                    $locale['name'],
                                    $locale['value'],
                                    $locale['edited_on'],
                                    $locale['user_id'],
                                    $locale['value'],
                                    $locale['edited_on'],
                                ]
                            );

                            // statistics
                            ++$statistics['imported'];
                        }
                    }
                }
            }
        }

        // rebuild cache
        foreach ($possibleApplications as $application) {
            foreach ($possibleLanguages[$application] as $language) {
                self::buildCache($language, $application);
            }
        }

        return $statistics;
    }

    public static function insert(array $item): int
    {
        // actions should be urlized
        if ($item['type'] == 'act' && urldecode($item['value']) != $item['value']) {
            $item['value'] = CommonUri::getUrl(
                $item['value']
            );
        }

        // insert item
        $item['id'] = (int) BackendModel::getContainer()->get('database')->insert('locale', $item);

        // rebuild the cache
        self::buildCache($item['language'], $item['application']);

        // return the new id
        return $item['id'];
    }

    public static function update(array $item): int
    {
        // actions should be urlized
        if ($item['type'] == 'act' && urldecode($item['value']) != $item['value']) {
            $item['value'] = CommonUri::getUrl(
                $item['value']
            );
        }

        // update category
        $updated = BackendModel::getContainer()->get('database')->update('locale', $item, 'id = ?', [$item['id']]);

        // rebuild the cache
        self::buildCache($item['language'], $item['application']);

        return $updated;
    }
}
