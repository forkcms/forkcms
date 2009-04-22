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
	private $aNavigation = array();


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
		$this->aNavigation = $aNavigation;
	}


	/**
	 * Create the html for the navigation
	 *	It will generate valid HTML for the given depth until the maximum depth
	 *
	 * @return	string
	 * @param	array $aNavigation
	 * @param	int $startDepth
	 * @param	int $maximumDepth
	 * @param	array[optional] $aSelectedKeys
	 * @param	int[optional] $currentDepth
	 * @param	string[optional] $html
	 */
	private function createHTML($aNavigation, $startDepth, $maximumDepth, $aSelectedKeys = array(), $currentDepth = 1, $html = '')
	{
		// redefine
		$aNavigation = (array) $aNavigation;
		$startDepth = (int) $startDepth;
		$maximumDepth = (int) $maximumDepth;
		$aSelectedKeys = (array) $aSelectedKeys;
		$currentDepth = (int) $currentDepth;
		$html = (string) $html;

		// add opening ul-tag
		$html .= '<ul>'."\n";

		// loop the incomming array
		foreach($aNavigation as $key => $level)
		{
			// if no url is known we have to use the first present url
			if($level['url'] === null)
			{
				// modules is something special, it can contain multiple levels
				if($key == 'modules')
				{
					// get the keys (the array is name-based not numeric)
					$aKeys = array_keys($aNavigation[$key]['children']);

					// check if the deepest level is present, if so we use that url
					if(isset($aNavigation[$key]['children'][$aKeys[0]]['children'][0]['url'])) $level['url'] = $aNavigation[$key]['children'][$aKeys[0]]['children'][0]['url'];

					// use first level, that should be filled, otherwise someone fucked with our navigation
					elseif(isset($aNavigation[$key]['children'][$aKeys[0]]['url'])) $level['url'] = $aNavigation[$key]['children'][$aKeys[0]]['url'];

					// a fallback, no way the script can get here
					else $level['url'] = '#';
				}

				// other first-level elements don't have multiple levels
				elseif(isset($aNavigation[$key]['children'][0]['url'])) $level['url'] = $aNavigation[$key]['children'][0]['url'];

				// a fallback, no way the script can get here
				else $level['url'] = '#';
			}

			// break urls into parts
			$aChunks = (array) explode('/', $level['url']);

			// is the html requested?
			if($currentDepth >= $startDepth && $currentDepth <= $maximumDepth && BackendAuthentication::isAllowedAction($aChunks[1], $aChunks[0]))
			{
				// open li-tag
				if(in_array($key, $aSelectedKeys, true) || $level['url'] == $this->url->getModule() .'/'. $this->url->getAction()) $html .= '<li class="selected">'."\n";
				else $html .= '<li>'."\n";

				// add the link
				$html .= '<a href="/'. NAMED_APPLICATION .'/'. BackendLanguage::getWorkingLanguage() .'/'. $level['url'] .'">'. $level['label'] .'</a>'."\n";
			}

			// has the current element children?
			if(isset($aNavigation[$key]['children']) && in_array($key, $aSelectedKeys, true) && BackendAuthentication::isAllowedAction($aChunks[1], $aChunks[0]))
			{
				// recursive alert, build the childs and reset the html
				$html = $this->createHTML($aNavigation[$key]['children'], $startDepth, $maximumDepth, $aSelectedKeys, $currentDepth + 1, $html);
			}

			// is the html requested?
			if($currentDepth >= $startDepth && $currentDepth <= $maximumDepth && BackendAuthentication::isAllowedAction($aChunks[1], $aChunks[0]))
			{
				// end the li-tag
				$html .= '</li>' ."\n";
			}
		}

		// end the ul-tag
		$html .= '</ul>'."\n";

		if($html == '<ul></ul>') $html = '';

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
		$aSelectedKeys = (array) $this->getSelectedKeys();

		// build and return the HTML
		return $this->createHTML($this->aNavigation, $startDepth, $maximumDepth, $aSelectedKeys);
	}


	/**
	 * Get the selected keys based on the current module/actions
	 *
	 * @return	array
	 */
	private function getSelectedKeys()
	{
		// init var
		$aKeys = array();

		// build the url to search for
		$urlToSearch = $this->url->getModule() .'/'. $this->url->getAction();

		// loop the first level
		foreach($this->aNavigation as $key => $level)
		{
			// url already known?
			if($level['url'] == $urlToSearch) $aKeys[] = $key;

			// has this level any children?
			if(isset($level['children']))
			{
				// loop second level
				foreach ($level['children'] as $module => $level)
				{
					// add all keys if the url is found
					if($level['url'] == $urlToSearch)
					{
						$aKeys[] = $key;
						$aKeys[] = $module;
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
								$aKeys[] = $key;
								$aKeys[] = $module;
							}
						}
					}
				}
			}
		}

		// return the selected keys
		return $aKeys;
	}
}

?>