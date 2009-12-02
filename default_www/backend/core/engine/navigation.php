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

		// require navigation-file
		require_once BACKEND_CACHE_PATH .'/navigation/navigation.php';

		// load it
		$this->navigation = $navigation;
	}


	/**
	 * Create the html for the navigation
	 *	It will generate valid HTML for the given depth until the maximum depth
	 *
	 * @return	string
	 * @param	array $navigation
	 * @param	int $startDepth
	 * @param	int $maximumDepth
	 * @param	array[optional] $selectedKeys
	 * @param	int[optional] $currentDepth
	 * @param	string[optional] $html
	 */
	private function createHTML($navigation, $startDepth, $maximumDepth, $selectedKeys = array(), $currentDepth = 1, $html = '')
	{
		// redefine
		$navigation = (array) $navigation;
		$startDepth = (int) $startDepth;
		$maximumDepth = (int) $maximumDepth;
		$selectedKeys = (array) $selectedKeys;
		$currentDepth = (int) $currentDepth;
		$html = (string) $html;

		// add opening ul-tag
		$html .= '<ul>'."\n";

		// loop the incomming array
		foreach($navigation as $key => $level)
		{
			// if no url is known we have to use the first present url
			if($level['url'] === null)
			{
				// modules is something special, it can contain multiple levels
				if($key == 'modules')
				{
					// get the keys (the array is name-based not numeric)
					$keys = array_keys($navigation[$key]['children']);

					// loop keys
					foreach($keys as $child)
					{
						if(isset($navigation[$key]['children'][$child]['children'][0]['url']) && BackendAuthentication::isAllowedModule($child))
						{
							$level['url'] = $navigation[$key]['children'][$child]['children'][0]['url'];
							break;
						}
					}
				}

				// other first-level elements don't have multiple levels
				else
				{
//					Spoon::dump($navigation[$key]['children']);

					if(isset($navigation[$key]['children'][0]['url'])) $level['url'] = $navigation[$key]['children'][0]['url'];
				}
			}

			// break urls into parts
			$chunks = (array) explode('/', $level['url']);

			if(!isset($chunks[1])) $chunks[1] = '';

//			Spoon::dump($chunks, false);

			// is the html requested?
			if($currentDepth >= $startDepth && $currentDepth <= $maximumDepth && BackendAuthentication::isAllowedAction($chunks[1], $chunks[0]))
			{
				// open li-tag
				if(in_array($key, $selectedKeys, true) || $level['url'] == $this->url->getModule() .'/'. $this->url->getAction()) $html .= '<li class="selected">'."\n";
				else $html .= '<li>'."\n";

				// add the link
				$html .= '<a href="/'. NAMED_APPLICATION .'/'. BackendLanguage::getWorkingLanguage() .'/'. $level['url'] .'">'. $level['label'] .'</a>'."\n";
			}

			// has the current element children?
			if(isset($navigation[$key]['children']) && in_array($key, $selectedKeys, true) && BackendAuthentication::isAllowedAction($chunks[1], $chunks[0]))
			{
				// recursive alert, build the childs and reset the html
				$html = $this->createHTML($navigation[$key]['children'], $startDepth, $maximumDepth, $selectedKeys, $currentDepth + 1, $html);
			}

			// is the html requested?
			if($currentDepth >= $startDepth && $currentDepth <= $maximumDepth && BackendAuthentication::isAllowedAction($chunks[1], $chunks[0]))
			{
				// end the li-tag
				$html .= '</li>' ."\n";
			}
		}

		// end the ul-tag
		$html .= '</ul>'."\n";

		// return the HTML that was build
		return $html;
	}


	/**
	 * Get the HTML for the navigation
	 *
	 * @return	string
	 * @param	int $startDepth
	 * @param	int[optional] $maximumDepth
	 */
	public function getNavigation($startDepth, $maximumDepth = null)
	{
		// redefine
		$startDepth = (int) $startDepth;
		$maximumDepth = ($maximumDepth !== null) ? (int) $maximumDepth :  $startDepth + 1;

		// get selected keys, we need them for the selected state
		$selectedKeys = (array) $this->getSelectedKeys();

		// build and return the HTML
		return $this->createHTML($this->navigation, $startDepth, $maximumDepth, $selectedKeys);
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

		// build the url to search for
		$urlToSearch = $this->url->getModule() .'/'. $this->url->getAction();

		// loop the first level
		foreach($this->navigation as $key => $level)
		{
			// url already known?
			if($level['url'] == $urlToSearch) $keys[] = $key;

			// has this level any children?
			if(isset($level['children']))
			{
				// loop second level
				foreach ($level['children'] as $module => $level)
				{
					// add all keys if the url is found
					if($level['url'] == $urlToSearch)
					{
						$keys[] = $key;
						$keys[] = $module;
					}

					// has children?
					if(isset($level['children']))
					{
						// loop third level
						foreach ($level['children'] as $level)
						{
							// url found?
							if($level['url'] == $urlToSearch)
							{
								$keys[] = $key;
								$keys[] = $module;
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