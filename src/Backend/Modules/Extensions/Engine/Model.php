<?php

namespace Backend\Modules\Extensions\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Finder\Finder;

use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
use Backend\Core\Engine\Exception;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;

/**
 * In this file we store all generic functions that we will be using in the extensions module.
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 */
class Model
{
    /**
     * Overview of templates.
     *
     * @var    string
     */
    const QRY_BROWSE_TEMPLATES = 'SELECT i.id, i.label AS title
                                  FROM themes_templates AS i
                                  WHERE i.theme = ?
                                  ORDER BY i.label ASC';

    /**
     * Modules which are part of the core and can not be managed.
     *
     * @var    array
     */
    private static $ignoredModules = array(
        'Authentication',
        'Dashboard',
        'Error',
        'Extensions',
        'Settings'
    );

    /**
     * Build HTML for a template (visual representation)
     *
     * @param array $format The template format.
     * @param bool  $large  Will the HTML be used in a large version?
     * @return string
     */
    public static function buildTemplateHTML($format, $large = false)
    {
        // cleanup
        $table = self::templateSyntaxToArray($format);

        // add start html
        $html = '<table cellspacing="10">' . "\n";
        $html .= '	<tbody>' . "\n";

        // init var
        $rows = count($table);
        $cells = count($table[0]);

        // loop rows
        for ($y = 0; $y < $rows; $y++) {
            // start row
            $html .= '		<tr>' . "\n";

            // loop cells
            for ($x = 0; $x < $cells; $x++) {
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
                    for ($i = 0; $i < $colspan; $i++) {
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
                        for ($i = 0; $i < $colspan; $i++) {
                            $table[$y + $rowspan][$x + $i] = null;
                        }

                        // increment
                        $rowspan++;
                    }
                }

                // decide state
                $exists = $value != '/';

                // set values
                $title = \SpoonFilter::ucfirst($value);

                // start cell
                $html .= '<td';

                // add rowspan if needed
                if ($rowspan > 1) {
                    $html .= ' rowspan="' . $rowspan . '"';
                }

                // add colspan if needed
                if ($colspan > 1) {
                    $html .= ' colspan="' . $colspan . '"';
                }

                // does the cell need content?
                if (!$exists) {
                    $html .= ' class="empty">&nbsp;</td>' . "\n";
                } else {
                    // large visual?
                    if ($large) {
                        $html .= ' id="templatePosition-' . $value . '" data-position="' . $value . '" class="box">
                                    <div class="heading linkedBlocksTitle"><h3>' . $title . '</h3></div>
                                    <div class="linkedBlocks"><!-- linked blocks will be added here --></div>
                                    <div class="buttonHolder buttonAddHolder">
                                        <a href="#addBlock" class="button icon iconAdd addBlock">
                                            <span>' . \SpoonFilter::ucfirst(BL::lbl('AddBlock')) . '</span>
                                        </a>
                                    </div>
                                </td>' . "\n";
                    } else {
                        $html .= '><a href="#position-' . $value . '" title="' . $title . '">' . $title . '</a></td>' . "\n";
                    }
                }
            }

            // end row
            $html .= '		</tr>' . "\n";
        }

        // end html
        $html .= '	</tbody>' . "\n";
        $html .= '</table>' . "\n";

