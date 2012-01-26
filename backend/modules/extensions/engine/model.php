<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using in the extensions module.
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Matthias Mullie <matthias@mullie.eu>
 */
class BackendExtensionsModel
{
	/**
	 * Overview of templates.
	 *
	 * @var	string
	 */
	const QRY_BROWSE_TEMPLATES = 'SELECT i.id, i.label AS title
									FROM themes_templates AS i
									WHERE i.theme = ?
									ORDER BY i.label ASC';

	/**
	 * Modules which are part of the core and can not be managed.
	 *
	 * @var	array
	 */
	private static $ignoredModules = array(
		'authentication', 'dashboard',
		'error', 'extensions', 'settings'
	);

	/**
	 * Build HTML for a template (visual representation)
	 *
	 * @param array $template The template format.
	 * @param bool[optional] $large Will the HTML be used in a large version?
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
		for($y = 0; $y < $rows; $y++)
		{
			// start row
			$html .= '		<tr>' . "\n";

			// loop cells
			for($x = 0; $x < $cells; $x++)
			{
				// skip if needed
				if(!isset($table[$y][$x])) continue;

				// get value
				$value = $table[$y][$x];

				// init var
				$colspan = 1;

				// reset items in the same collumn
				while($x + $colspan < $cells && $table[$y][$x + $colspan] === $value) $table[$y][$x + $colspan++] = null;

				// init var
				$rowspan = 1;
				$rowMatches = true;

				// loop while the rows match
				while($rowMatches && $y + $rowspan < $rows)
				{
					// loop columns inside spanned columns
					for($i = 0; $i < $colspan; $i++)
					{
						// check value
						if($table[$y + $rowspan][$x + $i] !== $value)
						{
							// no match, so stop
							$rowMatches = false;
							break;
						}
					}

					// any rowmatches?
					if($rowMatches)
					{
						// loop columns and reset value
						for($i = 0; $i < $colspan; $i++) $table[$y + $rowspan][$x + $i] = null;

						// increment
						$rowspan++;
					}
				}

				// decide state
				$exists = $value != '/';

				// set values
				$title = SpoonFilter::ucfirst($value);
				$type = '';

				// start cell
				$html .= '<td';

				// add rowspan if needed
				if($rowspan > 1) $html .= ' rowspan="' . $rowspan . '"';

				// add colspan if needed
				if($colspan > 1) $html .= ' colspan="' . $colspan . '"';

				// does the cell need content?
				if(!$exists) $html .= ' class="empty">&nbsp;</td>' . "\n";

				// the cell need a name
				else
				{
					// large visual?
					if($large)
					{
						$html .= ' id="templatePosition-' . $value . '" data-position="' . $value . '" class="box">
									<div class="heading linkedBlocksTitle"><h3>' . $title . '</h3></div>
									<div class="linkedBlocks"><!-- linked blocks will be added here --></div>
									<div class="buttonHolder buttonAddHolder">
										<a href="#addBlock" class="button icon iconAdd addBlock">
											<span>' . SpoonFilter::ucfirst(BL::lbl('AddBlock')) . '</span>
										</a>
									</div>
								</td>' . "\n";
					}

					// just regular
					else $html .= '><a href="#position-' . $value . '" title="' . $title . '">' . $title . '</a></td>' . "\n";
				}
			}

			// end row
			$html .= '		</tr>' . "\n";
		}

		// end html
		$html .= '	</tbody>' . "\n";
		$html .= '</table>' . "\n";

		// return html
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
		if(BackendAuthentication::isAllowedAction('index', 'settings'))
		{
			// check if the akismet key is available if there are modules that require it
			if(!empty($akismetModules) && BackendModel::getModuleSetting('core', 'akismet_key', null) == '')
			{
				// add warning
				$warnings[] = array('message' => sprintf(BL::err('AkismetKey'), BackendModel::createURLForAction('index', 'settings')));
			}

			// check if the google maps key is available if there are modules that require it
			if(!empty($googleMapsModules) && BackendModel::getModuleSetting('core', 'google_maps_key', null) == '')
			{
				// add warning
				$warnings[] = array('message' => sprintf(BL::err('GoogleMapsKey'), BackendModel::createURLForAction('index', 'settings')));
			}
		}

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('modules', 'extensions'))
		{
			// check if there are cronjobs that are not yet set
			$modules = BackendExtensionsModel::getModules();
			foreach($modules as $module)
			{
				if(isset($module['cronjobs_active']) && !$module['cronjobs_active'])
				{
					// add warning
					$warnings[] = array('message' => sprintf(BL::err('CronjobsNotSet', 'extensions'), BackendModel::createURLForAction('modules', 'extensions')));
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
		// list of cache files to be deleted
		$filesToDelete = array();

		// backend navigation
		$filesToDelete[] = BACKEND_CACHE_PATH . '/navigation/navigation.php';

		// backend locale
		foreach(SpoonFile::getList(BACKEND_CACHE_PATH . '/locale', '/\.php$/') as $file)
		{
			$filesToDelete[] = BACKEND_CACHE_PATH . '/locale/' . $file;
		}

		// frontend navigation
		foreach(SpoonFile::getList(FRONTEND_CACHE_PATH . '/navigation', '/\.(php|js)$/') as $file)
		{
			$filesToDelete[] = FRONTEND_CACHE_PATH . '/navigation/' . $file;
		}

		// frontend locale
		foreach(SpoonFile::getList(FRONTEND_CACHE_PATH . '/locale', '/\.php$/') as $file)
		{
			$filesToDelete[] = FRONTEND_CACHE_PATH . '/locale/' . $file;
		}

		// delete the files
		foreach($filesToDelete as $file) SpoonFile::delete($file);
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

		// get all templates
		$templates = self::getTemplates();

		// we can't delete a template that doesn't exist
		if(!isset($templates[$id])) return false;

		// we can't delete the last template
		if(count($templates) == 1) return false;

		// we can't delete the default template
		if($id == BackendModel::getModuleSetting('pages', 'default_template')) return false;
		if(BackendExtensionsModel::isTemplateInUse($id)) return false;

		// get db
		$db = BackendModel::getDB(true);

		// delete
		$db->delete('themes_templates', 'id = ?', $id);

		// get all non-active pages that use this template
		$ids = (array) $db->getColumn(
			'SELECT i.revision_id
			 FROM pages AS i
			 WHERE i.template_id = ? AND i.status != ?',
			array($id, 'active')
		);

		// any items
		if(!empty($ids))
		{
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
		$module = (string) $module;

		// check if modules directory exists
		return SpoonDirectory::exists(BACKEND_MODULES_PATH . '/' . $module);
	}

	/**
	 * Check if a template exists
	 *
	 * @param int $id The Id of the template to check for existence.
	 * @return bool
	 */
	public static function existsTemplate($id)
	{
		$id = (int) $id;

		// get data
		return (bool) BackendModel::getDB()->getVar(
			'SELECT i.id FROM themes_templates AS i	WHERE i.id = ?',
			array($id)
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
		$theme = (string) $theme;

		// check if modules directory exists
		return SpoonDirectory::exists(FRONTEND_PATH . '/themes/' . $theme) || $theme == 'core';
	}

	/**
	 * Get extras
	 *
	 * @return array
	 */
	public static function getExtras()
	{
		// get all extras
		$extras = (array) BackendModel::getDB()->getRecords(
			'SELECT i.id, i.module, i.type, i.label, i.data
			 FROM modules_extras AS i
			 INNER JOIN modules AS m ON i.module = m.name
			 WHERE i.hidden = ?
			 ORDER BY i.module, i.sequence',
			array('N'), 'id');

		// init var
		$itemsToRemove = array();

		// loop extras
		foreach($extras as $id => &$row)
		{
			// unserialize data
			$row['data'] = @unserialize($row['data']);

			// remove items that are not for the current language
			if(isset($row['data']['language']) && $row['data']['language'] != BackendLanguage::getWorkingLanguage()) $itemsToRemove[] = $id;

			// set URL if needed
			if(!isset($row['data']['url'])) $row['data']['url'] = BackendModel::createURLForAction('index', $row['module']);

			// build name
			$name = SpoonFilter::ucfirst(BL::lbl($row['label']));
			if(isset($row['data']['extra_label'])) $name = $row['data']['extra_label'];
			if(isset($row['data']['label_variables'])) $name = vsprintf($name, $row['data']['label_variables']);

			// add human readable name
			$module = SpoonFilter::ucfirst(BL::lbl(SpoonFilter::toCamelCase($row['module'])));
			$row['human_name'] = SpoonFilter::ucfirst(BL::lbl(SpoonFilter::toCamelCase('ExtraType_' . $row['type']))) . ': ' . $name;
			$row['path'] = SpoonFilter::ucfirst(BL::lbl(SpoonFilter::toCamelCase('ExtraType_' . $row['type']))) . ' › ' . $module . ($module != $name ? ' › ' . $name : '');
		}

		// any items to remove?
		if(!empty($itemsToRemove))
		{
			// loop and remove items
			foreach($itemsToRemove as $id) unset($extras[$id]);
		}

		// return extras
		return $extras;
	}

	/**
	 * Get all the available extra's
	 *
	 * @return array
	 */
	public static function getExtrasData()
	{
		// get all extras
		$extras = (array) BackendModel::getDB()->getRecords(
			'SELECT i.id, i.module, i.type, i.label, i.data
			 FROM modules_extras AS i
			 INNER JOIN modules AS m ON i.module = m.name
			 WHERE i.hidden = ?
			 ORDER BY i.module, i.sequence',
			array('N')
		);

		// build array
		$values = array();

		// init var
		$itemsToRemove = array();

		// loop extras
		foreach($extras as $id => $row)
		{
			// unserialize data
			$row['data'] = @unserialize($row['data']);

			// remove items that are not for the current language
			if(isset($row['data']['language']) && $row['data']['language'] != BackendLanguage::getWorkingLanguage()) continue;

			// set URL if needed
			if(!isset($row['data']['url'])) $row['data']['url'] = BackendModel::createURLForAction('index', $row['module']);

			// build name
			$name = SpoonFilter::ucfirst(BL::lbl($row['label']));
			if(isset($row['data']['extra_label'])) $name = $row['data']['extra_label'];
			if(isset($row['data']['label_variables'])) $name = vsprintf($name, $row['data']['label_variables']);

			// create modulename
			$moduleName = SpoonFilter::ucfirst(BL::lbl(SpoonFilter::toCamelCase($row['module'])));

			// build array
			if(!isset($values[$row['module']])) $values[$row['module']] = array('value' => $row['module'], 'name' => $moduleName, 'items' => array());

			// add real extra
			$values[$row['module']]['items'][$row['type']][$name] = array('id' => $row['id'], 'label' => $name);
		}

		// loop
		foreach($values as &$row)
		{
			if(!empty($row['items']['widget'])) $row['items']['widget'] = SpoonFilter::arraySortKeys($row['items']['widget']);
			if(!empty($row['items']['block'])) $row['items']['block'] = SpoonFilter::arraySortKeys($row['items']['block']);
		}

		return $values;
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
		// get installed modules
		$installedModules = (array) BackendModel::getDB()->getRecords('SELECT name FROM modules', null, 'name');

		// get modules present on the filesystem
		$modules = SpoonDirectory::getList(BACKEND_MODULES_PATH, false, null, '/^[a-zA-Z0-9_]+$/');

		// all modules that are managable in the backend
		$managableModules = array();

		// get more information for each module
		foreach($modules as $moduleName)
		{
			// skip ignored modules
			if(in_array($moduleName, self::$ignoredModules)) continue;

			// init module information
			$module = array();
			$module['id'] = 'module_' . $moduleName;
			$module['raw_name'] = $moduleName;
			$module['name'] = SpoonFilter::ucfirst(BL::getLabel(SpoonFilter::toCamelCase($moduleName)));
			$module['description'] = '';
			$module['version'] = '';
			$module['installed'] = false;
			$module['cronjobs_active'] = true;

			// the module is present in the database, that means its installed
			if(isset($installedModules[$moduleName])) $module['installed'] = true;

			// get extra info from the info.xml
			try
			{
				$infoXml = @new SimpleXMLElement(BACKEND_MODULES_PATH . '/' . $module['raw_name'] . '/info.xml', LIBXML_NOCDATA, true);

				// process XML to a clean array
				$info = self::processModuleXml($infoXml);

				// set fields if they were found in the XML
				if(isset($info['description'])) $module['description'] = BackendDataGridFunctions::truncate($info['description'], 80);
				if(isset($info['version'])) $module['version'] = $info['version'];

				// check if cronjobs are set
				if(isset($info['cronjobs']))
				{
					// go search whether or not all or active
					foreach($info['cronjobs'] as $cronjob)
					{
						if(!$cronjob['active'])
						{
							$module['cronjobs_active'] = false;
							break;
						}
					}
				}
			}
			catch(Exception $e)
			{
				// don't act upon error, we simply won't possess some info
			}

			// add to list of managable modules
			$managableModules[] = $module;
		}

		return $managableModules;
	}

	/**
	 * Fetch the list of modules that require Akismet API key
	 *
	 * @return array
	 */
	public static function getModulesThatRequireAkismet()
	{
		// init vars
		$modules = array();
		$installedModules = BackendModel::getModules();

		// loop modules
		foreach($installedModules as $module)
		{
			// fetch setting
			$setting = BackendModel::getModuleSetting($module, 'requires_akismet', false);

			// add to the list
			if($setting) $modules[] = $module;
		}

		// return
		return $modules;
	}

	/**
	 * Fetch the list of modules that require Google Maps API key
	 *
	 * @return array
	 */
	public static function getModulesThatRequireGoogleMaps()
	{
		// init vars
		$modules = array();
		$installedModules = BackendModel::getModules();

		// loop modules
		foreach($installedModules as $module)
		{
			// fetch setting
			$setting = BackendModel::getModuleSetting($module, 'requires_google_maps', false);

			// add to the list
			if($setting) $modules[] = $module;
		}

		// return
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
		$id = (int) $id;

		// fetch data
		return (array) BackendModel::getDB()->getRecord(
			'SELECT i.* FROM themes_templates AS i WHERE i.id = ?',
			array($id));
	}

	/**
	 * Get templates
	 *
	 * @param string[optional] $theme The theme we want to fetch the templates from.
	 * @return array
	 */
	public static function getTemplates($theme = null)
	{
		// get db
		$db = BackendModel::getDB();

		// validate input
		$theme = SpoonFilter::getValue((string) $theme, null, BackendModel::getModuleSetting('core', 'theme', 'core'));

		// get templates
		$templates = (array) $db->getRecords('SELECT i.id, i.label, i.path, i.data
												FROM themes_templates AS i
												WHERE i.theme = ? AND i.active = ?
												ORDER BY i.label ASC',
												array($theme, 'Y'), 'id');

		// get extras
		$extras = (array) self::getExtras();

		// init var
		$half = (int) ceil(count($templates) / 2);
		$i = 0;

		// loop templates to unserialize the data
		foreach($templates as $key => &$row)
		{
			// unserialize
			$row['data'] = unserialize($row['data']);
			$row['has_block'] = false;

			// reset
			if(isset($row['data']['default_extras_' . BL::getWorkingLanguage()])) $row['data']['default_extras'] = $row['data']['default_extras_' . BL::getWorkingLanguage()];

			// any extras?
			if(isset($row['data']['default_extras']))
			{
				// loop extras
				foreach($row['data']['default_extras'] as $value)
				{
					// store if the module has blocks
					if(SpoonFilter::isInteger($value) && isset($extras[$value]) && $extras[$value]['type'] == 'block') $row['has_block'] = true;
				}
			}

			// validate
			if(!isset($row['data']['format'])) throw new BackendException('Invalid template-format.');

			// build template HTML
			$row['html'] = self::buildTemplateHTML($row['data']['format']);
			$row['htmlLarge'] = self::buildTemplateHTML($row['data']['format'], true);

			// add all data as json
			$row['json'] = json_encode($row);

			// add the break-element so the templates can be split in 2 columns in the templatechooser
			if($i == $half) $row['break'] = true;

			// increment
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
		// fetch themes
		$records = (array) SpoonDirectory::getList(FRONTEND_PATH . '/themes/', false, array('.svn'));

		// loop and complete the records
		foreach($records as $key => $record)
		{
			try
			{
				// path to info.xml
				$pathInfoXml = PATH_WWW . '/frontend/themes/' . $record . '/info.xml';

				// load info.xml
				$infoXml = @new SimpleXMLElement($pathInfoXml, LIBXML_NOCDATA, true);

				// convert xml to useful array
				$information = BackendExtensionsModel::processThemeXml($infoXml);
				if(!$information) throw new BackendException('Invalid info.xml');
			}

			// no or invalid info.xml found
			catch(Exception $e)
			{
				// spoon thumbnail value
				$information['thumbnail'] = 'thumbnail.png';
			}

			// add additional values
			$records[$record]['value'] = $record;
			$records[$record]['label'] = $record;
			$records[$record]['thumbnail'] = '/frontend/themes/' . $record . '/' . $information['thumbnail'];

			// doublecheck if templates for this theme are installed already
			$records[$record]['installed'] = self::isThemeInstalled($record);
			$records[$record]['installable'] = isset($information['templates']);

			// unset the key
			unset($records[$key]);
		}

		// add core theme
		$core = array('core' => array());
		$core['core']['value'] = 'core';
		$core['core']['label'] = BL::lbl('NoTheme');
		$core['core']['thumbnail'] = '/frontend/core/layout/images/thumbnail.png';
		$core['core']['installed'] = self::isThemeInstalled('core');
		$core['core']['installable'] = false;
		$records = array_merge($core, $records);

		return (array) $records;
	}

	/**
	 * Inserts a new template
	 *
	 * @param array $template The data for the template to insert.
	 * @return int
	 */
	public static function insertTemplate(array $template)
	{
		return (int) BackendModel::getDB(true)->insert('themes_templates', $template);
	}

	/**
	 * Install a module.
	 *
	 * @param string $module The name of the module to be installed.
	 * @param array $information Warnings from the upload of the module.
	 */
	public static function installModule($module, array $warnings = array())
	{
		// we need the installer
		require_once BACKEND_CORE_PATH . '/installer/installer.php';
		require_once BACKEND_MODULES_PATH . '/' . $module . '/installer/installer.php';

		// installer class name
		$class = SpoonFilter::toCamelCase($module) . 'Installer';

		// possible variables available for the module installers
		$variables = array();

		// run installer
		$installer = new $class(
			BackendModel::getDB(true),
			BL::getActiveLanguages(),
			array_keys(BL::getInterfaceLanguages()),
			false,
			$variables
		);

		// execute installation
		$installer->install();

		// add the warnings
		foreach($warnings as $warning) $installer->addWarning($warning);

		// save the warnings in session for later use
		if($installer->getWarnings())
		{
			$warnings = SpoonSession::exists('installer_warnings') ? SpoonSession::get('installer_warnings') : array();
			$warnings = array_merge($warnings, array('module' => $module, 'warnings' => $installer->getWarnings()));
			SpoonSession::set('installer_warnings', $warnings);
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
		// set path to info.xml
		$pathInfoXml = FRONTEND_PATH . '/themes/' . $theme . '/info.xml';

		// load info.xml
		$infoXml = @new SimpleXMLElement($pathInfoXml, LIBXML_NOCDATA, true);

		// convert xml to useful array
		$information = BackendExtensionsModel::processThemeXml($infoXml);
		if(!$information) throw new BackendException('Invalid info.xml');

		// loop templates
		foreach($information['templates'] as $template)
		{
			// init var
			$item = array();

			// build array
			$item['theme'] = $information['name'];
			$item['label'] = $template['label'];
			$item['path'] = $template['path'];
			$item['active'] = 'Y';

			// set format
			$item['data']['format'] = $template['format'];

			// build positions
			$item['data']['names'] = array();
			$item['data']['default_extras'] = array();
			foreach($template['positions'] as $position)
			{
				// init position
				$item['data']['names'][] = $position['name'];
				$item['data']['default_extras'][$position['name']] = array();

				// add default widgets
				foreach($position['widgets'] as $widget)
				{
					// fetch extra_id for this extra
					$extraId = (int) BackendModel::getDB()->getVar(
						'SELECT i.id
						 FROM modules_extras AS i
						 WHERE type = ? AND module = ? AND action = ? AND data IS NULL AND hidden = ?',
						array('widget', $widget['module'], $widget['action'], 'N')
					);

					// add extra to defaults
					if($extraId) $item['data']['default_extras'][$position['name']][] = $extraId;
				}

				// add default editors
				foreach($position['editors'] as $editor)
				{
					$item['data']['default_extras'][$position['name']][] = 0;
				}
			}

			// serialize the data
			$item['data'] = serialize($item['data']);

			// insert the item
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
		return (bool) BackendModel::getDB()->getVar(
			'SELECT COUNT(name) FROM modules WHERE name = ?',
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
		return (bool) BackendModel::getDB(false)->getVar(
			'SELECT COUNT(i.template_id)
			 FROM pages AS i
			 WHERE i.template_id = ? AND i.status = ?',
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
		return (bool) BackendModeL::getDB()->getVar(
			'SELECT COUNT(id)
			 FROM themes_templates
			 WHERE theme = ?',
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
		// redefine argument
		$path = rtrim((string) $path, '/');

		// create random file
		$file = uniqid() . '.tmp';

		$return = @file_put_contents($path . '/' . $file, 'temporary file', FILE_APPEND);

		if($return === false) return false;

		// unlink the random file
		SpoonFile::delete($path . '/' . $file);

		return true;
	}

	/**
	 * Process the module's information XML and return an array with the information.
	 *
	 * @param SimpleXMLElement $xml
	 * @return array
	 */
	public static function processModuleXml(SimpleXMLElement $xml)
	{
		$information = array();

		// fetch theme node
		$module = $xml->xpath('/module');
		if(isset($module[0])) $module = $module[0];

		// fetch general module info
		$information['name'] = (string) $module->name;
		$information['version'] = (string) $module->version;
		$information['requirements'] = (array) $module->requirements;
		$information['description'] = (string) $module->description;
		$information['cronjobs'] = array();

		// authors
		foreach($xml->xpath('/module/authors/author') as $author)
		{
			$information['authors'][] = (array) $author;
		}

		// cronjobs
		foreach($xml->xpath('/module/cronjobs/cronjob') as $cronjob)
		{
			// attributes
			$attributes = $cronjob->attributes();

			// cronjob action is required
			if(!isset($attributes['action'])) continue;

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
			$cronjobs = (array) BackendModel::getModuleSetting('core', 'cronjobs');
			$item['active'] = in_array($information['name'] . '.' . $attributes['action'], $cronjobs);

			// add cronjob to list
			$information['cronjobs'][] = $item;
		}

		// events
		foreach($xml->xpath('/module/events/event') as $event)
		{
			// attributes
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
	 * @param SimpleXMLElement $xml
	 * @return array
	 */
	public static function processThemeXml(SimpleXMLElement $xml)
	{
		$information = array();

		// fetch theme node
		$theme = $xml->xpath('/theme');
		if(isset($theme[0])) $theme = $theme[0];

		// fetch general theme info
		$information['name'] = (string) $theme->name;
		$information['version'] = (string) $theme->version;
		$information['requirements'] = (array) $theme->requirements;
		$information['thumbnail'] = (string) $theme->thumbnail;
		$information['description'] = (string) $theme->description;

		// authors
		foreach($xml->xpath('/theme/authors/author') as $author)
		{
			$information['authors'][] = (array) $author;
		}

		// meta navigation
		$meta = $theme->metanavigation->attributes();
		if(isset($meta['supported']))
		{
			$information['meta'] = (string) $meta['supported'] && (string) $meta['supported'] !== 'false';
		}

		// templates
		foreach($xml->xpath('/theme/templates/template') as $templateXML)
		{
			// init var
			$template = array();

			// template data
			$template['label'] = (string) $templateXML['label'];
			$template['path'] = (string) $templateXML['path'];
			$template['format'] = trim(str_replace(array("\n", "\r", ' '), '', (string) $templateXML->format));

			// loop positions
			foreach($templateXML->positions->position as $positionXML)
			{
				// init var
				$position = array();

				// position name
				$position['name'] = (string) $positionXML['name'];

				// widgets
				$position['widgets'] = array();
				if($positionXML->defaults->widget)
				{
					foreach($positionXML->defaults->widget as $widget)
					{
						$position['widgets'][] = array('module' => (string) $widget['module'],
													'action' => (string) $widget['action']);
					}
				}

				// editor
				$position['editors'] = array();
				if($positionXML->defaults->editor)
				{
					foreach($positionXML->defaults->editor as $editor)
					{
						$position['editors'][] = (string) trim($editor);
					}
				}

				// add position
				$template['positions'][] = $position;
			}

			// add template
			$information['templates'][] = $template;
		}

		// information array
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

		// cleanup
		$syntax = trim(str_replace(array("\n", "\r", ' '), '', $syntax));

		// init var
		$table = array();

		// split into rows
		$rows = explode('],[', $syntax);

		// loop rows
		foreach($rows as $i => $row)
		{
			// cleanup
			$row = trim(str_replace(array('[',']'), '', $row));

			// build table
			$table[$i] = (array) explode(',', $row);
		}

		// no rows
		if(!isset($table[0])) return false;

		$columns = count($table[0]);

		foreach($table as $row)
		{
			if(count($row) != $columns) return false;
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
		BackendModel::getDB(true)->update('themes_templates', $item, 'id = ?', array((int) $item['id']));
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
		if(!$information['thumbnail']) $information['thumbnail'] = 'thumbnail.png';

		// check if there are templates
		if(isset($information['templates']) && $information['templates'])
		{
			// loop templates
			foreach($information['templates'] as $i => $template)
			{
				// check template data
				if(!isset($template['label']) || !$template['label'] || !isset($template['path']) || !$template['path'] || !isset($template['format']) || !$template['format'])
				{
					unset($information['templates'][$i]);
					continue;
				}

				// if there are no positions we should continue with the next item
				if(!isset($template['positions']) && $template['positions']) continue;

				// loop positions
				foreach($template['positions'] as $j => $position)
				{
					// check if position is valid
					if(!isset($position['name']) || !$position['name'])
					{
						unset($information['templates'][$i]['positions'][$j]);
						continue;
					}

					// ensure widgets are well-formed
					if(!isset($position['widgets']) || !$position['widgets'])
					{
						$information['templates'][$i]['positions'][$j]['widgets'] = array();
					}

					// ensure editors are well-formed
					if(!isset($position['editors']) || !$position['editors'])
					{
						$information['templates'][$i]['positions'][$j]['editors'] = array();
					}

					// loop widgets
					foreach($position['widgets'] as $k => $widget)
					{
						// check if widget is valid
						if(!isset($widget['module']) || !$widget['module'] || !isset($widget['action']) || !$widget['action'])
						{
							unset($information['templates'][$i]['positions'][$j]['widgets'][$k]);
							continue;
						}
					}
				}

				// check if there still are valid positions
				if(!isset($information['templates'][$i]['positions']) || !$information['templates'][$i]['positions']) return null;
			}

			// check if there still are valid templates
			if(!isset($information['templates']) || !$information['templates']) return null;
		}

		return $information;
	}
}
