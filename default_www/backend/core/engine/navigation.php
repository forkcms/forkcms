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
	 * Build the HTML for an navigation item
	 *
	 * @return	string
	 * @param	string $value
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
			$label = ucfirst(BL::getLabel($value['label']));
			$URL = $value['url'];

			// append extra parameters if needed
			if(isset($value['data']['parameters']) && !empty($value['data']['parameters'])) $URL .='?'. http_build_query($value['data']['parameters']);

			// start HTML
			$HTML = '';

			// que? let's call this piece magic
			if($currentDepth >= $startDepth - 1)
			{
				// start li
				if($selected) $HTML .= '<li class="selected">'."\n";
				else $HTML .= '<li>'."\n";
				$HTML .= '	<a href="/'. NAMED_APPLICATION .'/'. BackendLanguage::getWorkingLanguage() .'/'. $URL .'">'. $label.'</a>'."\n";
			}

			// children?
			if($selected && isset($value['children']))
			{
				// end depth not passed or isn't reached
				if($endDepth === null || $currentDepth < $endDepth)
				{
					// start ul if needed
					if($currentDepth != 0) $HTML .= '<ul>'."\n";

					// loop childs
					foreach($value['children'] as $subKey => $row)
					{
						$HTML .= '	'. $this->buildHTML($row, $subKey, $selectedKeys, $startDepth, $endDepth, $currentDepth + 1);
					}

					// end ul if needed
					if($currentDepth != 0) $HTML .= '</ul>'."\n";
				}
			}

			// end
			if($currentDepth >= $startDepth - 1) $HTML .= '</li>'."\n";
		}

		return $HTML;
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
		$HTML = '<ul>'."\n";

		// loop the navigation elements
		foreach($this->navigation as $key => $value)
		{
			// append the generated HTML
			$HTML .= $this->buildHTML($value, $key, $selectedKeys, $startDepth, $endDepth);
		}

		// end ul
		$HTML .= '</ul>';

		// cleanup
//		$HTML = preg_replace('|<ul>(\s*)</ul>|iUs', '<ul>', $HTML);
//		$HTML = preg_replace('|<ul>(\s*)<ul>|iUs', '<ul>', $HTML);
//		$HTML = preg_replace('|</ul>(\s*)</ul>|iUs', '<ul>', $HTML);

		// return the generated HTML
		return $HTML;
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
		$activeURL = $this->URL->getModule() .'/'. $this->URL->getAction();

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

					// wrap it up.
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

				// wrap it up.
				if(!empty($subKeys)) return $subKeys;
			}
		}
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