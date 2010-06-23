<?php

/**
 * BackendNavigation
 * This class will be used to build the navigation
 *
 * @package		backend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendNavigation
{
	/**
	 * The navigation array, will be used to build the navigation
	 *
	 * @var	array
	 */
	private $navigation = array();


	/**
	 * URL-instance
	 *
	 * @var	BackendURL
	 */
	private $URL;


	/**
	 * Default constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		// store in reference so we can access it from everywhere
		Spoon::setObjectReference('navigation', $this);

		// grab from the reference
		$this->URL = Spoon::getObjectReference('url');

		// init var
		$navigation = array();

		// require navigation-file
		require_once BACKEND_CACHE_PATH .'/navigation/navigation.php';

		// load it
		$this->navigation = (array) $navigation;
	}


	/**
	 * Get the HTML for the navigation
	 *
	 * @return	string
	 */
	public function getNavigation()
	{
		// get selected keys, we need them for the selected state
		$selectedKeys = (array) $this->getSelectedKeys();

		// nothing found: no sidemenu
		if(!isset($selectedKeys[0]) || !isset($this->navigation[$selectedKeys[0]]['children'])) return;

		// init html
		$html = '<ul>'."\n";

		// set active URL
		$activeModule = $this->URL->getModule();
		$activeAction = $this->URL->getAction();
		$activeURL = $activeModule .'/'. $activeAction;

		// search parent
		foreach($this->navigation[$selectedKeys[0]]['children'] as $key => $level)
		{
			// undefined URL?
			if($level['url'] === null && isset($level['children']))
			{
				// loop childs till we find a valid URL
				foreach($level['children'] as $child)
				{
					// break URL into parts
					$chunks = (array) explode('/', $child['url']);

					// can't get here!
					if(!isset($chunks[1])) throw new BackendException('invalid child');

					// check if the action is allowed
					if(BackendAuthentication::isAllowedAction($chunks[1], $chunks[0]))
					{
						// reset link
						$level['url'] = $child['url'];

						// stop searching for an URL
						break;
					}
				}
			}

			// no valid URL, piss off
			if($level['url'] === null) continue;

			// break URL into parts
			$chunks = (array) explode('/', $level['url']);

			// set first chunk
			if(!isset($chunks[1])) $chunks[1] = '';

			// is allowed?
			if(BackendAuthentication::isAllowedAction($chunks[1], $chunks[0]))
			{
				// selected state
				$selected = (bool) ($level['url'] == $activeURL || (isset($selectedKeys[1]) && $key == $selectedKeys[1]));

				// open li-tag
				if($selected) $html .= '<li class="selected">'."\n";
				else $html .= '<li>'."\n";

				// add the link
				$html .= '<a href="/'. NAMED_APPLICATION .'/'. BackendLanguage::getWorkingLanguage() .'/'. $level['url'] .'">'. ucfirst(BL::getLabel($level['label'], 'core')) .'</a>'."\n";

				// should we go deeper?
				if($selected && isset($level['children']))
				{
					// create list
					$subHTML = '<ul>'."\n";

					// loop childs
					foreach($level['children'] as $child)
					{
						// break URL into parts
						$chunks = (array) explode('/', $child['url']);

						// set first chunk
						if(!isset($chunks[1])) $chunks[1] = '';

						// is allowed?
						if(BackendAuthentication::isAllowedAction($chunks[1], $chunks[0]))
						{
							// selected state - does the child URL match 'module/action' or is the action present in the selected_for_actions array?
							$childSelected = (bool) ($child['url'] == $activeURL) || (isset($child['selected_for_actions']) && in_array($activeAction, $child['selected_for_actions']));

							if($childSelected) $subHTML .= '<li class="selected">'."\n";
							else $subHTML .= '<li>'."\n";

							// add the link
							$subHTML .= '<a href="/'. NAMED_APPLICATION .'/'. BackendLanguage::getWorkingLanguage() .'/'. $child['url'] .'">'. ucfirst(BL::getLabel($child['label'], 'core')) .'</a>'."\n";

							// end li
							$subHTML .='</li>'."\n";
						}
					}

					// close list
					$subHTML .= '</ul>' . "\n";

					// add to html
					$html .= $subHTML;
				}

				// end li
				$html .= '</li>'."\n";
			}
		}

		// end html
		$html .= '</ul>'."\n";

		// return the generated html
		return $html;
	}


	/**
	 * Get the HTML for the navigation
	 *
	 * @return	string
	 */
	public function getMainNavigation()
	{
		// some modules shouldn't be showed in the main-navigation
		$modulesToIgnore = array('settings');

		// get selected keys, we need them for the selected state
		$selectedKeys = (array) $this->getSelectedKeys();

		// init html
		$html = '<ul>'."\n";

		// set active URL
		$activeModule = $this->URL->getModule();
		$activeAction = $this->URL->getAction();
		$activeURL = $activeModule .'/'. $activeAction;

		// build and return the HTML
		foreach($this->navigation as $key => $level)
		{
			// ignore some modules
			if(in_array($key, $modulesToIgnore)) continue;

			if($key == 'dashboard' || $key == 'pages')
			{
				// break URL into parts
				$chunks = (array) explode('/', $level['url']);

				// set first chunk
				if(!isset($chunks[1])) $chunks[1] = '';

				// is allowed?
				if(BackendAuthentication::isAllowedAction($chunks[1], $chunks[0]))
				{
					// open li-tag
					if(in_array($key, $selectedKeys, true)) $html .= '<li class="selected">';
					elseif(($chunks[0] == $activeModule && isset($level['selected_for_actions']) && in_array($activeAction, $level['selected_for_actions']))) $html .= '<li class="selected">';
					elseif($level['url'] == $activeURL) $html .= '<li class="selected">';
					else $html .= '<li>';

					// add the link
					$html .= '<a href="/'. NAMED_APPLICATION .'/'. BackendLanguage::getWorkingLanguage() .'/'. $level['url'] .'">'. ucfirst(BL::getLabel($level['label'], 'core')) .'</a>';

					// end li
					$html .='</li>'."\n";
				}

				// if the active menu is 'settings', assign the option to set the selected state
				if(in_array('settings', $selectedKeys, true)) Spoon::getObjectReference('template')->assign('oSettingsSelected', true);
			}

			// modules is a special one
			else
			{
				if(!isset($level['children'])) continue;

				// loop the childs
				foreach($level['children'] as $child)
				{
					// no URL provided?
					if($child['url'] === null && isset($child['children']))
					{
						// loop all childs till we find a valid one
						foreach($child['children'] as $subChild)
						{
							// break URLs into parts
							$chunks = (array) explode('/', $subChild['url']);

							// set first chunk
							if(!isset($chunks[1])) $chunks[1] = '';

							// is allowed?
							if(BackendAuthentication::isAllowedAction($chunks[1], $chunks[0]))
							{
								// reset the child URL
								$child['url'] = $subChild['url'];

								// stop after we found the first one
								break;
							}
						}
					}

					if($child['url'] == '') continue;

					// break URL into parts
					$chunks = (array) explode('/', $child['url']);

					// set first chunk
					if(!isset($chunks[1])) $chunks[1] = '';

					// is allowed?
					if(BackendAuthentication::isAllowedAction($chunks[1], $chunks[0]))
					{
						// open li-tag
						if(in_array($key, $selectedKeys, true) || $child['url'] == $activeURL)
						{
							// settings is in the selected keys stack and the active submenu is modules, so we don't select this list item
							if(in_array('settings', $selectedKeys, true) && $key == 'modules') $html .= '<li>';

							// this item is selected
							else $html .= '<li class="selected">';
						}
						else $html .= '<li>';

						// add the link
						$html .= '<a href="/'. NAMED_APPLICATION .'/'. BackendLanguage::getWorkingLanguage() .'/'. $child['url'] .'">'. $level['label'] .'</a>';

						// end li
						$html .='</li>'."\n";

						// stop
						break;
					}
				}
			}
		}

		// end ul
		$html .= '</ul>';

		// return
		return $html;
	}


	/**
	 * Get the selected keys based on the current module/actions
	 *
	 * @return	array
	 */
	private function getSelectedKeys()
	{
		// init var
		$keys = array();
		$actions = array();

		// build the URL to search for
		$activeModule = $this->URL->getModule();
		$activeAction = $this->URL->getAction();
		$activeURL = $activeModule .'/'. $activeAction;

		// build an array so we can find out what submenu is used
		foreach($this->navigation as $key => $level)
		{
			if(isset($level['children']))
			{
				foreach($level['children'] as $module => $child)
				{
					if(isset($child['children']))
					{
						foreach($child['children'] as $index => $grandchild)
						{
							if(!empty($grandchild['url']))
							{
								// split the URL so we know the module/action
								$explodedURL = explode('/', $grandchild['url']);

								// store the submenu for this module and action
								$actions[$explodedURL[0]]['actions'][$explodedURL[1]] = $key;
							}
						}
					}
				}
			}
		}

		// loop the first level
		foreach($this->navigation as $key => $level)
		{
			// URL already known?
			if($level['url'] == $activeURL) $keys[] = $key;

			// has this level any children?
			if(isset($level['children']))
			{
				// loop second level
				foreach($level['children'] as $module => $child)
				{
					// init var
					$selected = false;

					// url and selected_for_actions are available?
					if($child['url'] != '' && isset($child['selected_for_actions']))
					{
						// split url into chunks
						$chunks = (array) explode('/', $child['url']);

						// validate the chunks, and check if the module from the URL is the same as the active one
						if(isset($chunks[0]) && $chunks[0] == $activeModule)
						{
							// loop actions wherefor this item should be selected
							foreach((array) $child['selected_for_actions'] as $action)
							{
								// is the current URL is the same as the one for the sub-action?
								if($activeURL == $activeModule .'/'. $action)
								{
									// this items should be selected
									$selected = true;

									// stop looking, we found it
									break;
								}
							}
						}
					}

					// add all keys if the URL is found
					if($child['url'] == $activeURL || $module == $activeModule || $selected)
					{
						// if the action is a part of the submenu 'settings', we need to store the settings/modules keys
						if(isset($actions[$activeModule]['actions'][$activeAction]) && $actions[$activeModule]['actions'][$activeAction] == 'settings')
						{
							$keys[] = 'settings';
							$keys[] = 'modules';
						}

						else
						{
							$keys[] = $key;
							$keys[] = $module;
						}
					}

					// has children?
					if(isset($child['children']))
					{
						// loop third level
						foreach($child['children'] as $subChild)
						{
							// URL found?
							if($subChild['url'] == $activeURL || $module == $activeModule)
							{
								// if the action is a part of the submenu 'settings', we need to store the settings/modules keys
								if(isset($actions[$activeModule]['actions'][$activeAction]) && $actions[$activeModule]['actions'][$activeAction] == 'settings')
								{
									$keys[] = 'settings';
									$keys[] = 'modules';
								}

								else
								{
									$keys[] = $key;
									$keys[] = $module;
								}
							}
						}
					}
				}
			}
		}

		// return the selected keys
		return $keys;
	}
}

?>