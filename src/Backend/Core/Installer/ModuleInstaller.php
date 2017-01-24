<?php

namespace Backend\Core\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Common\Uri as CommonUri;
use Backend\Core\Engine\Model as BackendModel;
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
     * @var \SpoonDatabase
     */
    private $db;

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
    private $defaultExtras;

    /**
     * The frontend language(s)
     *
     * @var array
     */
    private $languages = array();

    /**
     * The backend language(s)
     *
     * @var array
     */
    private $interfaceLanguages = array();

    /**
     * Cached modules
     *
     * @var array
     */
    private static $modules = array();

    /**
     * The variables passed by the installer
     *
     * @var array
     */
    private $variables = array();

    /**
     * @param \SpoonDatabase $db                 The database-connection.
     * @param array          $languages          The selected frontend languages.
     * @param array          $interfaceLanguages The selected backend languages.
     * @param bool           $example            Should example data be installed.
     * @param array          $variables          The passed variables.
     */
    public function __construct(
        \SpoonDatabase $db,
        array $languages,
        array $interfaceLanguages,
        $example = false,
        array $variables = array()
    ) {
        $this->db = $db;
        $this->languages = $languages;
        $this->interfaceLanguages = $interfaceLanguages;
        $this->example = (bool) $example;
        $this->variables = $variables;
    }

    /**
     * Adds a default extra to the stack of extras
     *
     * @param int    $extraId  The extra id to add to every page.
     * @param string $position The position to put the default extra.
     */
    protected function addDefaultExtra($extraId, $position)
    {
        $this->defaultExtras[] = array('id' => $extraId, 'position' => $position);
    }

    /**
     * Inserts a new module.
     * The getModule method becomes available after using addModule and returns $module parameter.
     *
     * @param string $module The name of the module.
     */
    protected function addModule($module)
    {
        $this->module = (string) $module;

        // module does not yet exists
        if (!(bool) $this->getDB()->getVar('SELECT 1 FROM modules WHERE name = ? LIMIT 1', $this->module)) {
            // build item
            $item = array(
                'name' => $this->module,
                'installed_on' => gmdate('Y-m-d H:i:s'),
            );

            // insert module
            $this->getDB()->insert('modules', $item);
        } else {
            // activate and update description
            $this->getDB()->update('modules', array('installed_on' => gmdate('Y-m-d H:i:s')), 'name = ?', $this->module);
        }
    }

    /**
     * Add a search index
     *
     * @param string $module   The module wherein will be searched.
     * @param int    $otherId  The id of the record.
     * @param array  $fields   A key/value pair of fields to index.
     * @param string $language The frontend language for this entry.
     */
    protected function addSearchIndex($module, $otherId, array $fields, $language)
    {
        // get db
        $db = $this->getDB();

        // validate cache
        if (empty(self::$modules)) {
            // get all modules
            self::$modules = (array) $db->getColumn('SELECT m.name FROM modules AS m');
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

            // insert in db
            $db->execute(
                'INSERT INTO search_index (module, other_id, language, field, value, active)
                 VALUES (?, ?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE value = ?, active = ?',
                array((string) $module, (int) $otherId, (string) $language, (string) $field, $value, 'Y', $value, 'Y')
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
    protected function execute()
    {
        // just a placeholder
    }

    /**
     * Get the database-handle
     *
     * @return \SpoonDatabase
     */
    protected function getDB()
    {
        return $this->db;
    }

    /**
     * Get the module name
     *
     * @return string
     */
    protected function getModule()
    {
        return $this->module;
    }

    /**
     * Get the default extras.
     *
     * @return array
     */
    public function getDefaultExtras()
    {
        return $this->defaultExtras;
    }

    /**
     * Get the default user
     *
     * @return int
     */
    protected function getDefaultUserID()
    {
        try {
            // fetch default user id
            return (int) $this->getDB()->getVar(
                'SELECT id
                 FROM users
                 WHERE is_god = ? AND active = ? AND deleted = ?
                 ORDER BY id ASC',
                array('Y', 'Y', 'N')
            );
        } catch (\Exception $e) {
            return 1;
        }
    }

    /**
     * Get the selected cms interface languages
     */
    protected function getInterfaceLanguages()
    {
        return $this->interfaceLanguages;
    }

    /**
     * Get the selected languages
     */
    protected function getLanguages()
    {
        return $this->languages;
    }

    /**
     * Get a locale item.
     *
     * @param string $name
     * @param string $module
     * @param string $language The language abbreviation.
     * @param string $type     The type of locale.
     * @param string $application
     *
     * @return string
     */
    protected function getLocale($name, $module = 'Core', $language = 'en', $type = 'lbl', $application = 'Backend')
    {
        $translation = (string) $this->getDB()->getVar(
            'SELECT value
             FROM locale
             WHERE name = ? AND module = ? AND language = ? AND type = ? AND application = ?',
            array((string) $name, (string) $module, (string) $language, (string) $type, (string) $application)
        );

        return ($translation != '') ? $translation : $name;
    }

    /**
     * Get a setting
     *
     * @param string $module The name of the module.
     * @param string $name   The name of the setting.
     *
     * @return mixed
     */
    protected function getSetting($module, $name)
    {
        return unserialize(
            $this->getDB()->getVar(
                'SELECT value
                 FROM modules_settings
                 WHERE module = ? AND name = ?',
                array((string) $module, (string) $name)
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
    protected function getTemplateId($template, $theme = null)
    {
        // no theme set = default theme
        if ($theme === null) {
            $theme = $this->getSetting('Core', 'theme');
        }

        // return best matching template id
        return (int) $this->getDB()->getVar(
            'SELECT id FROM themes_templates
             WHERE theme = ?
             ORDER BY path LIKE ? DESC, id ASC
             LIMIT 1',
            array((string) $theme, '%' . (string) $template . '%')
        );
    }

    /**
     * Get a variable
     *
     * @param string $name
     *
     * @return mixed
     */
    protected function getVariable($name)
    {
        return (!isset($this->variables[$name])) ? null : $this->variables[$name];
    }

    /**
     * Imports the locale XML file
     *
     * @param string $filename           The full path for the XML-file.
     * @param bool   $overwriteConflicts Should we overwrite when there is a conflict?
     */
    protected function importLocale($filename, $overwriteConflicts = false)
    {
        $filename = (string) $filename;
        $overwriteConflicts = (bool) $overwriteConflicts;

        // load the file content and execute it
        $content = trim(file_get_contents($filename));

        // file actually has content
        if (!empty($content)) {
            // load xml
            $xml = @simplexml_load_string($content);

            // import if it's valid xml
            if ($xml !== false) {
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
        }
    }

    /**
     * Imports the sql file
     *
     * @param string $filename The full path for the SQL-file.
     */
    protected function importSQL($filename)
    {
        // load the file content and execute it
        $content = trim(file_get_contents($filename));

        // file actually has content
        if (!empty($content)) {
            /**
             * Some versions of PHP can't handle multiple statements at once, so split them
             * We know this isn't the best solution, but we couldn't find a beter way.
             */
            $queries = preg_split("/;(\r)?\n/", $content);

            // loop queries and execute them
            foreach ($queries as $query) {
                $this->getDB()->execute($query);
            }
        }
    }

    /**
     * Insert a dashboard widget
     *
     * @param string $module
     * @param string $widget
     */
    protected function insertDashboardWidget($module, $widget)
    {
        // get db
        $db = $this->getDB();

        // fetch current settings
        $groupSettings = (array) $db->getRecords(
            'SELECT * FROM groups_settings WHERE name = ?',
            array('dashboard_sequence')
        );
        $userSettings = (array) $db->getRecords(
            'SELECT * FROM users_settings WHERE name = ?',
            array('dashboard_sequence')
        );

        // loop group settings
        foreach ($groupSettings as $settings) {
            // unserialize data
            $settings['value'] = unserialize($settings['value']);

            // add new widget
            $settings['value'][$module][] = $widget;

            // re-serialize value
            $settings['value'] = serialize($settings['value']);

            // update in db
            $db->update(
                'groups_settings',
                $settings,
                'group_id = ? AND name = ?',
                array($settings['group_id'], $settings['name'])
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

            // update in db
            $db->update(
                'users_settings',
                $settings,
                'user_id = ? AND name = ?',
                array($settings['user_id'], $settings['name'])
            );
        }
    }

    /**
     * Insert an extra
     *
     * @param string $module   The module for the extra.
     * @param ModuleExtraType|string $type     The type, possible values are: homepage, widget, block.
     * @param string $label    The label for the extra.
     * @param string $action   The action.
     * @param string $data     Optional data, will be passed in the extra.
     * @param bool   $hidden   Is this extra hidden?
     * @param int    $sequence The sequence for the extra.
     *
     * @return int
     */
    protected function insertExtra(
        $module,
        $type,
        $label,
        $action = null,
        $data = null,
        $hidden = false,
        $sequence = null
    ) {
        // no sequence set
        if (is_null($sequence)) {
            // set next sequence number for this module
            $sequence = $this->getDB()->getVar(
                'SELECT MAX(sequence) + 1 FROM modules_extras WHERE module = ?',
                array((string) $module)
            );

            // this is the first extra for this module: generate new 1000-series
            if (is_null($sequence)) {
                $sequence = $sequence = $this->getDB()->getVar(
                    'SELECT CEILING(MAX(sequence) / 1000) * 1000 FROM modules_extras'
                );
            }
        }

        $module = (string) $module;
        $type = (string) $type;
        $label = (string) $label;
        $action = !is_null($action) ? (string) $action : null;
        $data = !is_null($data) ? (string) $data : null;
        $hidden = $hidden && $hidden !== 'N' ? 'Y' : 'N';
        $sequence = (int) $sequence;

        // build item
        $item = array(
            'module' => $module,
            'type' => $type,
            'label' => $label,
            'action' => $action,
            'data' => $data,
            'hidden' => $hidden,
            'sequence' => $sequence,
        );

        // build query
        $query = 'SELECT id FROM modules_extras WHERE module = ? AND type = ? AND label = ?';
        $parameters = array($item['module'], $item['type'], $item['label']);

        if ($data !== null) {
            $query .= ' AND data = ?';
            $parameters[] = $data;
        } else {
            $query .= ' AND data IS NULL';
        }

        // get id (if it already exists)
        $extraId = (int) $this->getDB()->getVar($query, $parameters);

        // doesn't already exist
        if ($extraId === 0) {
            // insert extra and return id
            return (int) $this->getDB()->insert('modules_extras', $item);
        }

        // exists so return id
        return $extraId;
    }

    /**
     * Insert a meta item
     *
     * @param string $keywords             The keyword of the item.
     * @param string $description          A description of the item.
     * @param string $title                The page title for the item.
     * @param string $url                  The unique URL.
     * @param bool   $keywordsOverwrite    Should the keywords be overwritten?
     * @param bool   $descriptionOverwrite Should the descriptions be overwritten?
     * @param bool   $titleOverwrite       Should the page title be overwritten?
     * @param bool   $urlOverwrite         Should the URL be overwritten?
     * @param string $custom               Any custom meta-data.
     * @param array  $data                 Any custom meta-data.
     *
     * @return int
     */
    protected function insertMeta(
        $keywords,
        $description,
        $title,
        $url,
        $keywordsOverwrite = false,
        $descriptionOverwrite = false,
        $titleOverwrite = false,
        $urlOverwrite = false,
        $custom = null,
        $data = null
    ) {
        $item = array(
            'keywords' => (string) $keywords,
            'keywords_overwrite' => ($keywordsOverwrite && $keywordsOverwrite !== 'N' ? 'Y' : 'N'),
            'description' => (string) $description,
            'description_overwrite' => ($descriptionOverwrite && $descriptionOverwrite !== 'N' ? 'Y' : 'N'),
            'title' => (string) $title,
            'title_overwrite' => ($titleOverwrite && $titleOverwrite !== 'N' ? 'Y' : 'N'),
            'url' => CommonUri::getUrl((string) $url),
            'url_overwrite' => ($urlOverwrite && $urlOverwrite !== 'N' ? 'Y' : 'N'),
            'custom' => (!is_null($custom) ? (string) $custom : null),
            'data' => (!is_null($data)) ? serialize($data) : null,
        );

        return (int) $this->getDB()->insert('meta', $item);
    }

    /**
     * Insert a page
     *
     * @param array $revision An array with the revision data.
     * @param array $meta     The meta-data.
     * @param array $block    The blocks.
     *
     * @throws \SpoonDatabaseException
     * @throws \SpoonException
     *
     * @return int
     */
    protected function insertPage(array $revision, array $meta = null, array $block = null)
    {
        $revision = (array) $revision;
        $meta = (array) $meta;

        // deactived previous revisions
        if (isset($revision['id']) && isset($revision['language'])) {
            $this->getDB()->update(
                'pages',
                array('status' => 'archive'),
                'id = ? AND language = ?',
                array($revision['id'], $revision['language'])
            );
        }

        // build revision
        if (!isset($revision['language'])) {
            throw new \SpoonException('language is required for installing pages');
        }
        if (!isset($revision['title'])) {
            throw new \SpoonException('title is required for installing pages');
        }
        if (!isset($revision['id'])) {
            $revision['id'] = (int) $this->getDB()->getVar(
                'SELECT MAX(id) + 1 FROM pages WHERE language = ?',
                array($revision['language'])
            );
        }
        if (!$revision['id']) {
            $revision['id'] = 1;
        }
        if (!isset($revision['user_id'])) {
            $revision['user_id'] = $this->getDefaultUserID();
        }
        if (!isset($revision['template_id'])) {
            $revision['template_id'] = $this->getTemplateId('default');
        }
        if (!isset($revision['type'])) {
            $revision['type'] = 'page';
        }
        if (!isset($revision['parent_id'])) {
            $revision['parent_id'] = ($revision['type'] == 'page' ? 1 : 0);
        }
        if (!isset($revision['navigation_title'])) {
            $revision['navigation_title'] = $revision['title'];
        }
        if (!isset($revision['navigation_title_overwrite'])) {
            $revision['navigation_title_overwrite'] = 'N';
        }
        if (!isset($revision['hidden'])) {
            $revision['hidden'] = 'N';
        }
        if (!isset($revision['status'])) {
            $revision['status'] = 'active';
        }
        if (!isset($revision['publish_on'])) {
            $revision['publish_on'] = gmdate('Y-m-d H:i:s');
        }
        if (!isset($revision['created_on'])) {
            $revision['created_on'] = gmdate('Y-m-d H:i:s');
        }
        if (!isset($revision['edited_on'])) {
            $revision['edited_on'] = gmdate('Y-m-d H:i:s');
        }
        if (!isset($revision['data'])) {
            $revision['data'] = null;
        }
        if (!isset($revision['allow_move'])) {
            $revision['allow_move'] = 'Y';
        }
        if (!isset($revision['allow_children'])) {
            $revision['allow_children'] = 'Y';
        }
        if (!isset($revision['allow_edit'])) {
            $revision['allow_edit'] = 'Y';
        }
        if (!isset($revision['allow_delete'])) {
            $revision['allow_delete'] = 'Y';
        }
        if (!isset($revision['sequence'])) {
            $revision['sequence'] = (int) $this->getDB()->getVar(
                'SELECT MAX(sequence) + 1 FROM pages WHERE language = ? AND parent_id = ? AND type = ?',
                array($revision['language'], $revision['parent_id'], $revision['type'])
            );
        }

        // meta needs to be inserted
        if (!isset($revision['meta_id'])) {
            // build meta
            if (!isset($meta['keywords'])) {
                $meta['keywords'] = $revision['title'];
            }
            if (!isset($meta['keywords_overwrite'])) {
                $meta['keywords_overwrite'] = false;
            }
            if (!isset($meta['description'])) {
                $meta['description'] = $revision['title'];
            }
            if (!isset($meta['description_overwrite'])) {
                $meta['description_overwrite'] = false;
            }
            if (!isset($meta['title'])) {
                $meta['title'] = $revision['title'];
            }
            if (!isset($meta['title_overwrite'])) {
                $meta['title_overwrite'] = false;
            }
            if (!isset($meta['url'])) {
                $meta['url'] = $revision['title'];
            }
            if (!isset($meta['url_overwrite'])) {
                $meta['url_overwrite'] = false;
            }
            if (!isset($meta['custom'])) {
                $meta['custom'] = null;
            }
            if (!isset($meta['data'])) {
                $meta['data'] = null;
            }

            // insert meta
            $revision['meta_id'] = $this->insertMeta(
                $meta['keywords'],
                $meta['description'],
                $meta['title'],
                $meta['url'],
                $meta['keywords_overwrite'],
                $meta['description_overwrite'],
                $meta['title_overwrite'],
                $meta['url_overwrite'],
                $meta['custom'],
                $meta['data']
            );
        }

        // insert page
        $revision['revision_id'] = $this->getDB()->insert('pages', $revision);

        // array of positions and linked blocks (will be used to automatically set block sequence)
        $positions = array();

        // loop blocks: get arguments (this function has a variable length
        // argument list, to allow multiple blocks to be added)
        for ($i = 0; $i < func_num_args() - 2; ++$i) {
            // get block
            $block = @func_get_arg($i + 2);
            if ($block === false) {
                $block = array();
            } else {
                $block = (array) $block;
            }

            // save block position
            if (!isset($block['position'])) {
                $block['position'] = 'main';
            }
            $positions[$block['position']][] = $block;

            // build block
            if (!isset($block['revision_id'])) {
                $block['revision_id'] = $revision['revision_id'];
            }
            if (!isset($block['html'])) {
                $block['html'] = '';
            } elseif (file_exists($block['html'])) {
                $block['html'] = file_get_contents($block['html']);
            }
            if (!isset($block['created_on'])) {
                $block['created_on'] = gmdate('Y-m-d H:i:s');
            }
            if (!isset($block['edited_on'])) {
                $block['edited_on'] = gmdate('Y-m-d H:i:s');
            }
            if (!isset($block['extra_id'])) {
                $block['extra_id'] = null;
            }
            if (!isset($block['visible'])) {
                $block['visible'] = 'Y';
            }
            if (!isset($block['sequence'])) {
                $block['sequence'] = count($positions[$block['position']]) - 1;
            }

            $this->getDB()->insert('pages_blocks', $block);
        }

        // return page id
        return $revision['id'];
    }

    /**
     * Should example data be installed
     *
     * @return bool
     */
    protected function installExample()
    {
        return $this->example;
    }

    /**
     * Make a module searchable
     *
     * @param string $module     The module to make searchable.
     * @param bool   $searchable Enable/disable search for this module by default?
     * @param int    $weight     Set default search weight for this module.
     */
    protected function makeSearchable($module, $searchable = true, $weight = 1)
    {
        $module = (string) $module;
        $searchable = $searchable && $searchable !== 'N' ? 'Y' : 'N';
        $weight = (int) $weight;

        // make module searchable
        $this->getDB()->execute(
            'INSERT INTO search_modules (module, searchable, weight) VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE searchable = ?, weight = ?',
            array($module, $searchable, $weight, $searchable, $weight)
        );
    }

    /**
     * Set the rights for an action
     *
     * @param int    $groupId The group wherefore the rights will be set.
     * @param string $module  The module wherein the action appears.
     * @param string $action  The action wherefore the rights have to set.
     * @param int    $level   The level, default is 7 (max).
     */
    protected function setActionRights($groupId, $module, $action, $level = 7)
    {
        $groupId = (int) $groupId;
        $module = (string) $module;
        $action = (string) $action;
        $level = (int) $level;

        // check if the action already exists
        $exists = (bool) $this->getDB()->getVar(
            'SELECT 1
             FROM groups_rights_actions
             WHERE group_id = ? AND module = ? AND action = ?
             LIMIT 1',
            array($groupId, $module, $action)
        );

        // action doesn't exist
        if (!$exists) {
            // build item
            $item = array(
                'group_id' => $groupId,
                'module' => $module,
                'action' => $action,
                'level' => $level,
            );

            $this->getDB()->insert('groups_rights_actions', $item);
        }
    }

    /**
     * Sets the rights for a module
     *
     * @param int    $groupId The group wherefore the rights will be set.
     * @param string $module  The module too set the rights for.
     */
    protected function setModuleRights($groupId, $module)
    {
        $groupId = (int) $groupId;
        $module = (string) $module;

        // module doesn't exist
        if (!(bool) $this->getDB()->getVar(
            'SELECT 1
             FROM groups_rights_modules
             WHERE group_id = ? AND module = ?
             LIMIT 1',
            array((int) $groupId, (string) $module)
        )
        ) {
            $item = array(
                'group_id' => $groupId,
                'module' => $module,
            );

            $this->getDB()->insert('groups_rights_modules', $item);
        }
    }

    /**
     * Set a new navigation item.
     *
     * @param int    $parentId    Id of the navigation item under we should add this.
     * @param string $label       Label for the item.
     * @param string $url         Url for the item. If omitted the first child is used.
     * @param array  $selectedFor Set selected when these actions are active.
     * @param int    $sequence    Sequence to use for this item.
     *
     * @return int
     */
    protected function setNavigation($parentId, $label, $url = '', array $selectedFor = null, $sequence = null)
    {
        $parentId = (int) $parentId;
        $label = (string) $label;
        $url = (string) $url;
        $selectedFor = ($selectedFor !== null && is_array($selectedFor)) ? serialize($selectedFor) : null;

        // no custom sequence
        if ($sequence === null) {
            // get maximum sequence for this parent
            $maxSequence = (int) $this->getDB()->getVar(
                'SELECT MAX(sequence)
                 FROM backend_navigation
                 WHERE parent_id = ?',
                array($parentId)
            );

            // add at the end
            $sequence = $maxSequence + 1;
        } else {
            $sequence = (int) $sequence;
        }

        // get the id for this url
        $id = (int) $this->getDB()->getVar(
            'SELECT id
             FROM backend_navigation
             WHERE parent_id = ? AND label = ? AND url = ?',
            array($parentId, $label, $url)
        );

        // doesn't exist yet, add it
        if ($id === 0) {
            return $this->getDB()->insert(
                'backend_navigation',
                array(
                    'parent_id' => $parentId,
                    'label' => $label,
                    'url' => $url,
                    'selected_for' => $selectedFor,
                    'sequence' => $sequence,
                )
            );
        }

        // already exists so return current id
        return $id;
    }

    /**
     * Stores a module specific setting in the database.
     *
     * @param string $module    The module wherefore the setting will be set.
     * @param string $name      The name of the setting.
     * @param mixed  $value     The optional value.
     * @param bool   $overwrite Overwrite no matter what.
     */
    protected function setSetting($module, $name, $value = null, $overwrite = false)
    {
        $module = (string) $module;
        $name = (string) $name;
        $value = serialize($value);
        $overwrite = (bool) $overwrite;

        if ($overwrite) {
            $this->getDB()->execute(
                'INSERT INTO modules_settings (module, name, value)
                 VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE value = ?',
                array($module, $name, $value, $value)
            );
        } else {
            // check if this setting already exists
            $exists = (bool) $this->getDB()->getVar(
                'SELECT 1
                 FROM modules_settings
                 WHERE module = ? AND name = ?
                 LIMIT 1',
                array($module, $name)
            );

            // does not yet exist
            if (!$exists) {
                // build item
                $item = array(
                    'module' => $module,
                    'name' => $name,
                    'value' => $value,
                );

                $this->getDB()->insert('modules_settings', $item);
            }
        }
    }
}
