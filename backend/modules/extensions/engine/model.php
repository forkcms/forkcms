<?php

/**
 * In this file we store all generic functions that we will be using in the extensions module.
 *
 * @package		backend
 * @subpackage	extensions
 *
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		3.0.0
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
	 * @return	string
	 * @param	array $template			The template format.
	 * @param	bool[optional] $large	Will the HTML be used in a large version?
	 */
	public static function buildTemplateHTML($format, $large = false)
	{
		// cleanup
		$table = self::templateSyntaxToArray($format);

		// add start html
		$html = '<table border="0" cellpadding="0" cellspacing="10">' . "\n";
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
				$title = ucfirst($value);
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
											<span>' . ucfirst(BL::lbl('AddBlock')) . '</span>
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
	 * Clear all applications cache.
	 *
	 * Note: we do not need to rebuild anything, the core will do this when noticing the cache files are missing.
	 *
	 * @return	void
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
	 * Delete a template
	 *
	 * @return	bool
	 * @param	int $id		The id of the template to delete.
	 */
	public static function deleteTemplate($id)
	{
		// redefine
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
		$ids = (array) $db->getColumn('SELECT i.revision_id
										FROM pages AS i
										WHERE i.template_id = ? AND i.status != ?',
										array($id, 'active'));

		// any items
		if(!empty($ids))
		{
			// delete those pages and the linked blocks
			$db->delete('pages', 'revision_id IN(' . implode(',', $ids) . ')');
			$db->delete('pages_blocks', 'revision_id IN(' . implode(',', $ids) . ')');
		}

		// return
		return true;
	}


	/**
	 * Does this module exist.
	 * This does not check for existence in the database but on the filesystem.
	 *
	 * @return	bool
	 * @param	string $module		Module to check for existence.
	 */
	public static function existsModule($module)
	{
		// recast
		$module = (string) $module;

		// check if modules directory exists
		return SpoonDirectory::exists(BACKEND_MODULES_PATH . '/' . $module);
	}


	/**
	 * Check if a template exists
	 *
	 * @return	bool
	 * @param	int $id		The Id of the template to check for existence.
	 */
	public static function existsTemplate($id)
	{
		// redefine
		$id = (int) $id;

		// get data
		return (bool) BackendModel::getDB()->getVar('SELECT i.id
														FROM themes_templates AS i
														WHERE i.id = ?',
														array($id));
	}


	/**
	 * Get extras
	 *
	 * @return	array
	 */
	public static function getExtras()
	{
		// get all extras
		$extras = (array) BackendModel::getDB()->getRecords('SELECT i.id, i.module, i.type, i.label, i.data
																FROM modules_extras AS i
																INNER JOIN modules AS m ON i.module = m.name
																WHERE m.active = ? AND i.hidden = ?
																ORDER BY i.module, i.sequence',
																array('Y', 'N'), 'id');

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
			$name = ucfirst(BL::lbl($row['label']));
			if(isset($row['data']['extra_label'])) $name = $row['data']['extra_label'];
			if(isset($row['data']['label_variables'])) $name = vsprintf($name, $row['data']['label_variables']);

			// add human readable name
			$module = ucfirst(BL::lbl(SpoonFilter::toCamelCase($row['module'])));
			$row['human_name'] = ucfirst(BL::lbl(SpoonFilter::toCamelCase('ExtraType_' . $row['type']))) . ': ' . $name;
			$row['path'] = ucfirst(BL::lbl(SpoonFilter::toCamelCase('ExtraType_' . $row['type']))) . ' › ' . $module . ($module != $name ? ' › ' . $name : '');
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
	 * @return	array
	 */
	public static function getExtrasData()
	{
		// get all extras
		$extras = (array) BackendModel::getDB()->getRecords('SELECT i.id, i.module, i.type, i.label, i.data
																FROM modules_extras AS i
																INNER JOIN modules AS m ON i.module = m.name
																WHERE m.active = ? AND i.hidden = ?
																ORDER BY i.module, i.sequence',
																array('Y', 'N'));

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
			$name = ucfirst(BL::lbl($row['label']));
			if(isset($row['data']['extra_label'])) $name = $row['data']['extra_label'];
			if(isset($row['data']['label_variables'])) $name = vsprintf($name, $row['data']['label_variables']);

			// create modulename
			$moduleName = ucfirst(BL::lbl(SpoonFilter::toCamelCase($row['module'])));

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

		// return
		return $values;
	}


	/**
	 * Get modules based on the directory listing in the backend application.
	 *
	 * If a module contains a info.xml it will be parsed.
	 *
	 * @return	array
	 */
	public static function getModules()
	{
		// get installed modules
		$installedModules = (array) BackendModel::getDB()->getRecords('SELECT name, active FROM modules', null, 'name');

		// get modules present on the filesystem
		$modules = SpoonDirectory::getList(BACKEND_MODULES_PATH);

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
			$module['name'] = ucfirst(BL::getLabel(SpoonFilter::toCamelCase($moduleName)));
			$module['description'] = '';
			$module['version'] = '';
			$module['active'] = false;
			$module['installed'] = false;

			// the module is present in the database, that means its installed
			if(isset($installedModules[$moduleName]))
			{
				$module['installed'] = true;
				$module['active'] = ($installedModules[$moduleName]['active'] == 'Y');
			}

			// get extra info from the info.xml
			$infoXml = @simplexml_load_file(BACKEND_MODULES_PATH . '/' . $module['raw_name'] . '/info.xml', null, LIBXML_NOCDATA);

			// we need a valid XML
			if($infoXml !== false)
			{
				// process XML to a clean array
				$info = self::processModuleXml($infoXml);

				// set fields if they were found in the XML
				if(isset($info['description'])) $module['description'] = BackendDataGridFunctions::truncate($info['description'], 80);
				if(isset($info['version'])) $module['version'] = $info['version'];
			}

			// add to list of managable modules
			$managableModules[] = $module;
		}

		// managable modules
		return $managableModules;
	}


	/**
	 * Get a given template
	 *
	 * @return	array
	 * @param	int $id		The id of the requested template.
	 */
	public static function getTemplate($id)
	{
		// redefine
		$id = (int) $id;

		// fetch data
		return (array) BackendModel::getDB()->getRecord('SELECT i.*
															FROM themes_templates AS i
															WHERE i.id = ?',
															array($id));
	}


	/**
	 * Get templates
	 *
	 * @return	array
	 * @param	string[optional] $theme		The thele we want to fetch the templates from.
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

		// return
		return (array) $templates;
	}


	/**
	 * Fetch the list of available themes
	 *
	 * @return	array
	 */
	public static function getThemes()
	{
		// fetch themes
		$records = (array) SpoonDirectory::getList(FRONTEND_PATH . '/themes/', false, array('.svn'));

		// loop and complete the records
		foreach($records as $key => $record)
		{
			// path to info.xml
			$pathInfoXml = PATH_WWW . '/frontend/themes/' . $record . '/info.xml';

			// load info.xml
			$infoXml = @simplexml_load_file($pathInfoXml, null, LIBXML_NOCDATA);

			// valid XML
			if($infoXml !== false)
			{
				// convert xml to useful array
				$information = BackendExtensionsModel::processThemeXml($infoXml);
			}

			// invalid xml or not found, ignore theme
			else
			{
				unset($records[$key]);
				continue;
			}

			// add additional values
			$records[$record]['value'] = $record;
			$records[$record]['label'] = $record;
			$records[$record]['thumbnail'] = '/frontend/themes/' . $record . '/' . $information['thumbnail'];

			// unset the key
			unset($records[$key]);
		}

		// add core theme
		$core = array('core' => array());
		$core['core']['value'] = 'core';
		$core['core']['label'] = BL::lbl('NoTheme');
		$core['core']['thumbnail'] = '/frontend/core/layout/images/thumbnail.png';
		$records = array_merge($core, $records);

		// return the records
		return (array) $records;
	}


	/**
	 * Install a module.
	 *
	 * @param	string $module
	 */
	public static function installModule($module)
	{
		// we need the installer
		require_once BACKEND_CORE_PATH . '/installer/installer.php';
		require_once BACKEND_MODULES_PATH . '/' . $module . '/installer/installer.php';

		// installer class name
		$class = SpoonFilter::toCamelCase($module) . 'Installer';

		// possible variables available for the module installers
		$variables = array();

		// init installer
		$installer = new $class(
			BackendModel::getDB(true),
			BL::getActiveLanguages(),
			array_keys(BL::getInterfaceLanguages()),
			false,
			$variables
		);

		// execute installation
		$installer->install();

		// clear the cache so locale (and so much more) gets rebuilded
		self::clearCache();
	}


	/**
	 * Checks if a module is already installed.
	 *
	 * @return	bool
	 * @param	string $module
	 */
	public static function isModuleInstalled($module)
	{
		return (bool) BackendModel::getDB()->getVar('SELECT COUNT(name) FROM modules WHERE name = ?', (string) $module);
	}


	/**
	 * Is the provided template id in use by active versions of pages?
	 *
	 * @return	bool
	 * @param	int $templateId		The id of the template to check.
	 */
	public static function isTemplateInUse($templateId)
	{
		return (bool) BackendModel::getDB(false)->getVar('SELECT COUNT(i.template_id)
															FROM pages AS i
															WHERE i.template_id = ? AND i.status = ?',
															array((int) $templateId, 'active'));
	}


	/**
	 * Process the module's information XML and return an array with the information.
	 *
	 * @return	array
	 * @param	SimpleXMLElement $xml
	 */
	public static function processModuleXml(SimpleXMLElement $xml)
	{
		// init
		$information = array();

		// version
		$version = $xml->xpath('/module/version');
		if(isset($version[0])) $information['version'] = (string) $version[0];

		// description
		$description = $xml->xpath('/module/description');
		if(isset($description[0])) $information['description'] = (string) $description[0];

		// authors
		foreach($xml->xpath('/module/authors/author') as $author)
		{
			$information['authors'][] = (array) $author;
		}

		// events
		foreach($xml->xpath('/module/events/event') as $event)
		{
			// lose the simplexmlelement
			$event = (array) $event;

			// attributes
			$attributes = $event->attributes();

			// build event information and add it to the list
			$information['events'][] = array(
				'application' => (isset($attributes['application'])) ? $attributes['application'] : '',
				'name' => (isset($attributes['name'])) ? $attributes['name'] : '',
				'description' => $event[0]
			);
		}

		// information array
		return $information;
	}


	/**
	 * Process the theme's information XML and return an array with the information.
	 *
	 * @return	array
	 * @param	SimpleXMLElement $xml
	 */
	public static function processThemeXml(SimpleXMLElement $xml)
	{
		// init
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
						$position['editors'][] = (string) $editor;
					}
				}

				// add position
				$template['positions'][] = $position;
			}

			// add template
			$information['templates'][] = $template;
		}

		// information array
		return $information;
	}


	/**
	 * Convert the template syntax into an array to work with
	 *
	 * @return	array
	 * @param	string $syntax	The syntax.
	 */
	public static function templateSyntaxToArray($syntax)
	{
		// redefine
		$syntax = (string) $syntax;

		// cleanup
		$syntax = trim(str_replace(array("\n", "\r"), '', $syntax));

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

		// return
		return $table;
	}


	/**
	 * Update a template
	 *
	 * @return	void
	 * @param	array $item			The new data for the template.
	 */
	public static function updateTemplate(array $item)
	{
		// update item
		return BackendModel::getDB(true)->update('themes_templates', $item, 'id = ?', array((int) $item['id']));
	}
}

?>
