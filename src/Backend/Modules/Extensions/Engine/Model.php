<?php

namespace Backend\Modules\Extensions\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\ModulesSettings;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
use Backend\Core\Engine\Navigation;
use Backend\Core\Engine\Exception;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;

/**
 * In this file we store all generic functions that we will be using in the extensions module.
 */
class Model
{
    /**
     * Overview of templates.
     *
     * @var string
     */
    const QUERY_BROWSE_TEMPLATES = 'SELECT i.id, i.label AS title
                                  FROM themes_templates AS i
                                  WHERE i.theme = ?
                                  ORDER BY i.label ASC';

    /**
     * Modules which are part of the core and can not be managed.
     *
     * @var array
     */
    private static $ignoredModules = [
        'Authentication',
        'Dashboard',
        'Error',
        'Extensions',
        'Settings',
    ];

    /**
     * Build HTML for a template (visual representation)
     *
     * @param string $format The template format.
     * @param bool $large Will the HTML be used in a large version?
     *
     * @return string
     */
    public static function buildTemplateHTML(string $format, bool $large = false): string
    {
        // cleanup
        $table = self::templateSyntaxToArray($format);

        // init var
        $rows = count($table);
        $cells = count($table[0]);

        $htmlContent = [];

        // loop rows
        for ($y = 0; $y < $rows; ++$y) {
            $htmlContent[$y] = [];

            // loop cells
            for ($x = 0; $x < $cells; ++$x) {
                // skip if needed
                if (!isset($table[$y][$x])) {
                    continue;
                }

                // get value
                $value = $table[$y][$x];

                // init var
                $colspan = 1;

                // reset items in the same column
                while ($x + $colspan < $cells && $table[$y][$x + $colspan] === $value) {
                    $table[$y][$x + $colspan++] = null;
                }

                // init var
                $rowspan = 1;
                $rowMatches = true;

                // loop while the rows match
                while ($rowMatches && $y + $rowspan < $rows) {
                    // loop columns inside spanned columns
                    for ($i = 0; $i < $colspan; ++$i) {
                        // check value
                        if ($table[$y + $rowspan][$x + $i] !== $value) {
                            // no match, so stop
                            $rowMatches = false;
                            break;
                        }
                    }

                    // any rowmatches?
                    if ($rowMatches) {
                        // loop columns and reset value
                        for ($i = 0; $i < $colspan; ++$i) {
                            $table[$y + $rowspan][$x + $i] = null;
                        }

                        // increment
                        ++$rowspan;
                    }
                }

                $htmlContent[$y][$x] = [
                    'title' => \SpoonFilter::ucfirst($value),
                    'value' => $value,
                    'exists' => $value != '/',
                    'rowspan' => $rowspan,
                    'colspan' => $colspan,
                    'large' => $large,
                ];
            }
        }

        $templating = BackendModel::get('template');
        $templating->assign('table', $htmlContent);
        $html = $templating->getContent('Extensions/Layout/Templates/Templates.html.twig');

        return $html;
    }

    /**
     * Checks the settings and optionally returns an array with warnings
     *
     * @return array
     */
    public static function checkSettings(): array
    {
        $warnings = [];
        $akismetModules = self::getModulesThatRequireAkismet();
        $googleMapsModules = self::getModulesThatRequireGoogleMaps();

        // check if this action is allowed
        if (!BackendAuthentication::isAllowedAction('Index', 'Settings')) {
            return [];
        }

        // check if the akismet key is available if there are modules that require it
        if (!empty($akismetModules) && BackendModel::get('fork.settings')->get('Core', 'akismet_key', null) == '') {
            // add warning
            $warnings[] = [
                'message' => sprintf(
                    BL::err('AkismetKey'),
                    BackendModel::createUrlForAction('Index', 'Settings')
                ),
            ];
        }

        // check if the google maps key is available if there are modules that require it
        if (!empty($googleMapsModules)
            && BackendModel::get('fork.settings')->get('Core', 'google_maps_key', null) == '') {
            // add warning
            $warnings[] = [
                'message' => sprintf(
                    BL::err('GoogleMapsKey'),
                    BackendModel::createUrlForAction('Index', 'Settings')
                ),
            ];
        }

        return $warnings;
    }

