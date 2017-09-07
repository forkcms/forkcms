<?php

namespace Backend\Core\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Trait UninstallerTrait
 */
trait UninstallerTrait
{
    private $tables;

    /**
     * @return \SpoonDatabase
     */
    abstract protected function getDatabase(): \SpoonDatabase;

    /**
     * @return string
     */
    abstract protected function getModule(): string;

    /**
     * @return null|\Symfony\Component\Console\Input\InputInterface
     */
    abstract public function getInput(): ?InputInterface;

    /**
     * @return null|\Symfony\Component\Console\Output\OutputInterface
     */
    abstract public function getOutput(): ?OutputInterface;

    /**
     * @param string|array $table
     * @return bool
     */
    protected function tableExists($table): bool
    {
        if (null === $this->tables) {
            $this->tables = $this->getDatabase()->getTables();
        }

        $result = true;

        foreach ((array)$table as $tableName) {
            if (!in_array($tableName, $this->tables, true)) {
                $result = false;
                break;
            }
        }

        return $result;
    }

    /**
     * Full delete module
     *
     * @param string|null $module
     */
    protected function dropModule(string $module = null): void
    {
        $module = $module ?? $this->getModule();

        $this->deleteModuleExtra($module);

        $this->deleteModuleRights($module);
        $this->deleteActionRights($module);

        $this->deleteSettings($module);
        $this->deleteLocale($module);
        $this->deleteSearchable($module);
        $this->deleteModule($module);
    }

    /**
     * @param array|string $tables
     */
    protected function dropDatabase($tables)
    {
        $drop = [];

        foreach ((array)$tables as $table) {
            if (!empty($table) && $this->tableExists($table)) {
                $drop[] = $table;
            }
        }

        if (!empty($drop)) {
            $this->getDatabase()->drop($drop);
        }

        // clear exists cache
        $this->tables = null;
    }

    /**
     * Delete a module.
     *
     * @param string $module The name of the module.
     */
    protected function deleteModule(string $module): void
    {
        if ($this->tableExists('modules')) {
            $this->getDatabase()->delete(
                'modules',
                'name = ?',
                [$module]
            );
        }
    }

    /**
     * Delete searchable mark for module.
     *
     * @param string $module The name of the module.
     */
    protected function deleteSearchable(string $module): void
    {
        if ($this->tableExists('search_modules')) {
            $this->getDatabase()->delete(
                'search_modules',
                'module = ?',
                [$module]
            );
        }
    }

    /**
     * Delete module locale.
     *
     * @param string $module The name of the module.
     */
    protected function deleteLocale(string $module): void
    {
        if ($this->tableExists('locale')) {
            $this->getDatabase()->delete(
                'locale',
                'module = ?',
                [$module]
            );
        }
    }

    /**
     * Delete module settings.
     *
     * @param string $module The name of the module.
     * @param string|null $name The name of the option.
     */
    protected function deleteSettings(string $module, string $name = null): void
    {
        if ($this->tableExists('modules_settings')) {
            $where = 'module = ?';
            $parameters = [$module];

            if (null !== $name) {
                $where .= ' AND name = ?';
                $parameters[] = $name;
            }

            $this->getDatabase()->delete(
                'modules_settings',
                $where,
                $parameters
            );
        }
    }

    /**
     * Inserts a new module.
     *
     * @param string $module The name of the module.
     * @param array|null $labels
     */
    protected function deleteModuleExtra(string $module, array $labels = null): void
    {
        if ($this->tableExists('modules_extras')) {
            $where = 'module = ?';
            $parameters = [$module];

            if (null !== $labels) {
                if (count($labels) === 1) {
                    $where .= ' AND label = ?';
                    $parameters[] = array_shift($labels);
                } else {
                    $query = str_repeat('?, ', count($labels) - 1) . '?';

                    $where .= " AND label IN ($query)";
                    $parameters = array_merge($parameters, $labels);
                }
            }

            $this->getDatabase()->delete(
                'modules_extras',
                $where,
                $parameters
            );
        }
    }

