<?php

/**
 * BackendNavigation
 *
 * This class will be used to build the navigation
 *
 * @package		Backend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendNavigation
{
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

		$this->aNavigation = $aNavigation;
	}


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

			// open li-tag
			if(in_array($key, $aSelectedKeys, true) || $level['url'] == $this->url->getModule() .'/'. $this->url->getAction())
			{
				array_shift($aSelectedKeys);
				$html .= '<li class="selected">LUL***'."\n";
			}
			else $html .= '<li>'."\n";

			// add the link
			$html .= '<a href="/'. NAMED_APPLICATION .'/'. BackendLanguage::getWorkingLanguage() .'/'. $level['url'] .'">'. $level['label'] .'</a>'."\n";

			// has the current element children?
			if(isset($aNavigation[$key]['children']))
			{
				// recursive alert, build the childs and reset the html
				$html = $this->createHTML($aNavigation[$key]['children'], $startDepth, $maximumDepth, $aSelectedKeys, $currentDepth + 1, $html);
			}

			// end the li-tag
			$html .= '</li>' ."\n";
		}

		// end the ul-tag
		$html .= '</ul>'."\n";

		// return the HTML that was build
		return $html;
	}


	public function getNavigation($startDepth, $maximumDepth = null)
	{
		// get selected keys
		$aSelectedKeys = (array) $this->getSelectedKeys();

//		Spoon::dump($aSelectedKeys);

		// build html
		$html = $this->createHTML($this->aNavigation, $startDepth, $maximumDepth, $aSelectedKeys);

		return $html;
	}


	private function getSelectedKeys()
	{
		$aKeys = array();
		$urlToSearch = $this->url->getModule() .'/'. $this->url->getAction();

		foreach($this->aNavigation as $key => $level)
		{
			if($level['url'] == $urlToSearch) $aKeys[] = $key;

			if(isset($level['children']))
			{
				foreach ($level['children'] as $module => $level)
				{
					if($level['url'] == $urlToSearch)
					{
						$aKeys[] = $key;
						$aKeys[] = $module;
					}

					if(isset($level['children']))
					{
						foreach ($level['children'] as $action => $level)
						{
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

		return $aKeys;
	}
}
?>