    /**
     * Clear all applications cache.
     *
     * Note: we do not need to rebuild anything, the core will do this when noticing the cache files are missing.
     */
    public static function clearCache(): void
    {
        $finder = new Finder();
        $filesystem = new Filesystem();
        $files = $finder->files()
            ->name('*.php')
            ->name('*.js')
            ->in(BACKEND_CACHE_PATH . '/Locale')
            ->in(FRONTEND_CACHE_PATH . '/Navigation')
            ->in(FRONTEND_CACHE_PATH . '/Locale');
        foreach ($files as $file) {
            $filesystem->remove($file->getRealPath());
        }
        BackendModel::getContainer()->get('cache.backend_navigation')->delete();
    }

    /**
     * Delete a template.
     *
     * @param int $id The id of the template to delete.
     *
     * @return bool
     */
    public static function deleteTemplate(int $id): bool
    {
        $templates = self::getTemplates();

        // we can't delete a template that doesn't exist
        if (!isset($templates[$id])) {
            return false;
        }

        // we can't delete the last template
        if (count($templates) === 1) {
            return false;
        }

        // we can't delete the default template
        if ($id == BackendModel::get('fork.settings')->get('Pages', 'default_template')) {
            return false;
        }
        if (self::isTemplateInUse($id)) {
            return false;
        }

        $database = BackendModel::getContainer()->get('database');
        $database->delete('themes_templates', 'id = ?', $id);
        $ids = (array) $database->getColumn(
            'SELECT i.revision_id
             FROM pages AS i
             WHERE i.template_id = ? AND i.status != ?',
            [$id, 'active']
        );

        if (!empty($ids)) {
            // delete those pages and the linked blocks
            $database->delete('pages', 'revision_id IN(' . implode(',', $ids) . ')');
            $database->delete('pages_blocks', 'revision_id IN(' . implode(',', $ids) . ')');
        }

        return true;
    }

    /**
     * Does this module exist.
     * This does not check for existence in the database but on the filesystem.
     *
     * @param string $module Module to check for existence.
     *
     * @return bool
     */
    public static function existsModule(string $module): bool
    {
        return is_dir(BACKEND_MODULES_PATH . '/' . $module);
    }

