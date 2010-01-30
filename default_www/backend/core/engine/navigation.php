<?php

/**
 * BackendNavigation
 *
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
	 * Url-instance
	 *
	 * @var	BackendURL
	 */
	private $url;


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
		$this->url = Spoon::getObjectReference('url');

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
	 * @param	int $startDepth
	 * @param	int[optional] $maximumDepth
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
		$activeModule = $this->url->getModule();
		$activeAction = $this->url->getAction();
		$activeURL = $activeModule .'/'. $activeAction;

		// search parent
		foreach($this->navigation[$selectedKeys[0]]['children'] as $key => $level)
		{
			// undefined url?
			if($level['url'] === null && isset($level['children']))
			{
				// loop childs till we find a valid url
				foreach($level['children'] as $child)
				{
					// break urls into parts
					$chunks = (array) explode('/', $child['url']);

					// @todo	check me
					if(!isset($chunks[1])) Spoon::dump($child);

					// check if the action is allowed
					if(BackendAuthentication::isAllowedAction($chunks[1], $chunks[0]))
					{
						// reset link
						$level['url'] = $child['url'];

						// stop searching for an url
						break;
					}
				}
			}

			// no valid url, piss off
			if($level['url'] === null) continue;

			// break urls into parts
			$chunks = (array) explode('/', $level['url']);

			// set first chunk
			if(!isset($chunks[1])) $chunks[1] = '';

			// is allowed?
			if(BackendAuthentication::isAllowedAction($chunks[1], $chunks[0]))
			{
				// selected state
				$selected = (bool) (in_array($key, $selectedKeys, true) || $level['url'] == $this->url->getModule() .'/'. $this->url->getAction());

				// open li-tag
				if($selected) $html .= '<li class="selected">'."\n";
				else $html .= '<li>'."\n";

				// add the link
				$html .= '<a href="/'. NAMED_APPLICATION .'/'. BackendLanguage::getWorkingLanguage() .'/'. $level['url'] .'">'. $level['label'] .'</a>'."\n";

				// should we go deeper?
				if($selected && isset($level['children']))
				{
					// init var
					$first = true;

					foreach($level['children'] as $child)
					{
						$subHTML = '<ul>'."\n";

						// break urls into parts
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
							$subHTML .= '<a href="/'. NAMED_APPLICATION .'/'. BackendLanguage::getWorkingLanguage() .'/'. $child['url'] .'">'. $child['label'] .'</a>'."\n";

							// end li
							$subHTML .='</li>'."\n";
						}

						// end html, or replace it by nothing
						if($subHTML == '<ul>'."\n") $subHTML = '';
						else $subHTML .= '</ul>'."\n";

						// add html
						$html .= $subHTML;
					}
				}

				// end li
				$html .'</li>'."\n";
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
	 * @param	int $startDepth
	 * @param	int[optional] $maximumDepth
	 */
	public function getMainNavigation()
	{
		// some modules shouldn't be showed in the main-navigation
		$modulesToIgnore = array('settings');

		// get selected keys, we need them for the selected state
		$selectedKeys = (array) $this->getSelectedKeys();

		// init html
		$html = '<ul>';

		// build and return the HTML
		foreach($this->navigation as $key => $level)
		{
			// ignore some modules
			if(in_array($key, $modulesToIgnore)) continue;


			if($key == 'dashboard' || $key == 'pages')
			{
				// break urls into parts
				$chunks = (array) explode('/', $level['url']);

				// set first chunk
				if(!isset($chunks[1])) $chunks[1] = '';

				// is allowed?
				if(BackendAuthentication::isAllowedAction($chunks[1], $chunks[0]))
				{
					// open li-tag
					if(in_array($key, $selectedKeys, true) || $level['url'] == $this->url->getModule() .'/'. $this->url->getAction()) $html .= '<li class="selected">'."\n";
					else $html .= '<li>'."\n";

					// add the link
					$html .= '<a href="/'. NAMED_APPLICATION .'/'. BackendLanguage::getWorkingLanguage() .'/'. $level['url'] .'">'. $level['label'] .'</a>'."\n";

					// end li
					$html .'</li>'."\n";
				}
			}

			// modules is a special one
			else
			{
				if(!isset($level['children'])) continue;

				// loop the childs
				foreach($level['children'] as $child)
				{
					// no url provided?
					if($child['url'] === null && isset($child['children']))
					{
						// loop all childs till we find a valid one
						foreach($child['children'] as $subChild)
						{
							// break urls into parts
							$chunks = (array) explode('/', $subChild['url']);

							// set first chunk
							if(!isset($chunks[1])) $chunks[1] = '';

							// is allowed?
							if(BackendAuthentication::isAllowedAction($chunks[1], $chunks[0]))
							{
								// reset the child url
								$child['url'] = $subChild['url'];

								// stop after we found the first one
								break;
							}
						}
					}

					if($child['url'] == '') continue;

					// break urls into parts
					$chunks = (array) explode('/', $child['url']);

					// set first chunk
					if(!isset($chunks[1])) $chunks[1] = '';

					// is allowed?
					if(BackendAuthentication::isAllowedAction($chunks[1], $chunks[0]))
					{
						// open li-tag
						if(in_array($key, $selectedKeys, true) || $child['url'] == $this->url->getModule() .'/'. $this->url->getAction()) $html .= '<li class="selected">'."\n";
						else $html .= '<li>'."\n";

						// add the link
						$html .= '<a href="/'. NAMED_APPLICATION .'/'. BackendLanguage::getWorkingLanguage() .'/'. $child['url'] .'">'. $level['label'] .'</a>'."\n";

						// end li
						$html .'</li>'."\n";

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

		// build the url to search for
		$urlModule = $this->url->getModule();
		$urlAction = $this->url->getAction();
		$urlToSearch = $urlModule .'/'. $urlAction;

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
								// split the url so we know the module/action
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
			// url already known?
			if($level['url'] == $urlToSearch) $keys[] = $key;

			// has this level any children?
			if(isset($level['children']))
			{
				// loop second level
				foreach($level['children'] as $module => $level)
				{
					// add all keys if the url is found
					if($level['url'] == $urlToSearch || $module == $urlModule)
					{
						// if the action is a part of the submenu 'settings', we need to store the settings/modules keys
						if(isset($actions[$urlModule]['actions'][$urlAction]) && $actions[$urlModule]['actions'][$urlAction] == 'settings')
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
					if(isset($level['children']))
					{
						// loop third level
						foreach($level['children'] as $level)
						{
							// url found?
							if($level['url'] == $urlToSearch || $module == $urlModule)
							{
								// if the action is a part of the submenu 'settings', we need to store the settings/modules keys
								if(isset($actions[$urlModule]['actions'][$urlAction]) && $actions[$urlModule]['actions'][$urlAction] == 'settings')
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