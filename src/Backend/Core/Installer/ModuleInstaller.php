<?php

namespace Backend\Core\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Model;
use SpoonDatabase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Common\Uri as CommonUri;
use Backend\Modules\Locale\Engine\Model as BackendLocaleModel;
use Common\ModuleExtraType;

/**
 * The base-class for the installer
 */
class ModuleInstaller
{
    /**
     * Database connection instance
     *
     * @var SpoonDatabase
     */
    private $database;

    /**
     * The module name.
     *
     * @var string
     */
    private $module;

    /**
     * The default extras that have to be added to every page.
     *
     * @var array
     */
    private $defaultExtras = [];

    /**
     * The frontend language(s)
     *
     * @var array
     */
    private $languages = [];

    /**
     * The backend language(s)
     *
     * @var array
     */
    private $interfaceLanguages = [];

    /**
     * Cached modules
     *
     * @var array
     */
    private static $modules = [];

    /**
     * The variables passed by the installer
     *
     * @var array
     */
    private $variables = [];

    /**
     * Should example data be installed.
     *
     * @var bool
     */
    private $example;

    /**
     * @param SpoonDatabase $database The database-connection.
     * @param array $languages The selected frontend languages.
     * @param array $interfaceLanguages The selected backend languages.
     * @param bool $example Should example data be installed.
     * @param array $variables The passed variables.
     */
    public function __construct(
        SpoonDatabase $database,
        array $languages,
        array $interfaceLanguages,
        bool $example = false,
        array $variables = []
    ) {
        $this->database = $database;
        $this->languages = $languages;
        $this->interfaceLanguages = $interfaceLanguages;
        $this->example = $example;
        $this->variables = $variables;
    }

    /**
     * Adds a default extra to the stack of extras
     *
     * @param int $extraId The extra id to add to every page.
     * @param string $position The position to put the default extra.
     */
    protected function addDefaultExtra(int $extraId, string $position): void
    {
        $this->defaultExtras[] = ['id' => $extraId, 'position' => $position];
    }

    /**
     * Inserts a new module.
     * The getModule method becomes available after using addModule and returns $module parameter.
     *
     * @param string $module The name of the module.
     */
    protected function addModule(string $module): void
    {
        $this->module = (string) $module;

        // module does not yet exists
        if (!(bool) $this->getDatabase()->getVar('SELECT 1 FROM modules WHERE name = ? LIMIT 1', $this->module)) {
            // build item
            $item = [
                'name' => $this->module,
                'installed_on' => gmdate('Y-m-d H:i:s'),
            ];

            // insert module
            $this->getDatabase()->insert('modules', $item);

            return;
        }

        // activate and update description
        $this->getDatabase()->update('modules', ['installed_on' => gmdate('Y-m-d H:i:s')], 'name = ?', $this->module);
    }

