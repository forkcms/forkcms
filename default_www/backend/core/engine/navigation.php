<?php

/**
 * This class will be used to build the navigation
 *
 * @package		backend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Dave Lens <dave@netlash.com>
 * @author		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendNavigation
{
	/**
	 * The navigation array, will be used to build the navigation
	 *
	 * @var	array
	 */
	public $navigation = array();


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
		Spoon::set('navigation', $this);

		// grab from the reference
		$this->URL = Spoon::get('url');

		// init var
		$navigation = array();

		// require navigation-file
		require_once BACKEND_CACHE_PATH . '/navigation/navigation.php';

		// load it
		$this->navigation = (array) $navigation;

		// cleanup navigation (not needed for god user)
		if(!BackendAuthentication::getUser()->isGod())
		{
			$this->navigation = $this->cleanup($this->navigation);
		}
	}


	/**
	 * Build the HTML for a navigation item
	 *
	 * @return	string
	 * @param	array $value						The current value.
	 * @param	string $key							The current key.
	 * @param	array[optional] $selectedKeys		The keys that are selected.
	 * @param	int[optional] $startDepth			The depth to start from.
	 * @param	int[optional] $endDepth				The depth to end.
	 * @param	int[optional] $currentDepth			The depth the method is currently on.
	 */
	public function buildHTML(array $value, $key, array $selectedKeys = null, $startDepth = 0, $endDepth = null, $currentDepth = 0)
	{
		// return
		if($endDepth !== null && $currentDepth >= $endDepth) return '';

		// needed elements are set?
		if(isset($value['url']) && isset($value['label']))
		{
			// init some vars
			$selected = (isset($selectedKeys[$currentDepth]) && $selectedKeys[$currentDepth] == $key);
			$label = ucfirst(BL::lbl($value['label']));
			$URL = $value['url'];

			// append extra parameters if needed
			if(isset($value['data']['parameters']) && !empty($value['data']['parameters'])) $URL .='?' . http_build_query($value['data']['parameters']);

			// start HTML
			$HTML = '';

			// que? let's call this piece magic
			if($currentDepth >= $startDepth - 1)
			{
				// start li
				if($selected) $HTML .= '<li class="selected">' . "\n";
				else $HTML .= '<li>' . "\n";
				$HTML .= '	<a href="/' . NAMED_APPLICATION . '/' . BackendLanguage::getWorkingLanguage() . '/' . $URL . '">' . $label . '</a>' . "\n";
			}

			// children?
			if($selected && isset($value['children']))
			{
				// end depth not passed or isn't reached
				if($endDepth === null || $currentDepth < $endDepth)
				{
					// start ul if needed
					if($currentDepth != 0) $HTML .= '<ul>' . "\n";

					// loop childs
					foreach($value['children'] as $subKey => $row)
					{
						$HTML .= '	' . $this->buildHTML($row, $subKey, $selectedKeys, $startDepth, $endDepth, $currentDepth + 1);
					}

					// end ul if needed
					if($currentDepth != 0) $HTML .= '</ul>' . "\n";
				}
			}

			// end
			if($currentDepth >= $startDepth - 1) $HTML .= '</li>' . "\n";
		}

		// return
		return $HTML;
	}


	/**
	 * Clean the navigation
	 *
	 * @return	array
	 * @param	array $navigation	The navigation array.
	 */
	private function cleanup(array $navigation)
	{
		// loop elements
		foreach($navigation as $key => $value)
		{
			// init var
			$allowedChildren = array();

			// error?
			$allowed = true;

			// get rid of invalid items
			if(!isset($value['url']) || !isset($value['label'])) $allowed = false;

			// split up chunks
			list($module, $action) = explode('/', $value['url']);

			// no rights for this module?
			if(!BackendAuthentication::isAllowedModule($module)) $allowed = false;

			// no rights for this action?
			if(!BackendAuthentication::isAllowedAction($action, $module)) $allowed = false;

			// has children
			if(isset($value['children']) && is_array($value['children']) && !empty($value['children']))
			{
				// loop children
				foreach($value['children'] as $keyB => $valueB)
				{
					// error?
					$allowed = true;

					// init var
					$allowedChildrenB = array();

					// get rid of invalid items
					if(!isset($valueB['url']) || !isset($valueB['label'])) $allowed = false;

					// split up chunks
					list($module, $action) = explode('/', $valueB['url']);

					// no rights for this module?
					if(!BackendAuthentication::isAllowedModule($module)) $allowed = false;

					// no rights for this action?
					if(!BackendAuthentication::isAllowedAction($action, $module)) $allowed = false;

					// has children
					if(isset($valueB['children']) && is_array($valueB['children']) && !empty($valueB['children']))
					{
						// loop children
						foreach($valueB['children'] as $keyC => $valueC)
						{
							// error?
							$allowed = true;

							// get rid of invalid items
							if(!isset($valueC['url']) || !isset($valueC['label'])) $allowed = false;

							// split up chunks
							list($module, $action) = explode('/', $valueC['url']);

							// no rights for this module?
							if(!BackendAuthentication::isAllowedModule($module)) $allowed = false;

							// no rights for this action?
							if(!BackendAuthentication::isAllowedAction($action, $module)) $allowed = false;

							// error occured
							if(!$allowed)
							{
								unset($navigation[$key]['children'][$keyB]['children'][$keyC]);
								continue;
							}

							// store allowed children
							elseif(!in_array($navigation[$key]['children'][$keyB]['children'][$keyC], $allowedChildrenB)) $allowedChildrenB[] = $navigation[$key]['children'][$keyB]['children'][$keyC];
						}
					}

					// error occured and no allowed children on level B
					if(!$allowed && empty($allowedChildrenB))
					{
						unset($navigation[$key]['children'][$keyB]);
						continue;
					}

					// store allowed children on level B
					elseif(!in_array($navigation[$key]['children'][$keyB], $allowedChildren)) $allowedChildren[] = $navigation[$key]['children'][$keyB];

					// assign new base url for level B
					if(!empty($allowedChildrenB)) $navigation[$key]['children'][$keyB]['url'] = $allowedChildrenB[0]['url'];
				}
			}

			// error occured and no allowed children
			if(!$allowed && empty($allowedChildren))
			{
				unset($navigation[$key]);
				continue;
			}

			// assign new base url
			elseif(!empty($allowedChildren))
			{
				// init var
				$allowed = true;

				// split up chunks
				list($module, $action) = explode('/', $allowedChildren[0]['url']);

				// no rights for this module?
				if(!BackendAuthentication::isAllowedModule($module)) $allowed = false;

				// no rights for this action?
				if(!BackendAuthentication::isAllowedAction($action, $module)) $allowed = false;

				// allowed? assign base URL
				if($allowed) $navigation[$key]['url'] = $allowedChildren[0]['url'];

				// not allowed?
				else
				{
					// get first child
					$child = reset($navigation[$key]['children']);

					// assign base URL
					$navigation[$key]['url'] = $child['url'];
				}
			}
		}

		return $navigation;
	}


	/**
	 * Try to determine the selected state
	 *
	 * @return	mixed
	 * @param	array $value			The value.
	 * @param	int $key				The key.
	 * @param	array[optional] $keys	The previous marked keys.
	 */
	private function compareURL(array $value, $key, $keys = array())
	{
		// create active url
		$activeURL = $this->URL->getModule() . '/' . $this->URL->getAction();

		// add current key
		$keys[] = $key;

		// sub action?
		if(isset($value['selected_for']) && in_array($activeURL, (array) $value['selected_for'])) return $keys;

		// if the URL is available and same as the active one we have what we need.
		if(isset($value['url']) && $value['url'] == $activeURL)
		{
			if(isset($value['children']))
			{
				// loop the childs
				foreach($value['children'] as $key => $value)
				{
					// recursive here...
					$subKeys = $this->compareURL($value, $key, $keys);

					// wrap it up
					if(!empty($subKeys)) return $subKeys;
				}
			}

			// fallback
			return $keys;
		}

		// any children
		if(isset($value['children']))
		{
			// loop the childs
			foreach($value['children'] as $key => $value)
			{
				// recursive here...
				$subKeys = $this->compareURL($value, $key, $keys);

				// wrap it up
				if(!empty($subKeys)) return $subKeys;
			}
		}
	}


	/**
	 * Get the HTML for the navigation
	 *
	 * @return	string
	 * @param	int[optional] $startDepth	The start-depth.
	 * @param	int[optional] $endDepth		The end-depth.
	 */
	public function getNavigation($startDepth = 0, $endDepth = null)
	{
		// get selected
		$selectedKeys = $this->getSelectedKeys();

		// init html
		$HTML = '<ul>' . "\n";

		// loop the navigation elements
		foreach($this->navigation as $key => $value)
		{
			// append the generated HTML
			$HTML .= $this->buildHTML($value, $key, $selectedKeys, $startDepth, $endDepth);
		}

		// end ul
		$HTML .= '</ul>';

		// return the generated HTML
		return $HTML;
	}


	/**
	 * Get the selected keys based on the current module/actions
	 *
	 * @return	array
	 */
	private function getSelectedKeys()
	{
		// loop navigation
		foreach($this->navigation as $key => $value)
		{
			// get the keys
			$keys = $this->compareURL($value, $key, array());

			// stop when we found something
			if(!empty($keys)) break;
		}

		// return the selected keys
		return $keys;
	}
}

?>