        return $html;
    }

    /**
     * Checks the settings and optionally returns an array with warnings
     *
     * @return array
     */
    public static function checkSettings()
    {
        $warnings = array();
        $akismetModules = self::getModulesThatRequireAkismet();
        $googleMapsModules = self::getModulesThatRequireGoogleMaps();

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Index', 'Settings')) {
            // check if the akismet key is available if there are modules that require it
            if (!empty($akismetModules) && BackendModel::getModuleSetting('Core', 'akismet_key', null) == '') {
                // add warning
                $warnings[] = array(
                    'message' => sprintf(
                        BL::err('AkismetKey'),
                        BackendModel::createURLForAction('Index', 'Settings')
                    )
                );
            }

            // check if the google maps key is available if there are modules that require it
            if (!empty($googleMapsModules) && BackendModel::getModuleSetting('Core', 'google_maps_key', null) == '') {
                // add warning
                $warnings[] = array(
                    'message' => sprintf(
                        BL::err('GoogleMapsKey'),
                        BackendModel::createURLForAction('Index', 'Settings')
                    )
                );
            }
        }

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Modules', 'Extensions')) {
            // check if there are cronjobs that are not yet set
            $modules = self::getModules();
            foreach ($modules as $module) {
                if (isset($module['cronjobs_active']) && !$module['cronjobs_active']) {
                    // add warning
                    $warnings[] = array(
                        'message' => sprintf(
                            BL::err('CronjobsNotSet', 'Extensions'),
                            BackendModel::createURLForAction('Modules', 'Extensions')
                        )
                    );
                    break;
                }
            }
        }

        return $warnings;
    }

    /**
     * Clear all applications cache.
     *
     * Note: we do not need to rebuild anything, the core will do this when noticing the cache files are missing.
     */
    public static function clearCache()
    {
        $finder = new Finder();
        $fs = new Filesystem();
        foreach (
            $finder->files()
                ->name('*.php')
                ->name('*.js')
                ->in(BACKEND_CACHE_PATH . '/Locale')
                ->in(FRONTEND_CACHE_PATH . '/Navigation')
                ->in(FRONTEND_CACHE_PATH . '/Locale')
            as $file
        ) {
            $fs->remove($file->getRealPath());
        }
        $fs->remove(BACKEND_CACHE_PATH . '/Navigation/navigation.php');
    }

    /**
     * Delete a template.
     *
     * @param int $id The id of the template to delete.
     * @return bool
     */
    public static function deleteTemplate($id)
    {
        $id = (int) $id;
        $templates = self::getTemplates();

        // we can't delete a template that doesn't exist
        if (!isset($templates[$id])) {
            return false;
        }

        // we can't delete the last template
        if (count($templates) == 1) {
            return false;
        }

        // we can't delete the default template
        if ($id == BackendModel::getModuleSetting('Pages', 'default_template')) {
            return false;
        }
        if (self::isTemplateInUse($id)) {
            return false;
        }

        $db = BackendModel::getContainer()->get('database');
        $db->delete('themes_templates', 'id = ?', $id);
        $ids = (array) $db->getColumn(
            'SELECT i.revision_id
             FROM pages AS i
             WHERE i.template_id = ? AND i.status != ?',
            array($id, 'active')
        );

        if (!empty($ids)) {
            // delete those pages and the linked blocks
            $db->delete('pages', 'revision_id IN(' . implode(',', $ids) . ')');
            $db->delete('pages_blocks', 'revision_id IN(' . implode(',', $ids) . ')');
        }

        return true;
    }

    /**
     * Does this module exist.
     * This does not check for existence in the database but on the filesystem.
     *
     * @param string $module Module to check for existence.
     * @return bool
     */
    public static function existsModule($module)
    {
        return is_dir(BACKEND_MODULES_PATH . '/' . (string) $module);
    }

    /**
     * Check if a template exists
     *
     * @param int $id The Id of the template to check for existence.
     * @return bool
     */
    public static function existsTemplate($id)
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT i.id FROM themes_templates AS i	WHERE i.id = ?',
            array((int) $id)
        );
    }

    /**
     * Does this template exist.
     * This does not check for existence in the database but on the filesystem.
     *
     * @param string $theme Theme to check for existence.
     * @return bool
     */
    public static function existsTheme($theme)
    {
        return is_dir(FRONTEND_PATH . '/Themes/' . (string) $theme) || (string) $theme == 'Core';
    }

    /**
     * Get extras
     *
     * @return array
     */
    public static function getExtras()
    {
        $extras = (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id, i.module, i.type, i.label, i.data
             FROM modules_extras AS i
             INNER JOIN modules AS m ON i.module = m.name
             WHERE i.hidden = ?
             ORDER BY i.module, i.sequence',
            array('N'),
            'id'
        );
        $itemsToRemove = array();

        foreach ($extras as $id => &$row) {
            $row['data'] = @unserialize($row['data']);
            if (isset($row['data']['language']) && $row['data']['language'] != BL::getWorkingLanguage()) {
                $itemsToRemove[] = $id;
            }

            // set URL if needed, we use '' instead of null, because otherwise the module of the current action (modules) is used.
            if (!isset($row['data']['url'])) {
                $row['data']['url'] = BackendModel::createURLForAction('', $row['module']);
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
            $row['human_name'] = \SpoonFilter::ucfirst(
                BL::lbl(\SpoonFilter::toCamelCase('ExtraType_' . $row['type']))
            ) . ': ' . $name;
            $row['path'] = \SpoonFilter::ucfirst(
                BL::lbl(\SpoonFilter::toCamelCase('ExtraType_' . $row['type']))
            ) . ' › ' . $module . ($module != $name ? ' › ' . $name : '');
        }

        // any items to remove?
        if (!empty($itemsToRemove)) {
            foreach ($itemsToRemove as $id) {
                unset($extras[$id]);
            }
        }

        return $extras;
    }

    /**
     * Get all the available extra's
     *
     * @return array
     */
    public static function getExtrasData()
    {
        $extras = (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT i.id, i.module, i.type, i.label, i.data
             FROM modules_extras AS i
             INNER JOIN modules AS m ON i.module = m.name
             WHERE i.hidden = ?
             ORDER BY i.module, i.sequence',
            array('N')
        );
        $values = array();

        foreach ($extras as $row) {
            $row['data'] = @unserialize($row['data']);

            // remove items that are not for the current language
            if (isset($row['data']['language']) && $row['data']['language'] != BL::getWorkingLanguage()) {
                continue;
            }

            // set URL if needed
            if (!isset($row['data']['url'])) {
                $row['data']['url'] = BackendModel::createURLForAction(
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
                $values[$row['module']] = array(
                    'value' => $row['module'],
                    'name' => $moduleName,
                    'items' => array()
                );
            }

            $values[$row['module']]['items'][$row['type']][$name] = array('id' => $row['id'], 'label' => $name);
        }

        return $values;
    }

    /**
     * Fetch the module information from the info.xml file.
     *
     * @param string $module
     * @return array
     */
    public static function getModuleInformation($module)
    {
        $pathInfoXml = BACKEND_MODULES_PATH . '/' . $module . '/info.xml';
        $information = array('data' => array(), 'warnings' => array());

        if (is_file($pathInfoXml)) {
            try {
                $infoXml = @new \SimpleXMLElement($pathInfoXml, LIBXML_NOCDATA, true);
                $information['data'] = self::processModuleXml($infoXml);
                if (empty($information['data'])) {
                    $information['warnings'][] = array(
                        'message' => BL::getMessage('InformationFileIsEmpty')
                    );
                }

                // check if cronjobs are installed already
                if (isset($information['data']['cronjobs'])) {
                    foreach ($information['data']['cronjobs'] as $cronjob) {
                        if (!$cronjob['active']) {
                            $information['warnings'][] = array(
                                'message' => BL::getError('CronjobsNotSet')
                            );
                        }
                        break;
                    }
                }
            } catch (Exception $e) {
                $information['warnings'][] = array(
                    'message' => BL::getMessage('InformationFileCouldNotBeLoaded')
                );
            }
        } else {
            $information['warnings'][] = array(
                'message' => BL::getMessage('InformationFileIsMissing')
            );
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
    public static function getModules()
    {
        $installedModules = (array) BackendModel::getContainer()->get('database')->getRecords(
            'SELECT name FROM modules',
            null,
            'name'
        );
        $modules = BackendModel::getModulesOnFilesystem(false);
        $manageableModules = array();

        // get more information for each module
        foreach ($modules as $moduleName) {
            if (in_array($moduleName, self::$ignoredModules)) {
                continue;
            }

            $module = array();
            $module['id'] = 'module_' . $moduleName;
            $module['raw_name'] = $moduleName;
            $module['name'] = \SpoonFilter::ucfirst(BL::getLabel(\SpoonFilter::toCamelCase($moduleName)));
            $module['description'] = '';
            $module['version'] = '';
            $module['installed'] = false;
            $module['cronjobs_active'] = true;

            if (isset($installedModules[$moduleName])) {
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

                // check if cronjobs are set
                if (isset($info['cronjobs'])) {
                    foreach ($info['cronjobs'] as $cronjob) {
                        if (!$cronjob['active']) {
                            $module['cronjobs_active'] = false;
                            break;
                        }
                    }
                }
            } catch (Exception $e) {
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
    public static function getModulesThatRequireAkismet()
    {
        $modules = array();
        $installedModules = BackendModel::getModules();

        foreach ($installedModules as $module) {
            $setting = BackendModel::getModuleSetting($module, 'requires_akismet', false);
            if ($setting) {
                $modules[] = $module;
            }
        }

        return $modules;
    }

    /**
     * Fetch the list of modules that require Google Maps API key
     *
     * @return array
     */
    public static function getModulesThatRequireGoogleMaps()
    {
        $modules = array();
        $installedModules = BackendModel::getModules();

        foreach ($installedModules as $module) {
            $setting = BackendModel::getModuleSetting($module, 'requires_google_maps', false);
            if ($setting) {
                $modules[] = $module;
            }
        }

        return $modules;
    }

    /**
     * Get a given template
     *
     * @param int $id The id of the requested template.
     * @return array
     */
    public static function getTemplate($id)
    {
        return (array) BackendModel::getContainer()->get('database')->getRecord(
            'SELECT i.* FROM themes_templates AS i WHERE i.id = ?',
            array((int) $id)
        );
    }

    /**
     * Get templates
     *
     * @param string $theme The theme we want to fetch the templates from.
     * @return array
     */
    public static function getTemplates($theme = null)
    {
        $db = BackendModel::getContainer()->get('database');
        $theme = \SpoonFilter::getValue((string) $theme, null, BackendModel::getModuleSetting('Core', 'theme', 'Core'));

        $templates = (array) $db->getRecords(
            'SELECT i.id, i.label, i.path, i.data
            FROM themes_templates AS i
            WHERE i.theme = ? AND i.active = ?
            ORDER BY i.label ASC',
            array($theme, 'Y'),
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
                    if (
                        \SpoonFilter::isInteger($value) &&
                        isset($extras[$value]) && $extras[$value]['type'] == 'block'
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
            $i++;
        }

        return (array) $templates;
    }

    /**
     * Fetch the list of available themes
     *
     * @return array
     */
    public static function getThemes()
    {
        $records = array();
        $records['Core'] = array(
            'value' => 'Core',
            'label' => BL::lbl('NoTheme'),
            'thumbnail' => '/src/Frontend/Core/Layout/images/thumbnail.png',
            'installed' => self::isThemeInstalled('Core'),
            'installable' => false,
        );

        $finder = new Finder();
        foreach ($finder->directories()->in(FRONTEND_PATH . '/Themes')->depth(0) as $directory) {
            try {
                $pathInfoXml = PATH_WWW . '/src/Frontend/Themes/' . $directory->getBasename() . '/info.xml';
                $infoXml = @new \SimpleXMLElement($pathInfoXml, LIBXML_NOCDATA, true);
                $information = self::processThemeXml($infoXml);
                if (!$information) {
                    throw new Exception('Invalid info.xml');
                }
            } catch (Exception $e) {
                $information['thumbnail'] = 'thumbnail.png';
            }

            $item = array();
            $item['value'] = $directory->getBasename();
            $item['label'] = $directory->getBasename();
            $item['thumbnail'] = '/src/Frontend/Themes/' . $item['value'] . '/' . $information['thumbnail'];
            $item['installed'] = self::isThemeInstalled($item['value']);
            $item['installable'] = isset($information['templates']);

            $records[$item['value']] = $item;
        }

        return (array) $records;
    }

    /**
     * Create template XML for export
     *
     * @param string $theme
     * @return string
     */
    public static function createTemplateXmlForExport($theme)
    {
        $xml = new \DOMDocument('1.0', SPOON_CHARSET);
        $xml->preserveWhiteSpace = false;
        $xml->formatOutput = true;

        $root = $xml->createElement('templates');
        $xml->appendChild($root);

        $db = BackendModel::getContainer()->get('database');

        $records = $db->getRecords(self::QRY_BROWSE_TEMPLATES, array($theme));

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

    /**
     * Checks if a specific module has errors or not
     *
     * @param string $module
     * @return bool
     */
    public static function hasModuleWarnings($module)
    {
        $moduleInformation = self::getModuleInformation($module);

        return (empty($moduleInformation['warnings'])) ? 'N' : 'Y';
    }

    /**
     * Inserts a new template
     *
     * @param array $template The data for the template to insert.
     * @return int
     */
    public static function insertTemplate(array $template)
    {
        return (int) BackendModel::getContainer()->get('database')->insert('themes_templates', $template);
    }

    /**
     * Install a module.
     *
     * @param string $module   The name of the module to be installed.
     * @param array  $warnings Warnings from the upload of the module.
     */
    public static function installModule($module, array $warnings = array())
    {
        $class = 'Backend\\Modules\\' . $module . '\\Installer\\Installer';
        $variables = array();

        // run installer
        $installer = new $class(
            BackendModel::getContainer()->get('database'),
            BL::getActiveLanguages(),
            array_keys(BL::getInterfaceLanguages()),
            false,
            $variables
        );

        $installer->install();
        foreach ($warnings as $warning) {
            $installer->addWarning($warning);
        }

        // save the warnings in session for later use
        if ($installer->getWarnings()) {
            $warnings = \SpoonSession::exists('installer_warnings') ? \SpoonSession::get(
                'installer_warnings'
            ) : array();
            $warnings = array_merge($warnings, array('module' => $module, 'warnings' => $installer->getWarnings()));
            \SpoonSession::set('installer_warnings', $warnings);
        }

        // clear the cache so locale (and so much more) gets rebuilt
        self::clearCache();
    }

    /**
     * Install a theme.
     *
     * @param string $theme The name of the theme to be installed.
     */
    public static function installTheme($theme)
    {
        $pathInfoXml = FRONTEND_PATH . '/Themes/' . $theme . '/info.xml';
        $infoXml = @new \SimpleXMLElement($pathInfoXml, LIBXML_NOCDATA, true);

        $information = self::processThemeXml($infoXml);
        if (!$information) {
            throw new Exception('Invalid info.xml');
        }

        foreach ($information['templates'] as $template) {
            $item = array();
            $item['theme'] = $information['name'];
            $item['label'] = $template['label'];
            $item['path'] = $template['path'];
            $item['active'] = 'Y';
            $item['data']['format'] = $template['format'];

            // build positions
            $item['data']['names'] = array();
            $item['data']['default_extras'] = array();
            foreach ($template['positions'] as $position) {
                $item['data']['names'][] = $position['name'];
                $item['data']['default_extras'][$position['name']] = array();

                // add default widgets
                foreach ($position['widgets'] as $widget) {
                    // fetch extra_id for this extra
                    $extraId = (int) BackendModel::getContainer()->get('database')->getVar(
                        'SELECT i.id
                         FROM modules_extras AS i
                         WHERE type = ? AND module = ? AND action = ? AND data IS NULL AND hidden = ?',
                        array('widget', $widget['module'], $widget['action'], 'N')
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

    /**
     * Checks if a module is already installed.
     *
     * @param string $module
     * @return bool
     */
    public static function isModuleInstalled($module)
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM modules
             WHERE name = ?
             LIMIT 1',
            (string) $module
        );
    }

    /**
     * Is the provided template id in use by active versions of pages?
     *
     * @param int $templateId The id of the template to check.
     * @return bool
     */
    public static function isTemplateInUse($templateId)
    {
        return (bool) BackendModel::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM pages AS i
             WHERE i.template_id = ? AND i.status = ?
             LIMIT 1',
            array((int) $templateId, 'active')
        );
    }

    /**
     * Checks if a theme is already installed.
     *
     * @param string $theme
     * @return bool
     */
    public static function isThemeInstalled($theme)
    {
        return (bool) BackendModeL::getContainer()->get('database')->getVar(
            'SELECT 1
             FROM themes_templates
             WHERE theme = ?
             LIMIT 1',
            array($theme)
        );
    }

    /**
     * Check if a directory is writable.
     * The default is_writable function has problems due to Windows ACLs "bug"
     *
     * @param string $path The path to check.
     * @return bool
     */
    public static function isWritable($path)
    {
        $path = rtrim((string) $path, '/');
        $file = uniqid() . '.tmp';
        $return = @file_put_contents($path . '/' . $file, 'temporary file', FILE_APPEND);
        if ($return === false) {
            return false;
        }
        unlink($path . '/' . $file);

        return true;
    }

    /**
     * Process the module's information XML and return an array with the information.
     *
     * @param \SimpleXMLElement $xml
     * @return array
     */
    public static function processModuleXml(\SimpleXMLElement $xml)
    {
        $information = array();

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
        $information['cronjobs'] = array();

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
            $item = array();
            $item['minute'] = (isset($attributes['minute'])) ? $attributes['minute'] : '*';
            $item['hour'] = (isset($attributes['hour'])) ? $attributes['hour'] : '*';
            $item['day-of-month'] = (isset($attributes['day-of-month'])) ? $attributes['day-of-month'] : '*';
            $item['month'] = (isset($attributes['month'])) ? $attributes['month'] : '*';
            $item['day-of-week'] = (isset($attributes['day-of-week'])) ? $attributes['day-of-week'] : '*';
            $item['action'] = $attributes['action'];
            $item['description'] = $cronjob[0];

            // check if cronjob has already been run
            $cronjobs = (array) BackendModel::getModuleSetting('Core', 'cronjobs');
            $item['active'] = in_array($information['name'] . '.' . $attributes['action'], $cronjobs);

            $information['cronjobs'][] = $item;
        }

        // events
        foreach ($xml->xpath('/module/events/event') as $event) {
            $attributes = $event->attributes();

            // build event information and add it to the list
            $information['events'][] = array(
                'application' => (isset($attributes['application'])) ? $attributes['application'] : '',
                'name' => (isset($attributes['name'])) ? $attributes['name'] : '',
                'description' => $event[0]
            );
        }

        return $information;
    }

    /**
     * Process the theme's information XML and return an array with the information.
     *
     * @param \SimpleXMLElement $xml
     * @return array
     */
    public static function processThemeXml(\SimpleXMLElement $xml)
    {
        $information = array();

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
        foreach ($xml->xpath('/theme/templates/template') as $templateXML) {
            $template = array();

            // template data
            $template['label'] = (string) $templateXML['label'];
            $template['path'] = (string) $templateXML['path'];
            $template['format'] = trim(str_replace(array("\n", "\r", ' '), '', (string) $templateXML->format));

            // loop positions
            foreach ($templateXML->positions->position as $positionXML) {
                $position = array();

                $position['name'] = (string) $positionXML['name'];

                // widgets
                $position['widgets'] = array();
                if ($positionXML->defaults->widget) {
                    foreach ($positionXML->defaults->widget as $widget) {
                        $position['widgets'][] = array(
                            'module' => (string) $widget['module'],
                            'action' => (string) $widget['action']
                        );
                    }
                }

                // editor
                $position['editors'] = array();
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

    /**
     * Convert the template syntax into an array to work with.
     *
     * @param string $syntax
     * @return array
     */
    public static function templateSyntaxToArray($syntax)
    {
        $syntax = (string) $syntax;
        $syntax = trim(str_replace(array("\n", "\r", ' '), '', $syntax));
        $table = array();

        // split into rows
        $rows = explode('],[', $syntax);

        foreach ($rows as $i => $row) {
            $row = trim(str_replace(array('[', ']'), '', $row));
            $table[$i] = (array) explode(',', $row);
        }

        if (!isset($table[0])) {
            return false;
        }

        $columns = count($table[0]);

        foreach ($table as $row) {
            if (count($row) != $columns) {
                return false;
            }
        }

        return $table;
    }

    /**
     * Update a template
     *
     * @param array $item The new data for the template.
     */
    public static function updateTemplate(array $item)
    {
        BackendModel::getContainer()->get('database')->update(
            'themes_templates',
            $item,
            'id = ?',
            array((int) $item['id'])
        );
    }

    /**
     * Make sure that we have an entirely valid theme information array
     *
     * @param array $information Contains the parsed theme info.xml data.
     * @return array
     */
    public static function validateThemeInformation($information)
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
                        $information['templates'][$i]['positions'][$j]['widgets'] = array();
                    }

                    // ensure editors are well-formed
                    if (!isset($position['editors']) || !$position['editors']) {
                        $information['templates'][$i]['positions'][$j]['editors'] = array();
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
                    return null;
                }
            }

            // check if there still are valid templates
            if (!isset($information['templates']) || !$information['templates']) {
                return null;
            }
        }

        return $information;
    }
}
