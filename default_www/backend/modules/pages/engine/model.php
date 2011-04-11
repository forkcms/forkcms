<?php

/**
 * In this file we store all generic functions that we will be using in the PagesModule
 *
 * @package		backend
 * @subpackage	pages
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @author		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendPagesModel
{
	/**
	 * Overview of the recent pages
	 *
	 * @var	string
	 */
	const QRY_BROWSE_RECENT = 'SELECT i.id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.user_id
								FROM pages AS i
								WHERE i.status = ? AND i.language = ?
								ORDER BY i.edited_on DESC
								LIMIT ?';


	/**
	 * Overview of the drafts
	 *
	 * @va	string
	 */
	const QRY_DATAGRID_BROWSE_DRAFTS = 'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.user_id
										FROM pages AS i
										INNER JOIN
										(
											SELECT MAX(i.revision_id) AS revision_id
											FROM pages AS i
											WHERE i.status = ? AND i.user_id = ? AND i.language = ?
											GROUP BY i.id
										) AS p
										WHERE i.revision_id = p.revision_id';


	/**
	 * Overview of a specific page's revisions
	 *
	 * @var	string
	 */
	const QRY_BROWSE_REVISIONS = 'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.user_id
									FROM pages AS i
									WHERE i.id = ? AND i.status = ? AND i.language = ?
									ORDER BY i.edited_on DESC';

	/**
	 * Overview of a specific page's drafts
	 *
	 * @var	string
	 */
	const QRY_DATAGRID_BROWSE_SPECIFIC_DRAFTS = 'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.user_id
													FROM pages AS i
													WHERE i.id = ? AND i.status = ? AND i.language = ?
													ORDER BY i.edited_on DESC';


	/**
	 * Overview of template
	 *
	 * @var	string
	 */
	const QRY_BROWSE_TEMPLATES = 'SELECT i.id, i.label AS title
									FROM pages_templates AS i
									ORDER BY i.label ASC';


	/**
	 * Build the cache
	 *
	 * @return	void
	 * @param	string[optional] $language	The language to build the cache for, if not passes we use the working language.
	 */
	public static function buildCache($language = null)
	{
		// redefine
		$language = ($language !== null) ? (string) $language : BackendLanguage::getWorkingLanguage();

		// get tree
		$levels = self::getTree(array(0), null, 1, $language);

		// get extras
		$extras = (array) BackendModel::getDB()->getRecords('SELECT i.id, i.module, i.action
																FROM pages_extras AS i
																WHERE i.type = ?',
																array('block'), 'id');

		// get widgets
		$widgets = (array) BackendModel::getDB()->getRecords('SELECT i.id, i.module, i.action
																FROM pages_extras AS i
																WHERE i.type = ?',
																array('widget'), 'id');

		// search sitemap
		$sitemapID = null;

		foreach($widgets as $id => $row)
		{
			if($row['action'] == 'sitemap')
			{
				$sitemapID = $id;
				break;
			}
		}

		// init vars
		$keys = array();
		$navigation = array();

		// loop levels
		foreach($levels as $level => $pages)
		{
			// loop all items on this level
			foreach($pages as $pageID => $page)
			{
				// init var
				$parentID = (int) $page['parent_id'];

				// get URL for parent
				$URL = (isset($keys[$parentID])) ? $keys[$parentID] : '';

				// home is special
				if($pageID == 1) $page['url'] = '';

				// add it
				$keys[$pageID] = trim($URL . '/' . $page['url'], '/');

				// build navigation array
				$temp = array();
				$temp['page_id'] = (int) $pageID;
				$temp['url'] = $page['url'];
				$temp['full_url'] = $keys[$pageID];
				$temp['title'] = addslashes($page['title']);
				$temp['navigation_title'] = addslashes($page['navigation_title']);
				$temp['has_extra'] = (bool) ($page['has_extra'] == 'Y');
				$temp['no_follow'] = (bool) ($page['no_follow'] == 'Y');
				$temp['hidden'] = (bool) ($page['hidden'] == 'Y');
				$temp['extra_blocks'] = null;

				// any linked extra's?
				if($page['extra_ids'] !== null)
				{
					// get ids
					$ids = (array) explode(',', $page['extra_ids']);

					// loop ids
					foreach($ids as $id)
					{
						// redefine
						$id = (int) $id;

						// available in extras, so add it to the temp-array
						if(isset($extras[$id])) $temp['extra_blocks'][$id] = $extras[$id];
					}
				}

				// calculate tree-type
				$treeType = 'page';
				if($page['hidden'] == 'Y') $treeType = 'hidden';

				// homepage should have a special icon
				if($pageID == 1) $treeType = 'home';

				// 404 page should have a special icon
				elseif($pageID == 404) $treeType = 'error';

				// sitemap should have a special icon (but only the one that was added by the installer.
				elseif($pageID < 404 && substr_count($page['extra_ids'], $sitemapID) > 0)
				{
					// get extras
					$extraIDs = explode(',', $page['extra_ids']);

					// loop extras
					foreach($extraIDs as $id)
					{
						// check if this is the sitemap id
						if($id == $sitemapID)
						{
							// set type
							$treeType = 'sitemap';

							// break it
							break;
						}
					}
				}

				// add type
				$temp['tree_type'] = $treeType;

				// add it
				$navigation[$page['type']][$page['parent_id']][$pageID] = $temp;
			}
		}

		// order by URL
		asort($keys);

		// write the key-file
		$keysString = '<?php' . "\n\n";
		$keysString .= '/**' . "\n";
		$keysString .= ' * This file is generated by Fork CMS, it contains' . "\n";
		$keysString .= ' * the mapping between a pageID and the URL' . "\n";
		$keysString .= ' * ' . "\n";
		$keysString .= ' * @author	Fork CMS' . "\n";
		$keysString .= ' * @generated	' . date('Y-m-d H:i:s') . "\n";
		$keysString .= ' */' . "\n\n";
		$keysString .= '// init var' . "\n";
		$keysString .= '$keys = array();' . "\n\n";

		// loop all keys
		foreach($keys as $pageID => $URL) $keysString .= '$keys[' . $pageID . '] = \'' . $URL . '\';' . "\n";

		// end file
		$keysString .= "\n" . '?>';

		// write the file
		SpoonFile::setContent(FRONTEND_CACHE_PATH . '/navigation/keys_' . $language . '.php', $keysString);

		// write the navigation-file
		$navigationString = '<?php' . "\n\n";
		$navigationString .= '/**' . "\n";
		$navigationString .= ' * This file is generated by Fork CMS, it contains' . "\n";
		$navigationString .= ' * more information about the page-structure' . "\n";
		$navigationString .= ' * ' . "\n";
		$navigationString .= ' * @author	Fork CMS' . "\n";
		$navigationString .= ' * @generated	' . date('Y-m-d H:i:s') . "\n";
		$navigationString .= ' */' . "\n\n";
		$navigationString .= '// init var' . "\n";
		$navigationString .= '$navigation = array();' . "\n\n";

		// loop all types
		foreach($navigation as $type => $pages)
		{
			// loop all parents
			foreach($pages as $parentID => $page)
			{
				// loop all pages
				foreach($page as $pageID => $properties)
				{
					// loop properties
					foreach($properties as $key => $value)
					{
						// page_id should be an integer
						if(is_int($value)) $line = '$navigation[\'' . $type . '\'][' . $parentID . '][' . $pageID . '][\'' . $key . '\'] = ' . (int) $value . ';' . "\n";

						// booleans
						elseif(is_bool($value))
						{
							if($value) $line = '$navigation[\'' . $type . '\'][' . $parentID . '][' . $pageID . '][\'' . $key . '\'] = true;' . "\n";
							else $line = '$navigation[\'' . $type . '\'][' . $parentID . '][' . $pageID . '][\'' . $key . '\'] = false;' . "\n";
						}

						// extra_blocks should be an array
						elseif($key == 'extra_blocks')
						{
							if($value === null) $line = '$navigation[\'' . $type . '\'][' . $parentID . '][' . $pageID . '][\'' . $key . '\'] = null;' . "\n";
							else
							{
								// init var
								$extras = array();

								foreach($value as $row)
								{
									// init var
									$temp = 'array(';

									// add properties
									$temp .= '\'id\' => ' . (int) $row['id'];
									$temp .= ', \'module\' => \'' . (string) $row['module'] . '\'';

									if($row['action'] === null) $temp .= ', \'action\' => null';
									else $temp .= ', \'action\' => \'' . (string) $row['action'] . '\'';

									$temp .= ')';

									// add into extras
									$extras[] = $temp;
								}

								// set line
								$line = '$navigation[\'' . $type . '\'][' . $parentID . '][' . $pageID . '][\'' . $key . '\'] = array(' . implode(', ', $extras) . ');' . "\n";
							}
						}

						// fallback
						else $line = '$navigation[\'' . $type . '\'][' . $parentID . '][' . $pageID . '][\'' . $key . '\'] = \'' . (string) $value . '\';' . "\n";

						// add line
						$navigationString .= $line;
					}

					// end
					$navigationString .= "\n";
				}
			}
		}

		// end file
		$navigationString .= '?>';

		// write the file
		SpoonFile::setContent(FRONTEND_CACHE_PATH . '/navigation/navigation_' . $language . '.php', $navigationString);

		// write the key-file
		$tinyMCELinkListString = '/**' . "\n";
		$tinyMCELinkListString .= ' * This file is generated by Fork CMS, it contains' . "\n";
		$tinyMCELinkListString .= ' * the links that can be used by TinyMCE' . "\n";
		$tinyMCELinkListString .= ' * ' . "\n";
		$tinyMCELinkListString .= ' * @author	Fork CMS' . "\n";
		$tinyMCELinkListString .= ' * @generated	' . date('Y-m-d H:i:s') . "\n";
		$tinyMCELinkListString .= ' */' . "\n\n";
		$tinyMCELinkListString .= '// init var' . "\n";
		$tinyMCELinkListString .= 'var tinyMCELinkList = new Array(' . "\n";

		// init var
		$first = true;
		$cachedTitles = (array) BackendModel::getDB()->getPairs('SELECT i.id, i.navigation_title
																FROM pages AS i
																WHERE i.id IN(' . implode(',', array_keys($keys)) . ')');

		// loop all keys
		foreach($keys as $pageID => $URL)
		{
			// first item shouldn't have a seperator
			if(!$first) $tinyMCELinkListString .= ',' . "\n";

			if(!isset($cachedTitles[$pageID])) continue;

			// get the title
			$title = $cachedTitles[$pageID];

			// split into chunks
			$urlChunks = explode('/', $URL);
			$count = count($urlChunks);

			// subpage?
			if($count > 1)
			{
				// loop while we have more then 1 chunk
				while(count($urlChunks) > 1)
				{
					// remove last chunk of the url
					array_pop($urlChunks);

					// build the temporary URL, so we can search for an id
					$tempURL = implode('/', $urlChunks);

					// search the pageID
					$tempPageId = array_search($tempURL, $keys);

					// prepend the title
					if(!isset($cachedTitles[$tempPageId])) $title = ' > ' . $title;
					else $title = $cachedTitles[$tempPageId] . ' > ' . $title;
				}
			}

			// add
			if(SITE_MULTILANGUAGE) $tinyMCELinkListString .= '	["' . $title . '", "/' . $language . '/' . $URL . '"]';
			else $tinyMCELinkListString .= '	["' . $title . '", "/' . $URL . '"]';

			// reset
			$first = false;
		}

		// end file
		$tinyMCELinkListString .= ');' . "\n";

		// write the file
		SpoonFile::setContent(FRONTEND_CACHE_PATH . '/navigation/tinymce_link_list_' . $language . '.js', $tinyMCELinkListString);
	}


	/**
	 * Build HTML for a template (visual representation)
	 *
	 * @return	string
	 * @param	array $template			The template data.
	 * @param	bool[optional] $large	Will the HTML be used in a large version?
	 */
	private static function buildTemplateHTML($template, $large = false)
	{
		// validate
		if(!isset($template['data']['format'])) throw new BackendException('Invalid template-format.');

		// cleanup
		$table = self::templateSyntaxToArray($template['data']['format']);

		// add start html
		$html = '<table border="0" cellpadding="0" cellspacing="10">' . "\n";
		$html .= '	<tbody>' . "\n";

		// init var
		$rows = count($table);
		$cells = count($table[0]);
		$extras = self::getExtras();

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

				// decide selected state
				$exists = (isset($template['data']['names'][$value - 1]));

				// get title & index
				$title = ($exists) ? $template['data']['names'][$value - 1] : '';
				$extra = ($exists && isset($template['data']['default_extras'][$value - 1])) ? $template['data']['default_extras'][$value - 1] : '';
				$index = ($exists) ? ($value - 1) : '';

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
						$html .= ' id="templateBlock-' . $index . '">
									<h4 class="templateBlockTitle">' . $title . '</h4>
									<p><span class="helpTxt templateBlockCurrentType">&nbsp;</span></p>
									<div class="buttonHolder">
										<a href="#chooseExtra" class="button icon iconEdit iconOnly chooseExtra" data-block-id="' . $index . '">
											<span>' . ucfirst(BL::lbl('Edit')) . '</span>
										</a>
									</div>
								</td>' . "\n";
					}

					// just regular
					else $html .= '><a href="#block-' . $index . '" title="' . $title . '">' . $title . '</a></td>' . "\n";
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
	 * Creates the html for the menu
	 *
	 * @return	string
	 * @param	string[optional] $type			The type of navigation.
	 * @param	int[optional] $depth			The maximum depth to show.
	 * @param	int[optional] $parentId			The Id to start from.
	 * @param	string[optional] $html			Will hold the created HTML.
	 */
	public static function createHtml($type = 'page', $depth = 0, $parentId = 1, $html = '')
	{
		// init var
		$navigation = array();

		// require
		require_once FRONTEND_CACHE_PATH . '/navigation/navigation_' . BackendLanguage::getWorkingLanguage() . '.php';

		// check if item exists
		if(isset($navigation[$type][$depth][$parentId]))
		{
			// start html
			$html .= '<ul>' . "\n";

			// loop elements
			foreach($navigation[$type][$depth][$parentId] as $key => $aValue)
			{
				$html .= "\t<li>" . "\n";
				$html .= "\t\t" . '<a href="#">' . $aValue['navigation_title'] . '</a>' . "\n";

				// insert recursive here!
				if(isset($navigation[$type][$depth + 1][$key])) $html .= self::createHtml($type, $depth + 1, $parentId, '');

				// add html
				$html .= '</li>' . "\n";
			}

			// end html
			$html .= '</ul>' . "\n";
		}

		// return
		return $html;
	}


	/**
	 * Delete a page
	 *
	 * @return	bool
	 * @param	int $id							The id of the page to delete.
	 * @param	string[optional] $language		The language wherin the page will be deleted, if not provided we will use the working language.
	 */
	public static function delete($id, $language = null)
	{
		// redefine
		$id = (int) $id;
		$language = ($language === null) ? BackendLanguage::getWorkingLanguage() : (string) $language;

		// get db
		$db = BackendModel::getDB(true);

		// get record
		$page = self::get($id, $language);

		// validate
		if(empty($page)) return false;
		if($page['allow_delete'] == 'N') return false;

		// get revision ids
		$revisionIDs = (array) $db->getColumn('SELECT i.revision_id
												FROM pages AS i
												WHERE i.id = ? AND i.language = ?',
												array($id, $language));

		// get meta ids
		$metaIDs = (array) $db->getColumn('SELECT i.meta_id
											FROM pages AS i
											WHERE i.id = ? AND i.language = ?',
											array($id, $language));

		// delete meta records
		if(!empty($metaIDs)) $db->delete('meta', 'id IN (' . implode(',', $metaIDs) . ')');

		// delete blocks and their revisions
		if(!empty($revisionIDs)) $db->delete('pages_blocks', 'revision_id IN (' . implode(',', $revisionIDs) . ')');

		// delete page and the revisions
		if(!empty($revisionIDs)) $db->delete('pages', 'revision_id IN (' . implode(',', $revisionIDs) . ')');

		// return
		return true;
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
		if(BackendPagesModel::isTemplateInUse($id)) return false;

		// get db
		$db = BackendModel::getDB(true);

		// delete
		$db->delete('pages_templates', 'id = ?', $id);

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
	 * Check if a page exists
	 *
	 * @return	bool
	 * @param	int $id		The id to check for existence.
	 */
	public static function exists($id)
	{
		// redefine
		$id = (int) $id;
		$language = BackendLanguage::getWorkingLanguage();

		// exists?
		return (bool) BackendModel::getDB()->getVar('SELECT COUNT(i.id)
														FROM pages AS i
														WHERE i.id = ? AND i.language = ? AND i.status IN (?, ?)',
														array($id, $language, 'active', 'draft'));
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
														FROM pages_templates AS i
														WHERE i.id = ?',
														array($id));
	}


	/**
	 * Get the data for a record
	 *
	 * @return	mixed						false if the record can't be found, otherwise an array with all data.
	 * @param	int $id						The Id of the page to fetch.
	 * @param	string[optional] $language	The language to use while fetching the page.
	 */
	public static function get($id, $language = null)
	{
		// redefine
		$id = (int) $id;
		$language = ($language === null) ? BackendLanguage::getWorkingLanguage() : (string) $language;


		// get page (active version)
		$return = (array) BackendModel::getDB()->getRecord('SELECT i.*, UNIX_TIMESTAMP(i.publish_on) AS publish_on, UNIX_TIMESTAMP(i.created_on) AS created_on, UNIX_TIMESTAMP(i.edited_on) AS edited_on
															FROM pages AS i
															WHERE i.id = ? AND i.language = ? AND i.status = ?',
															array($id, $language, 'active'));

		// no page?
		if(empty($return)) return false;

		// can't be deleted
		if(in_array($return['id'], array(1, 404))) $return['allow_delete'] = 'N';

		// can't be moved
		if(in_array($return['id'], array(1, 404))) $return['allow_move'] = 'N';

		// can't have children
		if(in_array($return['id'], array(404))) $return['allow_move'] = 'N';

		// convert into bools for use in template engine
		$return['move_allowed'] = (bool) ($return['allow_move'] == 'Y');
		$return['children_allowed'] = (bool) ($return['allow_children'] == 'Y');
		$return['edit_allowed'] = (bool) ($return['allow_edit'] == 'Y');
		$return['delete_allowed'] = (bool) ($return['allow_delete'] == 'Y');

		// unserialize data
		if($return['data'] !== null) $return['data'] = unserialize($return['data']);

		// return
		return $return;
	}


	/**
	 * Get the blocks in a certain page
	 *
	 * @return	array
	 * @param	int $id						The Id of the page to get the blocks for.
	 * @param	string[optional] $language	The language to use.
	 */
	public static function getBlocks($id, $language = null)
	{
		// redefine
		$id = (int) $id;
		$language = ($language === null) ? BackendLanguage::getWorkingLanguage() : (string) $language;

		// get page (active version)
		return (array) BackendModel::getDB()->getRecords('SELECT b.*, UNIX_TIMESTAMP(b.created_on) AS created_on, UNIX_TIMESTAMP(b.edited_on) AS edited_on
															FROM pages_blocks AS b
															INNER JOIN pages AS i ON b.revision_id = i.revision_id
															WHERE i.id = ? AND i.language = ? AND i.status = ?
															ORDER BY b.id ASC',
															array($id, $language, 'active'));
	}


	/**
	 * Get revisioned blocks for a certain page
	 *
	 * @return	array
	 * @param 	int $id				The Id of the page.
	 * @param	int $revisionId		The revision to grab.
	 */
	public static function getBlocksRevision($id, $revisionId)
	{
		// redefine
		$id = (int) $id;
		$revisionId = (int) $revisionId;
		$language = BackendLanguage::getWorkingLanguage();

		// get page (active version)
		return (array) BackendModel::getDB()->getRecords('SELECT b.*, UNIX_TIMESTAMP(b.created_on) AS created_on, UNIX_TIMESTAMP(b.edited_on) AS edited_on
															FROM pages_blocks AS b
															INNER JOIN pages AS i ON b.revision_id = i.revision_id
															WHERE i.id = ? AND i.revision_id = ? AND i.language = ?
															ORDER BY b.id ASC',
															array($id, $revisionId, $language));
	}


	/**
	 * Get all items by a given tag id
	 *
	 * @return	array
	 * @param	int $tagId	The id of the tag.
	 */
	public static function getByTag($tagId)
	{
		// redefine
		$tagId = (int) $tagId;

		// get the items
		$items = (array) BackendModel::getDB()->getRecords('SELECT i.id AS url, i.title AS name, mt.module
															FROM modules_tags AS mt
															INNER JOIN tags AS t ON mt.tag_id = t.id
															INNER JOIN pages AS i ON mt.other_id = i.id
															WHERE mt.module = ? AND mt.tag_id = ? AND i.status = ?',
															array('pages', $tagId, 'active'));

		// loop items
		foreach($items as &$row) $row['url'] = BackendModel::createURLForAction('edit', 'pages', null, array('id' => $row['url']));

		// return
		return $items;
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
																FROM pages_extras AS i
																INNER JOIN modules AS m ON i.module = m.name
																WHERE m.active = ?
																ORDER BY i.module, i.sequence',
																array('Y'), 'id');

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

			// add human readable name
			$row['human_name'] = BL::lbl(SpoonFilter::toCamelCase('ExtraType_' . $row['type'])) . ': ' . $name;
			$row['message'] = sprintf(BL::msg(SpoonFilter::toCamelCase($row['type'] . '_attached'), 'pages'), $name);
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
																FROM pages_extras AS i
																INNER JOIN modules AS m ON i.module = m.name
																WHERE m.active = ?
																ORDER BY i.module, i.sequence',
																array('Y'));

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
	 * Get the first child for a given parent
	 *
	 * @return	mixed
	 * @param	int $pageId		The Id of the page to get the first child for.
	 */
	public static function getFirstChildId($pageId)
	{
		// redefine
		$pageId = (int) $pageId;

		// get child
		$childId = (int) BackendModel::getDB()->getVar('SELECT i.id
														FROM pages AS i
														WHERE i.parent_id = ? AND i.status = ? AND i.language = ?
														ORDER BY i.sequence ASC
														LIMIT 1',
														array($pageId, 'active', BL::getWorkingLanguage()));

		// return
		if($childId != 0) return $childId;

		// fallback
		return false;
	}


	/**
	 * Get the full-URL for a given menuId
	 *
	 * @return	string
	 * @param	int $id		The Id of the page to get the URL for.
	 */
	public static function getFullURL($id)
	{
		// generate the cache files if needed
		if(!SpoonFile::exists(PATH_WWW . '/frontend/cache/navigation/keys_' . BackendLanguage::getWorkingLanguage() . '.php')) self::buildCache(BL::getWorkingLanguage());

		// init var
		$keys = array();

		// require the file
		require PATH_WWW . '/frontend/cache/navigation/keys_' . BackendLanguage::getWorkingLanguage() . '.php';

		// available in generated file?
		if(isset($keys[$id])) $URL = $keys[$id];

		// parent id 0 hasn't an url
		elseif($id == 0)
		{
			// init
			$URL = '/';

			// multilanguages?
			if(SITE_MULTILANGUAGE) $URL = '/' . BackendLanguage::getWorkingLanguage();

			// return the unique URL!
			return $URL;
		}

		// not availble
		else
		{
			return false;
		}

		// if the is available in multiple languages we should add the current lang
		if(SITE_MULTILANGUAGE) $URL = '/' . BackendLanguage::getWorkingLanguage() . '/' . $URL;

		// just prepend with slash
		else $URL = '/' . $URL;

		// return the unique URL!
		return $URL;
	}


	/**
	 * Get the maximum unique id for blocks
	 *
	 * @return	int
	 */
	public static function getMaximumBlockId()
	{
		// get the maximum id
		return (int) BackendModel::getDB()->getVar('SELECT MAX(i.id)
													FROM pages_blocks AS i');
	}


	/**
	 * Get the maximum unique id for pages
	 *
	 * @return	int
	 * @param	string[optional] $language		The language to use, if not provided we will use the working language.
	 */
	public static function getMaximumPageId($language = null)
	{
		// redefine
		$language = ($language !== null) ? (string) $language : BackendLanguage::getWorkingLanguage();

		// get the maximum id
		$maximumMenuId = (int) BackendModel::getDB()->getVar('SELECT MAX(i.id)
																FROM pages AS i
																WHERE i.language = ?',
																array($language));

		// pages created by a user that isn't a god should have an id higher then 1000
		// with this hack we can easily find which pages are added by a user
		if($maximumMenuId < 1000 && !BackendAuthentication::getUser()->isGod()) return $maximumMenuId + 1000;

		// fallback
		return $maximumMenuId;
	}


	/**
	 * Get the maximum sequence inside a leaf
	 *
	 * @return	int
	 * @param	int $parentId				The Id of the parent.
	 * @param	int[optional] $language		The language to use, if not provided we will use the working language.
	 */
	public static function getMaximumSequence($parentId, $language = null)
	{
		// redefine
		$parentId = (int) $parentId;
		$language = ($language !== null) ? (string) $language : BackendLanguage::getWorkingLanguage();

		// get the maximum sequence inside a certain leaf
		return (int) BackendModel::getDB()->getVar('SELECT MAX(i.sequence)
													FROM pages AS i
													WHERE i.language = ? AND i.parent_id = ?',
													array($language, $parentId));
	}


	/**
	 * Get the revisioned data for a record
	 *
	 * @return	array
	 * @param	int $id				The Id of the page.
	 * @param	int $revisionId		The revision to grab.
	 */
	public static function getRevision($id, $revisionId)
	{
		// redefine
		$id = (int) $id;
		$revisionId = (int) $revisionId;
		$language = BackendLanguage::getWorkingLanguage();

		// get page (active version)
		$revision = (array) BackendModel::getDB()->getRecord('SELECT *, UNIX_TIMESTAMP(i.publish_on) AS publish_on, UNIX_TIMESTAMP(i.created_on) AS created_on, UNIX_TIMESTAMP(i.edited_on) AS edited_on
																FROM pages AS i
																WHERE i.id = ? AND i.revision_id = ? AND i.language = ?',
																array($id, $revisionId, $language));

		// anything found
		if(empty($revision)) return array();

		// can't be deleted
		if(in_array($revision['id'], array(1, 404))) $revision['allow_delete'] = 'N';

		// can't be moved
		if(in_array($revision['id'], array(1, 404))) $revision['allow_move'] = 'N';

		// can't have children
		if(in_array($revision['id'], array(404))) $revision['allow_move'] = 'N';

		// convert into bools for use in template engine
		$revision['move_allowed'] = (bool) ($revision['allow_move'] == 'Y');
		$revision['children_allowed'] = (bool) ($revision['allow_children'] == 'Y');
		$revision['edit_allowed'] = (bool) ($revision['allow_edit'] == 'Y');
		$revision['delete_allowed'] = (bool) ($revision['allow_delete'] == 'Y');

		// return
		return $revision;
	}


	/**
	 * Get the subtree for a root element
	 *
	 * @return	string
	 * @param	array $navigation			The navigation array.
	 * @param 	int $parentId				The id of the parent.
	 * @param	string[optional] $html		A holder for the generated HTML.
	 */
	public static function getSubtree($navigation, $parentId, $html = '')
	{
		// redefine
		$navigation = (array) $navigation;
		$parentId = (int) $parentId;
		$html = '';

		// any elements
		if(isset($navigation['page'][$parentId]) && !empty($navigation['page'][$parentId]))
		{
			// start
			$html .= '<ul>' . "\n";

			// loop pages
			foreach($navigation['page'][$parentId] as $page)
			{
				// start
				$html .= '<li id="page-' . $page['page_id'] . '" rel="' . $page['tree_type'] . '">' . "\n";

				// insert link
				$html .= '	<a href="' . BackendModel::createURLForAction('edit', null, null, array('id' => $page['page_id'])) . '"><ins>&#160;</ins>' . $page['navigation_title'] . '</a>' . "\n";

				// get childs
				$html .= self::getSubtree($navigation, $page['page_id'], $html);

				// end
				$html .= '</li>' . "\n";
			}

			// end
			$html .= '</ul>' . "\n";
		}

		// return
		return $html;
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
															FROM pages_templates AS i
															WHERE i.id = ?',
															array($id));
	}


	/**
	 * Get templates
	 *
	 * @return	array
	 */
	public static function getTemplates()
	{
		// get db
		$db = BackendModel::getDB();

		// get templates
		$templates = (array) $db->getRecords('SELECT i.id, i.label, i.path, i.num_blocks, i.data
																FROM pages_templates AS i
																WHERE i.active = ?
																ORDER BY i.label ASC',
																array('Y'), 'id');

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

			// build template HTML
			$row['html'] = self::buildTemplateHTML($row);
			$row['htmlLarge'] = self::buildTemplateHTML($row, true);

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
	 * Get all pages/level
	 *
	 * @return	array
	 * @param	array $ids					The parentIds.
	 * @param	array[optional] $data		A holder for the generated data.
	 * @param	int[optional] $level		The counter for the level.
	 * @param	string[optional] $language	The language.
	 */
	private static function getTree(array $ids, array $data = null, $level = 1, $language = null)
	{
		// redefine
		$level = (int) $level;
		$language = ($language !== null) ? (string) $language : BackendLanguage::getWorkingLanguage();

		// get data
		$data[$level] = (array) BackendModel::getDB()->getRecords('SELECT i.id, i.title, i.parent_id, i.navigation_title, i.type, i.hidden, i.has_extra, i.no_follow,
																		i.extra_ids,
																		m.url
																	FROM pages AS i
																	INNER JOIN meta AS m ON i.meta_id = m.id
																	WHERE i.parent_id IN (' . implode(', ', $ids) . ')
																	AND i.status = ? AND i.language = ?
																	ORDER BY i.sequence ASC',
																	array('active', $language), 'id');

		// get the childIDs
		$childIds = array_keys($data[$level]);

		// build array
		if(!empty($data[$level])) return self::getTree($childIds, $data, ++$level, $language);

		// cleanup
		else unset($data[$level]);

		// return
		return $data;
	}


	/**
	 * Get the tree
	 *
	 * @return	string
	 */
	public static function getTreeHTML()
	{
		// check if the cached file exists, if not we generated it
		if(!SpoonFile::exists(PATH_WWW . '/frontend/cache/navigation/navigation_' . BackendLanguage::getWorkingLanguage() . '.php')) self::buildCache(BL::getWorkingLanguage());

		// init var
		$navigation = array();

		// require the file
		require_once FRONTEND_CACHE_PATH . '/navigation/navigation_' . BackendLanguage::getWorkingLanguage() . '.php';

		// start HTML
		$html = '<h4>' . ucfirst(BL::lbl('MainNavigation')) . '</h4>' . "\n";
		$html .= '<div class="clearfix">' . "\n";
		$html .= '	<ul>' . "\n";
		$html .= '		<li id="page-1" rel="home">';

		// homepage should
		$html .= '			<a href="' . BackendModel::createURLForAction('edit', null, null, array('id' => 1)) . '"><ins>&#160;</ins>' . ucfirst(BL::lbl('Home')) . '</a>' . "\n";

		// add subpages
		$html .= self::getSubTree($navigation, 1);

		// end
		$html .= '		</li>' . "\n";
		$html .= '	</ul>' . "\n";
		$html .= '</div>' . "\n";

		// only show meta if needed
		if(BackendModel::getModuleSetting('pages', 'meta_navigation', false))
		{
			// meta pages
			$html .= '<h4>' . ucfirst(BL::lbl('Meta')) . '</h4>' . "\n";
			$html .= '<div class="clearfix">' . "\n";
			$html .= '	<ul>' . "\n";

			// are there any meta pages
			if(isset($navigation['meta'][0]) && !empty($navigation['meta'][0]))
			{
				// loop the items
				foreach($navigation['meta'][0] as $page)
				{
					// start
					$html .= '		<li id="page-' . $page['page_id'] . '" rel="' . $page['tree_type'] . '">' . "\n";

					// insert link
					$html .= '			<a href="' . BackendModel::createURLForAction('edit', null, null, array('id' => $page['page_id'])) . '"><ins>&#160;</ins>' . $page['navigation_title'] . '</a>' . "\n";

					// insert subtree
					$html .= self::getSubTree($navigation, $page['page_id']);

					// end
					$html .= '		</li>' . "\n";
				}
			}

			// end
			$html .= '	</ul>' . "\n";
			$html .= '</div>' . "\n";
		}

		// footer pages
		$html .= '<h4>' . ucfirst(BL::lbl('Footer')) . '</h4>' . "\n";

		// start
		$html .= '<div class="clearfix">' . "\n";
		$html .= '	<ul>' . "\n";

		// are there any footer pages
		if(isset($navigation['footer'][0]) && !empty($navigation['footer'][0]))
		{

			// loop the items
			foreach($navigation['footer'][0] as $page)
			{
				// start
				$html .= '		<li id="page-' . $page['page_id'] . '" rel="' . $page['tree_type'] . '">' . "\n";

				// insert link
				$html .= '			<a href="' . BackendModel::createURLForAction('edit', null, null, array('id' => $page['page_id'])) . '"><ins>&#160;</ins>' . $page['navigation_title'] . '</a>' . "\n";

				// end
				$html .= '		</li>' . "\n";
			}
		}

		// end
		$html .= '	</ul>' . "\n";
		$html .= '</div>' . "\n";

		// are there any root pages
		if(isset($navigation['root'][0]) && !empty($navigation['root'][0]))
		{
			// meta pages
			$html .= '<h4>' . ucfirst(BL::lbl('Root')) . '</h4>' . "\n";

			// start
			$html .= '<div class="clearfix">' . "\n";
			$html .= '	<ul>' . "\n";

			// loop the items
			foreach($navigation['root'][0] as $page)
			{
				// start
				$html .= '		<li id="page-' . $page['page_id'] . '" rel="' . $page['tree_type'] . '">' . "\n";

				// insert link
				$html .= '			<a href="' . BackendModel::createURLForAction('edit', null, null, array('id' => $page['page_id'])) . '"><ins>&#160;</ins>' . $page['navigation_title'] . '</a>' . "\n";

				// insert subtree
				$html .= self::getSubTree($navigation, $page['page_id']);

				// end
				$html .= '		</li>' . "\n";
			}

			// end
			$html .= '	</ul>' . "\n";
			$html .= '</div>' . "\n";
		}

		// return
		return $html;
	}


	/**
	 * Get the possible types for a block
	 *
	 * @return	array
	 */
	public static function getTypes()
	{
		return array('rich_text' => BL::lbl('Editor'),
					 'block' => BL::lbl('Module'),
					 'widget' => BL::lbl('Widget'));
	}


	/**
	 * Get an unique URL for a page
	 *
	 * @return	string
	 * @param	string $URL					The URL to base on.
	 * @param	int[optional] $id			The id to ignore.
	 * @param	int[optional] $parentId		The parent for the page to create an url for.
	 * @param	bool[optional] $isAction	Is this page an action.
	 */
	public static function getURL($URL, $id = null, $parentId = 0, $isAction = false)
	{
		// redefine
		$URL = (string) $URL;
		$parentIds = array((int) $parentId);

		// 0, 1, 2, 3, 4 are all toplevels, so we should place them on the same level
		if($parentId == 0 || $parentId == 1 || $parentId == 2 || $parentId == 3 || $parentId == 4) $parentIds = array(0, 1, 2, 3, 4);

		// get db
		$db = BackendModel::getDB();

		// no specific id
		if($id === null)
		{
			// get number of childs within this parent with the specified URL
			$number = (int) $db->getVar('SELECT COUNT(i.id)
										FROM pages AS i
										INNER JOIN meta AS m ON i.meta_id = m.id
										WHERE i.parent_id IN(' . implode(',', $parentIds) . ') AND i.status = ? AND m.url = ? AND i.language = ?',
										array('active', $URL, BL::getWorkingLanguage()));

			// no items?
			if($number != 0)
			{
				// add a number
				$URL = BackendModel::addNumber($URL);

				// recall this method, but with a new URL
				return self::getURL($URL, null, $parentId, $isAction);
			}
		}

		// one item should be ignored
		else
		{
			// get number of childs within this parent with the specified URL
			$number = (int) $db->getVar('SELECT COUNT(i.id)
										FROM pages AS i
										INNER JOIN meta AS m ON i.meta_id = m.id
										WHERE i.parent_id IN(' . implode(',', $parentIds) . ') AND i.status = ? AND m.url = ? AND i.id != ? AND i.language = ?',
										array('active', $URL, $id, BL::getWorkingLanguage()));

			// there are items so, call this method again.
			if($number != 0)
			{
				// add a number
				$URL = BackendModel::addNumber($URL);

				// recall this method, but with a new URL
				return self::getURL($URL, $id, $parentId, $isAction);
			}
		}

		// get full URL
		$fullURL = self::getFullUrl($parentId) . '/' . $URL;

		// get info about parent page
		$parentPageInfo = self::get($parentId);

		// does the parent has extra's?
		if($parentPageInfo['has_extra'] == 'Y' && !$isAction)
		{
			// set locale
			FrontendLanguage::setLocale(BackendLanguage::getWorkingLanguage());

			// get all onsite action
			$actions = FrontendLanguage::getActions();

			// if the new URL conflicts with an action we should rebuild the URL
			if(in_array($URL, $actions))
			{
				// add a number
				$URL = BackendModel::addNumber($URL);

				// recall this method, but with a new URL
				return self::getURL($URL, $id, $parentId, $isAction);
			}
		}

		// check if folder exists
		if(SpoonDirectory::exists(PATH_WWW . '/' . $fullURL))
		{
			// add a number
			$URL = BackendModel::addNumber($URL);

			// recall this method, but with a new URL
			return self::getURL($URL, $id, $parentId, $isAction);
		}

		// check if it is an appliation
		if(in_array(trim($fullURL, '/'), array_keys(ApplicationRouting::getRoutes())))
		{
			// add a number
			$URL = BackendModel::addNumber($URL);

			// recall this method, but with a new URL
			return self::getURL($URL, $id, $parentId, $isAction);
		}

		// return the unique URL!
		return $URL;
	}


	/**
	 * Insert a page
	 *
	 * @return	int
	 * @param	array $page		The data for the page.
	 */
	public static function insert(array $page)
	{
		return (int) BackendModel::getDB(true)->insert('pages', $page);
	}


	/**
	 * Insert multiple blocks at once
	 *
	 * @return	void
	 * @param	array $blocks				The blocks to insert.
	 * @param	bool[optional] $hasBlock	The blocks to insert.
	 */
	public static function insertBlocks(array $blocks, $hasBlock = false)
	{
		// get db
		$db = BackendModel::getDB(true);

		// rebuild value for has_extra
		$hasExtra = ($hasBlock) ? 'Y' : 'N';

		// init var
		$extraIds = array();

		// loop blocks to add extraIds
		foreach($blocks as $block) if($block['extra_id'] !== null) $extraIds[] = $block['extra_id'];

		// init var
		$extraIdsValue = null;
		if(!empty($extraIds)) $extraIdsValue = implode(',', $extraIds);

		// update page
		$db->update('pages', array('has_extra' => $hasExtra, 'extra_ids' => $extraIdsValue), 'revision_id = ? AND status = ?', array($blocks[0]['revision_id'], 'active'));

		// insert blocks
		$db->insert('pages_blocks', $blocks);
	}


	/**
	 * Inserts a new template
	 *
	 * @return	int
	 * @param	array $template	The data for the template to insert.
	 */
	public static function insertTemplate(array $template)
	{
		// insert
		$return = (int) BackendModel::getDB(true)->insert('pages_templates', $template);

		// update setting for maximum blocks
		self::setMaximumBlocks();

		// return
		return $return;

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
	 * Move a page
	 *
	 * @return	bool
	 * @param	int $id							The id for the page that has to be moved.
	 * @param	int $droppedOn					The id for the page where to page has been dropped on.
	 * @param	string $typeOfDrop				The type of drop, possible values are: before, after, inside.
	 * @param	string[optional] $language		The language to use, if not provided we will use the working language.
	 */
	public static function move($id, $droppedOn, $typeOfDrop, $language = null)
	{
		// redefine
		$id = (int) $id;
		$droppedOn = (int) $droppedOn;
		$typeOfDrop = SpoonFilter::getValue($typeOfDrop, array('before', 'after', 'inside'), 'inside');
		$language = ($language === null) ? BackendLanguage::getWorkingLanguage() : (string) $language;

		// get db
		$db = BackendModel::getDB(true);

		// reset type of drop for special pages
		if($droppedOn == 1) $typeOfDrop = 'inside';
		if($droppedOn == 0) $typeOfDrop = 'inside';

		// get data for pages
		$page = self::get($id, $language);
		$droppedOnPage = self::get($droppedOn, $language);

		// reset if the drop was on 0 (new meta)
		if($droppedOn == 0) $droppedOnPage = self::get(1, $language);

		// validate
		if(empty($page) || empty($droppedOnPage)) return false;

		// calculate new parent for items that should be moved inside
		if($droppedOn == 0) $newParent = 0;
		elseif($typeOfDrop == 'inside')
		{
			// check if item allows children
			if($page['allow_children'] != 'Y') return false;

			// set new parent to the dropped on page.
			$newParent = $droppedOnPage['id'];
		}

		// if the item has to be moved before or after
		else $newParent = $droppedOnPage['parent_id'];

		// decide new type
		$newType = 'page';
		if($droppedOn == 0) $newType = 'meta';
		if($droppedOnPage['type'] == 'meta')
		{
			if($newParent == 0) $newType = 'meta';
			else $newType = 'page';
		}
		if($droppedOnPage['type'] == 'footer') $newType = 'footer';
		if($droppedOnPage['type'] == 'root')
		{
			if($newParent == 0) $newType = 'root';
			else $newType = 'page';
		}

		// calculate new sequence for items that should be moved inside
		if($typeOfDrop == 'inside')
		{
			// get highest sequence + 1
			$newSequence = (int) $db->getVar('SELECT MAX(i.sequence)
												FROM pages AS i
												WHERE i.id = ? AND i.language = ? AND i.status = ?',
												array($newParent, $language, 'active')) + 1;

			// update
			$db->update('pages', array('parent_id' => $newParent, 'sequence' => $newSequence, 'type' => $newType), 'id = ? AND language = ? AND status = ?', array($id, $language, 'active'));
		}

		// calculate new sequence for items that should be moved before
		elseif($typeOfDrop == 'before')
		{
			// get new sequence
			$newSequence = (int) $db->getVar('SELECT i.sequence
												FROM pages AS i
												WHERE i.id = ? AND i.language = ? AND i.status = ?
												LIMIT 1',
												array($droppedOnPage['id'], $language, 'active')) - 1;

			// increment all pages with a sequence that is higher or equal to the current sequence;
			$db->execute('UPDATE pages
							SET sequence = sequence + 1
							WHERE parent_id = ? AND language = ? AND sequence >= ?',
							array($newParent, $language, $newSequence + 1));

			// update
			$db->update('pages', array('parent_id' => $newParent, 'sequence' => $newSequence, 'type' => $newType), 'id = ? AND language = ? AND status = ?', array($id, $language, 'active'));
		}

		// calculate new sequence for items that should be moved after
		elseif($typeOfDrop == 'after')
		{
			// get new sequence
			$newSequence = (int) $db->getVar('SELECT i.sequence
												FROM pages AS i
												WHERE i.id = ? AND i.language = ? AND i.status = ?
												LIMIT 1',
												array($droppedOnPage['id'], $language, 'active')) + 1;

			// increment all pages with a sequence that is higher then the current sequence;
			$db->execute('UPDATE pages
							SET sequence = sequence + 1
							WHERE parent_id = ? AND language = ? AND sequence > ?',
							array($newParent, $language, $newSequence));

			// update
			$db->update('pages', array('parent_id' => $newParent, 'sequence' => $newSequence, 'type' => $newType), 'id = ? AND language = ? AND status = ?', array($id, $language, 'active'));
		}

		// fallback
		else return false;

		// get current URL
		$currentURL = (string) $db->getVar('SELECT url
											FROM meta AS m
											WHERE m.id = ?',
											array($page['meta_id']));

		// rebuild url
		$newURL = self::getURL($currentURL, $id, $newParent, (isset($page['data']['is_action']) && $page['data']['is_action'] == 'Y'));

		// store
		$db->update('meta', array('url' => $newURL), 'id = ?', array($page['meta_id']));

		// return
		return true;
	}


	/**
	 * Calculate the maximum number of blocks for all active templates and store into a module-settings
	 *
	 * @return	void
	 */
	private static function setMaximumBlocks()
	{
		// get maximum number of blocks for active templates
		$maximumNumberOfBlocks = (int) BackendModel::getDB()->getVar('SELECT MAX(i.num_blocks) AS max_num_blocks
																		FROM pages_templates AS i
																		WHERE i.active = ?',
																		array('Y'));

		// store
		BackendModel::setModuleSetting('pages', 'template_max_blocks', $maximumNumberOfBlocks);
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
	 * Update a page
	 *
	 * @return	int
	 * @param	array $page		The new data for the page.
	 */
	public static function update(array $page)
	{
		// get db
		$db = BackendModel::getDB(true);

		// update old revisions
		if($page['status'] != 'draft') $db->update('pages', array('status' => 'archive'), 'id = ? AND language = ?', array((int) $page['id'], BL::getWorkingLanguage()));
		else $db->delete('pages', 'id = ? AND user_id = ? AND status = ? AND language = ?', array((int) $page['id'], BackendAuthentication::getUser()->getUserId(), 'draft', BL::getWorkingLanguage()));

		// insert
		$page['revision_id'] = (int) $db->insert('pages', $page);

		// how many revisions should we keep
		$rowsToKeep = (int) BackendModel::getModuleSetting('pages', 'max_num_revisions', 20);

		// get revision-ids for items to keep
		$revisionIdsToKeep = (array) $db->getColumn('SELECT i.revision_id
														FROM pages AS i
														WHERE i.id = ? AND i.status = ?
														ORDER BY i.edited_on DESC
														LIMIT ?',
														array((int) $page['id'], 'archive', $rowsToKeep));

		// delete other revisions
		if(!empty($revisionIdsToKeep))
		{
			// because blocks are linked by revision we should get all revisions we want to delete
			$revisionsToDelete = (array) $db->getColumn('SELECT i.revision_id
															FROM pages AS i
															WHERE i.id = ? AND i.status = ? AND i.revision_id NOT IN(' . implode(', ', $revisionIdsToKeep) . ')',
															array((int) $page['id'], 'archive'));

			// any revisions to delete
			if(!empty($revisionsToDelete))
			{
				$db->delete('pages', 'revision_id IN(' . implode(', ', $revisionsToDelete) . ')');
				$db->delete('pages_blocks', 'revision_id IN(' . implode(', ', $revisionsToDelete) . ')');
			}
		}

		// return the new revision id
		return $page['revision_id'];
	}


	/**
	 * Update the blocks
	 *
	 * @return	void
	 * @param	array $blocks				The blocks to update.
	 * @param	bool[optional] $hasBlock	Is there a real block inside the blocks.
	 */
	public static function updateBlocks(array $blocks, $hasBlock = false)
	{
		// get db
		$db = BackendModel::getDB(true);

		// rebuild value for has_extra
		$hasExtra = ($hasBlock) ? 'Y' : 'N';

		// init var
		$extraIds = array();

		// loop blocks to add extraIds
		foreach($blocks as $block) if($block['extra_id'] !== null) $extraIds[] = $block['extra_id'];

		// init var
		$extraIdsValue = null;
		if(!empty($extraIds)) $extraIdsValue = implode(',', $extraIds);

		// update page
		$db->update('pages', array('has_extra' => $hasExtra, 'extra_ids' => $extraIdsValue), 'revision_id = ? AND status = ?', array($blocks[0]['revision_id'], 'active'));

		// update old revisions
		$db->update('pages_blocks', array('status' => 'archive'), 'revision_id = ?', array($blocks[0]['revision_id']));

		// insert
		$db->insert('pages_blocks', $blocks);
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
		$updated = BackendModel::getDB(true)->update('pages_templates', $item, 'id = ?', array((int) $item['id']));

		// update setting for maximum blocks
		self::setMaximumBlocks();

		// return updated
		return $updated;
	}
}

?>