    /**
     * Check if a template exists
     *
     * @param int $id The Id of the template to check for existence.
     *
     * @return bool
     */
    public static function existsTemplate(int $id): bool
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT i.id FROM themes_templates AS i WHERE i.id = ?',
            [$id]
        );
    }

    /**
     * Does this template exist.
     * This does not check for existence in the database but on the filesystem.
     *
     * @param string $theme Theme to check for existence.
     *
     * @return bool
     */
    public static function existsTheme(string $theme): bool
    {
        return is_dir(FRONTEND_PATH . '/Themes/' . (string) $theme) || $theme === 'Core';
    }

    public static function getExtras(): array
    {
        $extras = (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id, i.module, i.type, i.label, i.data
             FROM modules_extras AS i
             INNER JOIN modules AS m ON i.module = m.name
             WHERE i.hidden = ?
             ORDER BY i.module, i.sequence',
            [false],
            'id'
        );
        $itemsToRemove = [];

        foreach ($extras as $id => &$row) {
            $row['data'] = $row['data'] === null ? [] : @unserialize($row['data']);
            if (isset($row['data']['language']) && $row['data']['language'] != BL::getWorkingLanguage()) {
                $itemsToRemove[] = $id;
            }

            // set URL if needed, we use '' instead of null, because otherwise the module of the current action (modules) is used.
            if (!isset($row['data']['url'])) {
                $row['data']['url'] = BackendModel::createUrlForAction('', $row['module']);
            }

            $name = \SpoonFilter::ucfirst(BL::lbl($row['label']));
            if (isset($row['data']['extra_label'])) {
                $name = $row['data']['extra_label'];
            }
            if (isset($row['data']['label_variables'])) {
                $name = vsprintf($name, $row['data']['label_variables']);
            }

            // add human readable name
            $module = \SpoonFilter::ucfirst(BL::lbl(\SpoonFilter::toCamelCase($row['module'])));
            $extraTypeLabel = \SpoonFilter::ucfirst(BL::lbl(\SpoonFilter::toCamelCase('ExtraType_' . $row['type'])));
            $row['human_name'] = $extraTypeLabel . ': ' . $name;
            $row['path'] = $extraTypeLabel . ' › ' . $module . ($module !== $name ? ' › ' . $name : '');
        }

        // any items to remove?
        if (!empty($itemsToRemove)) {
            foreach ($itemsToRemove as $id) {
                unset($extras[$id]);
            }
        }

        return $extras;
    }

    public static function getExtrasData(): array
    {
        $extras = (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id, i.module, i.type, i.label, i.data
             FROM modules_extras AS i
             INNER JOIN modules AS m ON i.module = m.name
             WHERE i.hidden = ?
             ORDER BY i.module, i.sequence',
            [false]
        );
        $values = [];

        foreach ($extras as $row) {
            $row['data'] = @unserialize($row['data']);

            // remove items that are not for the current language
            if (isset($row['data']['language']) && $row['data']['language'] != BL::getWorkingLanguage()) {
                continue;
            }

            // set URL if needed
            if (!isset($row['data']['url'])) {
                $row['data']['url'] = BackendModel::createUrlForAction(
                    'Index',
                    $row['module']
                );
            }

            $name = \SpoonFilter::ucfirst(BL::lbl($row['label']));
            if (isset($row['data']['extra_label'])) {
                $name = $row['data']['extra_label'];
            }
            if (isset($row['data']['label_variables'])) {
                $name = vsprintf($name, $row['data']['label_variables']);
            }
            $moduleName = \SpoonFilter::ucfirst(BL::lbl(\SpoonFilter::toCamelCase($row['module'])));

            if (!isset($values[$row['module']])) {
                $values[$row['module']] = [
                    'value' => $row['module'],
                    'name' => $moduleName,
                    'items' => [],
                ];
            }

            $values[$row['module']]['items'][$row['type']][$name] = ['id' => $row['id'], 'label' => $name];
        }

        return $values;
    }

    /**
     * Fetch the module information from the info.xml file.
     *
     * @param string $module
     *
     * @return array
     */
    public static function getModuleInformation(string $module): array
    {
        $pathInfoXml = BACKEND_MODULES_PATH . '/' . $module . '/info.xml';
        $information = ['data' => [], 'warnings' => []];

        if (is_file($pathInfoXml)) {
            try {
                $infoXml = @new \SimpleXMLElement($pathInfoXml, LIBXML_NOCDATA, true);
                $information['data'] = self::processModuleXml($infoXml);
                if (empty($information['data'])) {
                    $information['warnings'][] = [
                        'message' => BL::getMessage('InformationFileIsEmpty'),
                    ];
                }

                // check if cronjobs are installed already
                if (isset($information['data']['cronjobs'])) {
                    foreach ($information['data']['cronjobs'] as $cronjob) {
                        if (!$cronjob['active']) {
                            $information['warnings'][] = [
                                'message' => BL::getError('CronjobsNotSet'),
                            ];
                        }
                        break;
                    }
                }
            } catch (Exception $e) {
                $information['warnings'][] = [
                    'message' => BL::getMessage('InformationFileCouldNotBeLoaded'),
                ];
            }
        } else {
            $information['warnings'][] = [
                'message' => BL::getMessage('InformationFileIsMissing'),
            ];
        }

        return $information;
    }

    /**
     * Get modules based on the directory listing in the backend application.
     *
     * If a module contains a info.xml it will be parsed.
     *
     * @return array
     */
    public static function getModules(): array
    {
        $installedModules = (array) BackendModel::getContainer()
            ->getParameter('installed_modules');
        $modules = BackendModel::getModulesOnFilesystem(false);
        $manageableModules = [];

        // get more information for each module
        foreach ($modules as $moduleName) {
            if (in_array($moduleName, self::$ignoredModules)) {
                continue;
            }

            $module = [];
            $module['id'] = 'module_' . $moduleName;
            $module['raw_name'] = $moduleName;
            $module['name'] = \SpoonFilter::ucfirst(BL::getLabel(\SpoonFilter::toCamelCase($moduleName)));
            $module['description'] = '';
            $module['version'] = '';
            $module['installed'] = false;

            if (in_array($moduleName, $installedModules)) {
                $module['installed'] = true;
            }

            try {
                $infoXml = @new \SimpleXMLElement(
                    BACKEND_MODULES_PATH . '/' . $module['raw_name'] . '/info.xml',
                    LIBXML_NOCDATA,
                    true
                );

                $info = self::processModuleXml($infoXml);

                // set fields if they were found in the XML
                if (isset($info['description'])) {
                    $module['description'] = BackendDataGridFunctions::truncate($info['description'], 80);
                }
                if (isset($info['version'])) {
                    $module['version'] = $info['version'];
                }
            } catch (\Exception $e) {
                // don't act upon error, we simply won't possess some info
            }

            $manageableModules[] = $module;
        }

        return $manageableModules;
    }

    /**
     * Fetch the list of modules that require Akismet API key
     *
     * @return array
     */
    public static function getModulesThatRequireAkismet(): array
    {
        return self::getModulesThatRequireSetting('akismet');
    }

    /**
     * Fetch the list of modules that require Google Maps API key
     *
     * @return array
     */
    public static function getModulesThatRequireGoogleMaps(): array
    {
        return self::getModulesThatRequireSetting('google_maps');
    }

    /**
     * Fetch the list of modules that require Google Recaptcha API key
     *
     * @return array
     */
    public static function getModulesThatRequireGoogleRecaptcha(): array
    {
        return self::getModulesThatRequireSetting('google_recaptcha');
    }

    /**
     * Fetch the list of modules that require a certain setting. The setting is affixed by 'requires_'
     *
     * @param string $setting
     *
     * @return array
     */
    private static function getModulesThatRequireSetting(string $setting): array
    {
        if ($setting === '') {
            return [];
        }

        /** @var ModulesSettings $moduleSettings */
        $moduleSettings = BackendModel::get('fork.settings');

        return array_filter(
            BackendModel::getModules(),
            function (string $module) use ($moduleSettings, $setting): bool {
                $requiresGoogleRecaptcha = $moduleSettings->get($module, 'requires_' . $setting, false);

                return $requiresGoogleRecaptcha;
            }
        );
    }

    public static function getTemplate(int $id): array
    {
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT i.* FROM themes_templates AS i WHERE i.id = ?',
            [$id]
        );
    }

    public static function getTemplates(string $theme = null): array
    {
        $database = BackendModel::getContainer()->get('database');
        $theme = \SpoonFilter::getValue(
            (string) $theme,
            null,
            BackendModel::get('fork.settings')->get('Core', 'theme', 'Fork')
        );

        $templates = (array) $database->getRecords(
            'SELECT i.id, i.label, i.path, i.data
            FROM themes_templates AS i
            WHERE i.theme = ? AND i.active = ?
            ORDER BY i.label ASC',
            [$theme, true],
            'id'
        );

        $extras = (array) self::getExtras();
        $half = (int) ceil(count($templates) / 2);
        $i = 0;

        foreach ($templates as &$row) {
            $row['data'] = unserialize($row['data']);
            $row['has_block'] = false;

            // reset
            if (isset($row['data']['default_extras_' . BL::getWorkingLanguage()])) {
                $row['data']['default_extras'] = $row['data']['default_extras_' . BL::getWorkingLanguage()];
            }

            // any extras?
            if (isset($row['data']['default_extras'])) {
                foreach ($row['data']['default_extras'] as $value) {
                    if (\SpoonFilter::isInteger($value)
                        && isset($extras[$value]) && $extras[$value]['type'] == 'block'
                    ) {
                        $row['has_block'] = true;
                    }
                }
            }

            // validate
            if (!isset($row['data']['format'])) {
                throw new Exception('Invalid template-format.');
            }

            $row['html'] = self::buildTemplateHTML($row['data']['format']);
            $row['htmlLarge'] = self::buildTemplateHTML($row['data']['format'], true);
            $row['json'] = json_encode($row);
            if ($i == $half) {
                $row['break'] = true;
            }
            ++$i;
        }

        return (array) $templates;
    }

    public static function getThemes(): array
    {
        $records = [];
        $finder = new Finder();
        foreach ($finder->directories()->in(FRONTEND_PATH . '/Themes')->depth(0) as $directory) {
            $pathInfoXml = BackendModel::getContainer()->getParameter('site.path_www') . '/src/Frontend/Themes/'
                           . $directory->getBasename() . '/info.xml';
            if (!is_file($pathInfoXml)) {
                throw new Exception('info.xml is missing for the theme ' . $directory->getBasename());
            }
            try {
                $infoXml = @new \SimpleXMLElement($pathInfoXml, LIBXML_NOCDATA, true);
                $information = self::processThemeXml($infoXml);
                if (empty($information)) {
                    throw new Exception('Invalid info.xml');
                }
            } catch (Exception $e) {
                $information['thumbnail'] = 'thumbnail.png';
            }

            $item = [];
            $item['value'] = $directory->getBasename();
            $item['label'] = $directory->getBasename();
            $item['thumbnail'] = '/src/Frontend/Themes/' . $item['value'] . '/' . $information['thumbnail'];
            $item['installed'] = self::isThemeInstalled($item['value']);
            $item['installable'] = isset($information['templates']);

            $records[$item['value']] = $item;
        }

        return (array) $records;
    }

    public static function createTemplateXmlForExport(string $theme): string
    {
        $charset = BackendModel::getContainer()->getParameter('kernel.charset');

        // build xml
        $xml = new \DOMDocument('1.0', $charset);
        $xml->preserveWhiteSpace = false;
        $xml->formatOutput = true;

        $root = $xml->createElement('templates');
        $xml->appendChild($root);

        $database = BackendModel::getContainer()->get('database');

        $records = $database->getRecords(self::QUERY_BROWSE_TEMPLATES, [$theme]);

        foreach ($records as $row) {
            $template = self::getTemplate($row['id']);
            $data = unserialize($template['data']);

            $templateElement = $xml->createElement('template');
            $templateElement->setAttribute('label', $template['label']);
            $templateElement->setAttribute('path', $template['path']);
            $root->appendChild($templateElement);

            $positionsElement = $xml->createElement('positions');
            $templateElement->appendChild($positionsElement);

            foreach ($data['names'] as $name) {
                $positionElement = $xml->createElement('position');
                $positionElement->setAttribute('name', $name);
                $positionsElement->appendChild($positionElement);
            }

            $formatElement = $xml->createElement('format');
            $templateElement->appendChild($formatElement);
            $formatElement->nodeValue = $data['format'];
        }

        return $xml->saveXML();
    }

    public static function hasModuleWarnings(string $module): string
    {
        $moduleInformation = self::getModuleInformation($module);

        return !empty($moduleInformation['warnings']);
    }

    public static function insertTemplate(array $template): int
    {
        return (int) BackendModel::getContainer()->get('database')->insert('themes_templates', $template);
    }

    public static function installModule(string $module): void
    {
        $class = 'Backend\\Modules\\' . $module . '\\Installer\\Installer';
        $variables = [];

        // run installer
        $installer = new $class(
            BackendModel::getContainer()->get('database'),
            BL::getActiveLanguages(),
            array_keys(BL::getInterfaceLanguages()),
            false,
            $variables
        );

        $installer->install();

        // clear the cache so locale (and so much more) gets rebuilt
        self::clearCache();
    }

    public static function installTheme(string $theme): void
    {
        $pathInfoXml = FRONTEND_PATH . '/Themes/' . $theme . '/info.xml';
        $infoXml = @new \SimpleXMLElement($pathInfoXml, LIBXML_NOCDATA, true);

        $information = self::processThemeXml($infoXml);
        if (empty($information)) {
            throw new Exception('Invalid info.xml');
        }

        foreach ($information['templates'] as $template) {
            $item = [];
            $item['theme'] = $information['name'];
            $item['label'] = $template['label'];
            $item['path'] = $template['path'];
            $item['active'] = true;
            $item['data']['format'] = $template['format'];
            $item['data']['image'] = $template['image'];

            // build positions
            $item['data']['names'] = [];
            $item['data']['default_extras'] = [];
            foreach ($template['positions'] as $position) {
                $item['data']['names'][] = $position['name'];
                $item['data']['default_extras'][$position['name']] = [];

                // add default widgets
                foreach ($position['widgets'] as $widget) {
                    // fetch extra_id for this extra
                    $extraId = (int) BackendModel::getContainer()->get('database')->getVar(
                        'SELECT i.id
                         FROM modules_extras AS i
                         WHERE type = ? AND module = ? AND action = ? AND data IS NULL AND hidden = ?',
                        ['widget', $widget['module'], $widget['action'], false]
                    );

                    // add extra to defaults
                    if ($extraId) {
                        $item['data']['default_extras'][$position['name']][] = $extraId;
                    }
                }

                // add default editors
                foreach ($position['editors'] as $editor) {
                    $item['data']['default_extras'][$position['name']][] = 0;
                }
            }

            $item['data'] = serialize($item['data']);
            $item['id'] = self::insertTemplate($item);
        }
    }

    public static function isModuleInstalled(string $module): bool
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM modules
             WHERE name = ?
             LIMIT 1',
            $module
        );
    }

    /**
     * Is the provided template id in use by active versions of pages?
     *
     * @param int $templateId The id of the template to check.
     *
     * @return bool
     */
    public static function isTemplateInUse(int $templateId): bool
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM pages AS i
             WHERE i.template_id = ? AND i.status = ?
             LIMIT 1',
            [$templateId, 'active']
        );
    }

    public static function isThemeInstalled(string $theme): bool
    {
        return (bool) BackendModeL::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM themes_templates
             WHERE theme = ?
             LIMIT 1',
            [$theme]
        );
    }

    /**
     * Check if a directory is writable.
     * The default is_writable function has problems due to Windows ACLs "bug"
     *
     * @param string $path The path to check.
     *
     * @return bool
     */
    public static function isWritable(string $path): bool
    {
        $path = rtrim((string) $path, '/');
        $file = uniqid('', true) . '.tmp';
        $return = @file_put_contents($path . '/' . $file, 'temporary file', FILE_APPEND);
        if ($return === false) {
            return false;
        }
        unlink($path . '/' . $file);

        return true;
    }

    public static function processModuleXml(\SimpleXMLElement $xml): array
    {
        $information = [];

        // fetch theme node
        $module = $xml->xpath('/module');
        if (isset($module[0])) {
            $module = $module[0];
        }

        // fetch general module info
        $information['name'] = (string) $module->name;
        $information['version'] = (string) $module->version;
        $information['requirements'] = (array) $module->requirements;
        $information['description'] = (string) $module->description;
        $information['cronjobs'] = [];

        // authors
        foreach ($xml->xpath('/module/authors/author') as $author) {
            $information['authors'][] = (array) $author;
        }

        // cronjobs
        foreach ($xml->xpath('/module/cronjobs/cronjob') as $cronjob) {
            $attributes = $cronjob->attributes();
            if (!isset($attributes['action'])) {
                continue;
            }

            // build cronjob information
            $item = [];
            $item['minute'] = (isset($attributes['minute'])) ? $attributes['minute'] : '*';
            $item['hour'] = (isset($attributes['hour'])) ? $attributes['hour'] : '*';
            $item['day-of-month'] = (isset($attributes['day-of-month'])) ? $attributes['day-of-month'] : '*';
            $item['month'] = (isset($attributes['month'])) ? $attributes['month'] : '*';
            $item['day-of-week'] = (isset($attributes['day-of-week'])) ? $attributes['day-of-week'] : '*';
            $item['action'] = $attributes['action'];
            $item['description'] = $cronjob[0];

            // check if cronjob has already been run
            $cronjobs = (array) BackendModel::get('fork.settings')->get('Core', 'cronjobs');
            $item['active'] = in_array($information['name'] . '.' . $attributes['action'], $cronjobs);

            $information['cronjobs'][] = $item;
        }

        // events
        foreach ($xml->xpath('/module/events/event') as $event) {
            $attributes = $event->attributes();

            // build event information and add it to the list
            $information['events'][] = [
                'application' => (isset($attributes['application'])) ? $attributes['application'] : '',
                'name' => (isset($attributes['name'])) ? $attributes['name'] : '',
                'description' => $event[0],
            ];
        }

        return $information;
    }

    public static function processThemeXml(\SimpleXMLElement $xml): array
    {
        $information = [];

        $theme = $xml->xpath('/theme');
        if (isset($theme[0])) {
            $theme = $theme[0];
        }

        // fetch general theme info
        $information['name'] = (string) $theme->name;
        $information['version'] = (string) $theme->version;
        $information['requirements'] = (array) $theme->requirements;
        $information['thumbnail'] = (string) $theme->thumbnail;
        $information['description'] = (string) $theme->description;

        // authors
        foreach ($xml->xpath('/theme/authors/author') as $author) {
            $information['authors'][] = (array) $author;
        }

        // meta navigation
        $meta = $theme->metanavigation->attributes();
        if (isset($meta['supported'])) {
            $information['meta'] = (string) $meta['supported'] && (string) $meta['supported'] !== 'false';
        }

        // templates
        $information['templates'] = [];
        foreach ($xml->xpath('/theme/templates/template') as $templateXML) {
            $template = [];

            // template data
            $template['label'] = (string) $templateXML['label'];
            $template['path'] = (string) $templateXML['path'];
            $template['image'] = isset($templateXML['image'])
                ? (string) $templateXML['image'] && (string) $templateXML['image'] !== 'false' : false;
            $template['format'] = trim(str_replace(["\n", "\r", ' '], '', (string) $templateXML->format));

            // loop positions
            foreach ($templateXML->positions->position as $positionXML) {
                $position = [];

                $position['name'] = (string) $positionXML['name'];

                // widgets
                $position['widgets'] = [];
                if ($positionXML->defaults->widget) {
                    foreach ($positionXML->defaults->widget as $widget) {
                        $position['widgets'][] = [
                            'module' => (string) $widget['module'],
                            'action' => (string) $widget['action'],
                        ];
                    }
                }

                // editor
                $position['editors'] = [];
                if ($positionXML->defaults->editor) {
                    foreach ($positionXML->defaults->editor as $editor) {
                        $position['editors'][] = (string) trim($editor);
                    }
                }

                $template['positions'][] = $position;
            }

            $information['templates'][] = $template;
        }

        return self::validateThemeInformation($information);
    }

    public static function templateSyntaxToArray(string $syntax): array
    {
        $syntax = (string) $syntax;
        $syntax = trim(str_replace(["\n", "\r", ' '], '', $syntax));
        $table = [];

        // split into rows
        $rows = explode('],[', $syntax);

        foreach ($rows as $i => $row) {
            $row = trim(str_replace(['[', ']'], '', $row));
            $table[$i] = (array) explode(',', $row);
        }

        if (!isset($table[0])) {
            return [];
        }

        $columns = count($table[0]);

        foreach ($table as $row) {
            if (count($row) !== $columns) {
                return [];
            }
        }

        return $table;
    }

    public static function updateTemplate(array $templateData): void
    {
        BackendModel::getContainer()->get('database')->update(
            'themes_templates',
            $templateData,
            'id = ?',
            [(int) $templateData['id']]
        );
    }

    /**
     * Make sure that we have an entirely valid theme information array
     *
     * @param array $information Contains the parsed theme info.xml data.
     *
     * @return array
     */
    public static function validateThemeInformation(array $information): array
    {
        // set default thumbnail if not sets
        if (!$information['thumbnail']) {
            $information['thumbnail'] = 'thumbnail.png';
        }

        // check if there are templates
        if (isset($information['templates']) && $information['templates']) {
            foreach ($information['templates'] as $i => $template) {
                if (!isset($template['label']) || !$template['label'] || !isset($template['path']) || !$template['path'] || !isset($template['format']) || !$template['format']) {
                    unset($information['templates'][$i]);
                    continue;
                }

                // if there are no positions we should continue with the next item
                if (!isset($template['positions']) && $template['positions']) {
                    continue;
                }

                // loop positions
                foreach ($template['positions'] as $j => $position) {
                    if (!isset($position['name']) || !$position['name']) {
                        unset($information['templates'][$i]['positions'][$j]);
                        continue;
                    }

                    // ensure widgets are well-formed
                    if (!isset($position['widgets']) || !$position['widgets']) {
                        $information['templates'][$i]['positions'][$j]['widgets'] = [];
                    }

                    // ensure editors are well-formed
                    if (!isset($position['editors']) || !$position['editors']) {
                        $information['templates'][$i]['positions'][$j]['editors'] = [];
                    }

                    // loop widgets
                    foreach ($position['widgets'] as $k => $widget) {
                        // check if widget is valid
                        if (!isset($widget['module']) || !$widget['module'] || !isset($widget['action']) || !$widget['action']) {
                            unset($information['templates'][$i]['positions'][$j]['widgets'][$k]);
                            continue;
                        }
                    }
                }

                // check if there still are valid positions
                if (!isset($information['templates'][$i]['positions']) || !$information['templates'][$i]['positions']) {
                    return [];
                }
            }

            // check if there still are valid templates
            if (!isset($information['templates']) || !$information['templates']) {
                return [];
            }
        }

        return $information;
    }
}