    /**
     * Get a navigation item.
     *
     * @param int|null $parentId Id of the navigation item under we should add this.
     * @param string $label Label for the item.
     * @param string|null $url Url for the item. If omitted the first child is used.
     * @return int|null
     */
    protected function getNavigation($parentId, string $label = null): ?int
    {
        if ($this->tableExists('backend_navigation')) {
            // get the id for this url
            return (int)$this->getDatabase()->getVar(
                'SELECT id
             FROM backend_navigation
             WHERE parent_id = ? AND label = ?',
                [$parentId, $label]
            );
        }

        return null;
    }

    /**
     * Delete a navigation item.
     *
     * @param string $path path of removed navigation
     */
    protected function deleteNavigation($path): void
    {
        if ($this->tableExists('backend_navigation')) {
            $output = $this->getOutput();

            $chunks = explode('.', $path);

            if ($output->isVerbose()) {
                $output->writeln('Check navigation: ' . $path);
            }

            if (!empty($chunks)) {
                $lastNavItemId = 0;

                foreach ($chunks as $item) {
                    if ($output->isVeryVerbose()) {
                        $output->write('Getting [' . ($lastNavItemId ?? 'null') . ' -> ' . $item . ']: ');
                    }

                    $lastNavItemId = $this->getNavigation($lastNavItemId, $item);

                    if ($output->isVeryVerbose()) {
                        $output->writeln($lastNavItemId ?? 'null');
                    }
                }

                if (0 !== $lastNavItemId) {
                    if ($output->isVerbose()) {
                        $output->writeln('Remove navigation: ' . $lastNavItemId ?? 'null');
                    }

                    $this->getDatabase()->delete(
                        'backend_navigation',
                        'id = ?',
                        [$lastNavItemId]
                    );
                }
            }
        }
    }

    /**
     * Delete the rights for an action
     *
     * @param string $module The module wherein the action appears.
     */
    protected function deleteActionRights(string $module): void
    {
        if ($this->tableExists('groups_rights_actions')) {
            $this->getDatabase()->delete(
                'groups_rights_actions',
                'module = ?',
                [$module]
            );
        }
    }

    /**
     * Delete the rights for a module
     *
     * @param string $module The module too set the rights for.
     */
    protected function deleteModuleRights(string $module): void
    {
        if ($this->tableExists('groups_rights_modules')) {
            $this->getDatabase()->delete(
                'groups_rights_modules',
                'module = ?',
                [$module]
            );
        }
    }

    /**
     * Delete dashboard widgets
     *
     * @param string $module
     * @param array $widgets
     */
    protected function deleteDashboardWidgets(string $module, array $widgets): void
    {
        if ($this->tableExists(['groups_settings', 'users_settings'])) {
            // get database
            $database = $this->getDatabase();

            // fetch current settings
            $groupSettings = (array)$database->getRecords(
                'SELECT * FROM groups_settings WHERE name = ?',
                ['dashboard_sequence']
            );
            $userSettings = (array)$database->getRecords(
                'SELECT * FROM users_settings WHERE name = ?',
                ['dashboard_sequence']
            );

            // loop group settings
            foreach ($groupSettings as $settings) {
                // unserialize data
                $settings['value'] = unserialize($settings['value']);

                foreach ($widgets as $widget) {
                    $settings['value'][$module] = array_filter(
                        $settings['value'][$module],
                        function ($item) use ($widget) {
                            return $widget !== $item;
                        }
                    );
                }

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

                foreach ($widgets as $widget) {
                    $settings['value'][$module] = array_filter(
                        $settings['value'][$module],
                        function ($item) use ($widget) {
                            return $widget !== $item;
                        }
                    );
                }

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
    }

    /**
     * Delete pages.
     *
     * @param array $pages
     */
    protected function deletePages(array $pages): void
    {
        if ($this->tableExists('pages')) {
            $this->getDatabase()->delete(
                'pages',
                'title IN (' . str_repeat('?, ', count($pages) - 1) . ' ?)',
                $pages
            );
        }
    }
}