    /**
     * Add a search index
     *
     * @param string $module The module wherein will be searched.
     * @param int $otherId The id of the record.
     * @param array $fields A key/value pair of fields to index.
     * @param string $language The frontend language for this entry.
     */
    protected function addSearchIndex(string $module, int $otherId, array $fields, string $language): void
    {
        // get database
        $database = $this->getDatabase();

        // validate cache
        if (empty(self::$modules)) {
            // get all modules
            self::$modules = (array) $database->getColumn('SELECT m.name FROM modules AS m');
        }

        // module exists?
        if (!in_array('Search', self::$modules)) {
            return;
        }

        // no fields?
        if (empty($fields)) {
            return;
        }

        // insert search index
        foreach ($fields as $field => $value) {
            // reformat value
            $value = strip_tags((string) $value);

            // insert in database
            $database->execute(
                'INSERT INTO search_index (module, other_id, language, field, value, active)
                 VALUES (?, ?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE value = ?, active = ?',
                [(string) $module, (int) $otherId, (string) $language, (string) $field, $value, true, $value, true]
            );
        }

        // invalidate the cache for search
        $finder = new Finder();
        $filesystem = new Filesystem();
        foreach ($finder->files()->in(FRONTEND_CACHE_PATH . '/Search/') as $file) {
            /** @var $file \SplFileInfo */
            $filesystem->remove($file->getRealPath());
        }
    }

    /**
     * Method that will be overridden by the specific installers
     */
    protected function execute(): void
    {
        // just a placeholder
    }

    /**
     * Get the database-handle
     *
     * @return SpoonDatabase
     */
    protected function getDatabase(): SpoonDatabase
    {
        return $this->database;
    }

    /**
     * Get the module name
     *
     * @return string
     */
    protected function getModule(): string
    {
        return $this->module;
    }

    /**
     * Get the default extras.
     *
     * @return array
     */
    public function getDefaultExtras(): array
    {
        return $this->defaultExtras;
    }

    /**
     * Get the default user
     *
     * @return int
     */
    protected function getDefaultUserID(): int
    {
        try {
            // fetch default user id
            return (int) $this->getDatabase()->getVar(
                'SELECT id
                 FROM users
                 WHERE is_god = ? AND active = ? AND deleted = ?
                 ORDER BY id ASC',
                [true, true, false]
            );
        } catch (\Exception $e) {
            return 1;
        }
    }

    /**
     * Get the selected cms interface languages
     *
     * @return array
     */
    protected function getInterfaceLanguages(): array
    {
        return $this->interfaceLanguages;
    }

    /**
     * Get the selected languages
     *
     * @return array
     */
    protected function getLanguages(): array
    {
        return $this->languages;
    }

    /**
     * Get a locale item.
     *
     * @param string $name
     * @param string $module
     * @param string $language The language abbreviation.
     * @param string $type The type of locale.
     * @param string $application
     *
     * @return string
     */
    protected function getLocale(
        string $name,
        string $module = 'Core',
        string $language = 'en',
        string $type = 'lbl',
        string $application = 'Backend'
    ): string {
        $translation = (string) $this->getDatabase()->getVar(
            'SELECT value
             FROM locale
             WHERE name = ? AND module = ? AND language = ? AND type = ? AND application = ?',
            [$name, $module, $language, $type, $application]
        );

        return ($translation !== '') ? $translation : $name;
    }

    /**
     * Get a setting
     *
     * @param string $module The name of the module.
     * @param string $name The name of the setting.
     *
     * @return mixed
     */
    protected function getSetting(string $module, string $name)
    {
        return unserialize(
            $this->getDatabase()->getVar(
                'SELECT value
                 FROM modules_settings
                 WHERE module = ? AND name = ?',
                [$module, $name]
            )
        );
    }

    /**
     * Get the id of the requested template of the active theme.
     *
     * @param string $template
     * @param string $theme
     *
     * @return int
     */
    protected function getTemplateId(string $template, string $theme = null): int
    {
        // no theme set = default theme
        if ($theme === null) {
            $theme = $this->getSetting('Core', 'theme');
        }

        // if the theme is still null we should fallback to the core
        if ($theme === null) {
            $theme = 'Fork';
        }

        // return best matching template id
        return (int) $this->getDatabase()->getVar(
            'SELECT id FROM themes_templates
             WHERE theme = ?
             ORDER BY path LIKE ? DESC, id ASC
             LIMIT 1',
            [$theme, '%' . $template . '%']
        );
    }

    /**
     * Get a variable
     *
     * @param string $name
     *
     * @return mixed
     */
    protected function getVariable(string $name)
    {
        return $this->variables[$name] ?? null;
    }

    /**
     * Imports the locale XML file
     *
     * @param string $filename The full path for the XML-file.
     * @param bool $overwriteConflicts Should we overwrite when there is a conflict?
     */
    protected function importLocale(string $filename, bool $overwriteConflicts = false): void
    {
        // load the file content and execute it
        $content = trim(file_get_contents($filename));

        // file actually has content
        if (empty($content)) {
            return;
        }

        // load xml
        $xml = @simplexml_load_string($content);

        // import if it's valid xml
        if ($xml === false) {
            return;
        }

        // import locale
        BackendLocaleModel::importXML(
            $xml,
            $overwriteConflicts,
            $this->getLanguages(),
            $this->getInterfaceLanguages(),
            $this->getDefaultUserID(),
            gmdate('Y-m-d H:i:s')
        );
    }

    /**
     * Imports the sql file
     *
     * @param string $filename The full path for the SQL-file.
     */
    protected function importSQL(string $filename): void
    {
        // load the file content and execute it
        $queries = trim(file_get_contents($filename));

        // file actually has content
        if (empty($queries)) {
            return;
        }

        $this->getDatabase()->execute($queries);
    }

    protected function insertDashboardWidget(string $module, string $widget): void
    {
        // get database
        $database = $this->getDatabase();

        // fetch current settings
        $groupSettings = (array) $database->getRecords(
            'SELECT * FROM groups_settings WHERE name = ?',
            ['dashboard_sequence']
        );
        $userSettings = (array) $database->getRecords(
            'SELECT * FROM users_settings WHERE name = ?',
            ['dashboard_sequence']
        );

        // loop group settings
        foreach ($groupSettings as $settings) {
            // unserialize data
            $settings['value'] = unserialize($settings['value']);

            // add new widget
            $settings['value'][$module][] = $widget;

            // re-serialize value
            $settings['value'] = serialize($settings['value']);

            // update in database
            $database->update(
                'groups_settings',
                $settings,
                'group_id = ? AND name = ?',
                [$settings['group_id'], $settings['name']]
            );
        }

        // loop user settings
        foreach ($userSettings as $settings) {
            // unserialize data
            $settings['value'] = unserialize($settings['value']);

            // add new widget
            $settings['value'][$module][] = $widget;

            // re-serialize value
            $settings['value'] = serialize($settings['value']);

            // update in database
            $database->update(
                'users_settings',
                $settings,
                'user_id = ? AND name = ?',
                [$settings['user_id'], $settings['name']]
            );
        }
    }

    private function getNextSequenceForModule(string $module): int
    {
        // set next sequence number for this module
        $sequence = (int) $this->getDatabase()->getVar(
            'SELECT MAX(sequence) + 1 FROM modules_extras WHERE module = ?',
            [$module]
        );

        // this is the first extra for this module: generate new 1000-series
        if ($sequence > 0) {
            return $sequence;
        }

        return (int) $this->getDatabase()->getVar(
            'SELECT CEILING(MAX(sequence) / 1000) * 1000 FROM modules_extras'
        );
    }

    /**
     * Insert an extra
     *
     * @param string $module The module for the extra.
     * @param ModuleExtraType $type The type, possible values are: homepage, widget, block.
     * @param string $label The label for the extra.
     * @param string|null $action The action.
     * @param array|null $data data, will be passed in the extra.
     * @param bool $hidden Is this extra hidden?
     * @param int|null $sequence The sequence for the extra.
     *
     * @return int
     */
    protected function insertExtra(
        string $module,
        ModuleExtraType $type,
        string $label,
        string $action = null,
        array $data = null,
        bool $hidden = false,
        int $sequence = null
    ): int {
        $extraId = $this->findModuleExtraId($module, $type, $label, $data);
        if ($extraId !== 0) {
            return $extraId;
        }

        return Model::insertExtra(
            $type,
            $module,
            $action,
            $label,
            $data,
            $hidden,
            $sequence ?? $this->getNextSequenceForModule($module)
        );
    }

    /**
     * @param string $module
     * @param ModuleExtraType $type
     * @param string $label
     * @param array|null $data
     *
     * @return int
     */
    private function findModuleExtraId(string $module, ModuleExtraType $type, string $label, array $data = null): int
    {
        // build query
        $query = 'SELECT id FROM modules_extras WHERE module = ? AND type = ? AND label = ?';
        $parameters = [$module, $type, $label];

        if ($data === null) {
            $query .= ' AND data IS NULL';

            return (int) $this->getDatabase()->getVar($query, $parameters);
        }

        $query .= ' AND data = ?';
        $parameters[] = serialize($data);

        // get id (if it already exists)
        return (int) $this->getDatabase()->getVar($query, $parameters);
    }

    /**
     * Insert a meta item
     *
     * @param string $keywords The keyword of the item.
     * @param string $description A description of the item.
     * @param string $title The page title for the item.
     * @param string $url The unique URL.
     * @param bool $keywordsOverwrite Should the keywords be overwritten?
     * @param bool $descriptionOverwrite Should the descriptions be overwritten?
     * @param bool $titleOverwrite Should the page title be overwritten?
     * @param bool $urlOverwrite Should the URL be overwritten?
     * @param string $custom Any custom meta-data.
     * @param string $seoFollow Any custom meta-data.
     * @param string $seoIndex Any custom meta-data.
     * @param array $data Any custom meta-data.
     *
     * @return int
     */
    protected function insertMeta(
        string $keywords,
        string $description,
        string $title,
        string $url,
        bool $keywordsOverwrite = false,
        bool $descriptionOverwrite = false,
        bool $titleOverwrite = false,
        bool $urlOverwrite = false,
        string $custom = null,
        string $seoFollow = null,
        string $seoIndex = null,
        array $data = null
    ): int {
        return (int) $this->getDatabase()->insert(
            'meta',
            [
                'keywords' => $keywords,
                'keywords_overwrite' => $keywordsOverwrite,
                'description' => $description,
                'description_overwrite' => $descriptionOverwrite,
                'title' => $title,
                'title_overwrite' => $titleOverwrite,
                'url' => CommonUri::getUrl($url),
                'url_overwrite' => $urlOverwrite,
                'custom' => $custom,
                'seo_follow' => $seoFollow,
                'seo_index' => $seoIndex,
                'data' => $data !== null ? serialize($data) : null,
            ]
        );
    }

    /**
     * Looks for the next page id, if it is the first page it will default to 1
     *
     * @param string $language
     *
     * @return int
     */
    private function getNextPageIdForLanguage(string $language): int
    {
        $maximumPageId = (int) $this->getDatabase()->getVar(
            'SELECT MAX(id) FROM pages WHERE language = ?',
            [$language]
        );

        return ++$maximumPageId;
    }

    private function archiveAllRevisionsOfAPageForLanguage(int $pageId, string $language): void
    {
        $this->getDatabase()->update(
            'pages',
            ['status' => 'archive'],
            'id = ? AND language = ?',
            [$pageId, $language]
        );
    }

    private function getNextPageSequence(string $language, int $parentId, string $type): int
    {
        $maximumPageSequence = (int) $this->getDatabase()->getVar(
            'SELECT MAX(sequence) FROM pages WHERE language = ? AND parent_id = ? AND type = ?',
            [$language, $parentId, $type]
        );

        return ++$maximumPageSequence;
    }

    /**
     * Add the missing data to the meta record
     *
     * @param array $meta
     * @param string $defaultValue
     *
     * @return array
     */
    private function completeMetaRecord(array $meta, string $defaultValue): array
    {
        $meta['keywords'] = $meta['keywords'] ?? $defaultValue;
        $meta['keywords_overwrite'] = $meta['keywords_overwrite'] ?? false;
        $meta['description'] = $meta['description'] ?? $defaultValue;
        $meta['description_overwrite'] = $meta['description_overwrite'] ?? false;
        $meta['title'] = $meta['title'] ?? $defaultValue;
        $meta['title_overwrite'] = $meta['title_overwrite'] ?? false;
        $meta['url'] = $meta['url'] ?? $defaultValue;
        $meta['url_overwrite'] = $meta['url_overwrite'] ?? false;
        $meta['custom'] = $meta['custom'] ?? null;
        $meta['seo_follow'] = $meta['seo_follow'] ?? null;
        $meta['seo_index'] = $meta['seo_index'] ?? null;
        $meta['data'] = $meta['data'] ?? null;

        return $meta;
    }

    private function getNewMetaId(array $meta, string $defaultValue): int
    {
        $meta = $this->completeMetaRecord($meta, $defaultValue);

        return $this->insertMeta(
            $meta['keywords'],
            $meta['description'],
            $meta['title'],
            $meta['url'],
            $meta['keywords_overwrite'],
            $meta['description_overwrite'],
            $meta['title_overwrite'],
            $meta['url_overwrite'],
            $meta['custom'],
            $meta['seo_follow'],
            $meta['seo_index'],
            $meta['data']
        );
    }

    private function completePageRevisionRecord(array $revision, array $meta = []): array
    {
        $revision['id'] = $revision['id'] ?? $this->getNextPageIdForLanguage($revision['language']);
        $revision['user_id'] = $revision['user_id'] ?? $this->getDefaultUserID();
        $revision['template_id'] = $revision['template_id'] ?? $this->getTemplateId('Default');
        $revision['type'] = $revision['type'] ?? 'page';
        $revision['parent_id'] = $revision['parent_id'] ?? ($revision['type'] === 'page' ? 1 : 0);
        $revision['navigation_title'] = $revision['navigation_title'] ?? $revision['title'];
        $revision['navigation_title_overwrite'] = $revision['navigation_title_overwrite'] ?? false;
        $revision['hidden'] = $revision['hidden'] ?? false;
        $revision['status'] = $revision['status'] ?? 'active';
        $revision['publish_on'] = $revision['publish_on'] ?? gmdate('Y-m-d H:i:s');
        $revision['created_on'] = $revision['created_on'] ?? gmdate('Y-m-d H:i:s');
        $revision['edited_on'] = $revision['edited_on'] ?? gmdate('Y-m-d H:i:s');
        $revision['data'] = $revision['data'] ?? null;
        $revision['allow_move'] = $revision['allow_move'] ?? true;
        $revision['allow_children'] = $revision['allow_children'] ?? true;
        $revision['allow_edit'] = $revision['allow_edit'] ?? true;
        $revision['allow_delete'] = $revision['allow_delete'] ?? true;
        $revision['sequence'] = $revision['sequence'] ?? $this->getNextPageSequence(
            $revision['language'],
            $revision['parent_id'],
            $revision['type']
        );
        $revision['meta_id'] = $revision['meta_id'] ?? $this->getNewMetaId($meta, $revision['title']);

        if (!isset($revision['data']['image']) && $this->installExample()) {
            $revision['data']['image'] = $this->getAndCopyRandomImage();
        }
        if ($revision['data'] !== null) {
            $revision['data'] = serialize($revision['data']);
        }

        return $revision;
    }

    /**
     * Insert a page
     *
     * @param array $revision An array with the revision data.
     * @param array $meta The meta-data.
     * @param array[] $blocks The blocks.
     *
     * @throws \SpoonDatabaseException
     * @throws \SpoonException
     *
     * @return int
     */
    protected function insertPage(array $revision, array $meta = null, array ...$blocks): int
    {
        // build revision
        if (!isset($revision['language'])) {
            throw new \SpoonException('language is required for installing pages');
        }
        if (!isset($revision['title'])) {
            throw new \SpoonException('title is required for installing pages');
        }
        // deactivate previous page revisions
        if (isset($revision['id'])) {
            $this->archiveAllRevisionsOfAPageForLanguage($revision['id'], $revision['language']);
        }

        $revision = $this->completePageRevisionRecord($revision, (array) $meta);

        // insert page
        $revision['revision_id'] = $this->getDatabase()->insert('pages', $revision);

        if (empty($blocks)) {
            return $revision['id'];
        }

        $this->getDatabase()->insert(
            'pages_blocks',
            $this->completePageBlockRecords($blocks, $revision['revision_id'])
        );

        // return page id
        return $revision['id'];
    }

    private function completePageBlockRecords(array $blocks, int $defaultRevisionId): array
    {
        // array of positions and linked blocks (will be used to automatically set block sequence)
        $positions = [];

        return array_map(
            function (array $block) use (&$positions, $defaultRevisionId) {
                $block['position'] = $block['position'] ?? 'main';
                $positions[$block['position']][] = $block;
                $block['revision_id'] = $block['revision_id'] ?? $defaultRevisionId;
                $block['created_on'] = $block['created_on'] ?? gmdate('Y-m-d H:i:s');
                $block['edited_on'] = $block['edited_on'] ?? gmdate('Y-m-d H:i:s');
                $block['extra_id'] = $block['extra_id'] ?? null;
                $block['visible'] = $block['visible'] ?? true;
                $block['sequence'] = $block['sequence'] ?? count($positions[$block['position']]) - 1;
                $block['html'] = $block['html'] ?? '';

                // get the html from the template file if it is defined
                if (!empty($block['html'])) {
                    $block['html'] = file_get_contents($block['html']);
                }

                // sort array by its keys, so the array is always the same for SpoonDatabase::insert,
                // when you don't provide an array with arrays sorted in the same order, the fields get
                // mixed into different columns
                ksort($block);

                return $block;
            },
            $blocks
        );
    }

    /**
     * Should example data be installed
     *
     * @return bool
     */
    protected function installExample(): bool
    {
        return $this->example;
    }

    /**
     * Make a module searchable
     *
     * @param string $module The module to make searchable.
     * @param bool $searchable Enable/disable search for this module by default?
     * @param int $weight Set default search weight for this module.
     */
    protected function makeSearchable(string $module, bool $searchable = true, int $weight = 1): void
    {
        $this->getDatabase()->execute(
            'INSERT INTO search_modules (module, searchable, weight) VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE searchable = ?, weight = ?',
            [$module, $searchable, $weight, $searchable, $weight]
        );
    }

    /**
     * Set the rights for an action
     *
     * @param int $groupId The group wherefore the rights will be set.
     * @param string $module The module wherein the action appears.
     * @param string $action The action wherefore the rights have to set.
     * @param int $level The level, default is 7 (max).
     */
    protected function setActionRights(int $groupId, string $module, string $action, int $level = 7): void
    {
        // check if the action already exists
        $actionRightAlreadyExist = (bool) $this->getDatabase()->getVar(
            'SELECT 1
             FROM groups_rights_actions
             WHERE group_id = ? AND module = ? AND action = ?
             LIMIT 1',
            [$groupId, $module, $action]
        );

        if ($actionRightAlreadyExist) {
            return;
        }

        $this->getDatabase()->insert(
            'groups_rights_actions',
            [
                'group_id' => $groupId,
                'module' => $module,
                'action' => $action,
                'level' => $level,
            ]
        );
    }

    /**
     * Sets the rights for a module
     *
     * @param int $groupId The group wherefore the rights will be set.
     * @param string $module The module too set the rights for.
     */
    protected function setModuleRights(int $groupId, string $module): void
    {
        $moduleRightAlreadyExist = (bool) $this->getDatabase()->getVar(
            'SELECT 1
             FROM groups_rights_modules
             WHERE group_id = ? AND module = ?
             LIMIT 1',
            [$groupId, $module]
        );

        if ($moduleRightAlreadyExist) {
            return;
        }

        $this->getDatabase()->insert(
            'groups_rights_modules',
            [
                'group_id' => $groupId,
                'module' => $module,
            ]
        );
    }

    private function getNextBackendNavigationSequence(int $parentId): int
    {
        // get maximum sequence for this parent
        $currentMaxBackendNavigationSequence = (int) $this->getDatabase()->getVar(
            'SELECT MAX(sequence)
             FROM backend_navigation
             WHERE parent_id = ?',
            [$parentId]
        );

        return ++$currentMaxBackendNavigationSequence;
    }

    /**
     * Set a new navigation item.
     *
     * @param int|null $parentId Id of the navigation item under we should add this.
     * @param string $label Label for the item.
     * @param string|null $url Url for the item. If omitted the first child is used.
     * @param array $selectedFor Set selected when these actions are active.
     * @param int $sequence Sequence to use for this item.
     *
     * @return int
     */
    protected function setNavigation(
        $parentId,
        string $label,
        string $url = null,
        array $selectedFor = null,
        int $sequence = null
    ): int {
        // if it is null we should cast it to int so we get a 0
        $parentId = (int) $parentId;

        $sequence = $sequence ?? $this->getNextBackendNavigationSequence($parentId);

        // get the id for this url
        $id = (int) $this->getDatabase()->getVar(
            'SELECT id
             FROM backend_navigation
             WHERE parent_id = ? AND label = ? AND url ' . ($url === null ? 'IS' : '=') . ' ?',
            [$parentId, $label, $url]
        );

        // already exists so return current id
        if ($id !== 0) {
            return $id;
        }

        // doesn't exist yet, add it
        return (int) $this->getDatabase()->insert(
            'backend_navigation',
            [
                'parent_id' => $parentId,
                'label' => $label,
                'url' => $url,
                'selected_for' => $selectedFor === null ? null : serialize($selectedFor),
                'sequence' => $sequence,
            ]
        );
    }

    /**
     * Stores a module specific setting in the database.
     *
     * @param string $module The module wherefore the setting will be set.
     * @param string $name The name of the setting.
     * @param mixed $value The optional value.
     * @param bool $overwrite Overwrite no matter what.
     */
    protected function setSetting(string $module, string $name, $value = null, bool $overwrite = false): void
    {
        $value = serialize($value);

        if ($overwrite) {
            $this->getDatabase()->execute(
                'INSERT INTO modules_settings (module, name, value)
                 VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE value = ?',
                [$module, $name, $value, $value]
            );

            return;
        }

        // check if this setting already exists
        $moduleSettingAlreadyExists = (bool) $this->getDatabase()->getVar(
            'SELECT 1
             FROM modules_settings
             WHERE module = ? AND name = ?
             LIMIT 1',
            [$module, $name]
        );

        if ($moduleSettingAlreadyExists) {
            return;
        }

        $this->getDatabase()->insert(
            'modules_settings',
            [
                'module' => $module,
                'name' => $name,
                'value' => $value,
            ]
        );
    }

    private function getAndCopyRandomImage(): string
    {
        $finder = new Finder();
        $finder
            ->files()
            ->name('*.jpg')
            ->in(__DIR__ . '/Data/images');

        $finder = iterator_to_array($finder);
        $randomImage = $finder[array_rand($finder)];
        $randomName = time() . '.jpg';

        $fileSystem = new Filesystem();
        $fileSystem->copy(
            $randomImage->getRealPath(),
            __DIR__ . '/../../../Frontend/Files/Pages/images/source/' . $randomName
        );

        return $randomName;
    }
}
