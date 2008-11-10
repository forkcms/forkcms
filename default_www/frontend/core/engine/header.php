<?php

/**
 * Fork
 *
 * This source file is part of Fork CMS.
 *
 * @package		frontend
 * @subpackage	header
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendHeader
{
	/**
	 * Custom meta
	 *
	 * @var	string
	 */
	private $metaCustom;


	/**
	 * Metadescription
	 *
	 * @var	string
	 */
	private $metaDescription;


	/**
	 * Metakeywords
	 *
	 * @var	string
	 */
	private $metaKeywords;


	/**
	 * Pagetitle
	 *
	 * @var	string
	 */
	private $pageTitle;


	/**
	 * Set meta-custom
	 *
	 * @return	void
	 * @param	string $value
	 */
	public function setMetaCustom($value)
	{
		$this->metaDescription = (string) $value;
	}


	/**
	 * Set meta-description
	 *
	 * @return	void
	 * @param	string $value
	 * @param	bool[optional] $overwrite
	 */
	public function setMetaDescription($value, $overwrite = false)
	{
		// redefine vars
		$value = (string) $value;
		$overwrite = (bool) $overwrite;

		// set var
		if ($overwrite) $this->metaDescription = $value;
		else
		{
			if($this->metaDescription == '') $this->metaDescription = $value;
			else $this->metaDescription .= ', '. $value;
		}
	}


	/**
	 * Set meta-keywords
	 *
	 * @return	void
	 * @param	string $value
	 * @param	bool[optional] $overwrite
	 */
	public function setMetaKeywords($value, $overwrite = false)
	{
		// redefine vars
		$value = (string) $value;
		$overwrite = (bool) $overwrite;

		// set var
		if ($overwrite) $this->metaKeywords = $value;
		else
		{
			if($this->metaKeywords == '') $this->metaKeywords = $value;
			else $this->metaKeywords .= ', '. $value;
		}
	}


	/**
	 * Set the pagetitle
	 *
	 * @return	void
	 * @param	string $value
	 * @param	bool[optional] $overwrite
	 */
	public function setPageTitle($value, $overwrite = false)
	{
		// redefine vars
		$value = (string) $value;
		$overwrite = (bool) $overwrite;

		// set var
		if($overwrite) $this->pageTitle = $value;
		else
		{
			if(empty($value)) $this->pageTitle = CoreModel::getModuleSetting('core', 'site_title_'. FRONTEND_LANGUAGE, SITE_DEFAULT_TITLE);
			else
			{
				if($this->pageTitle == '') $this->pageTitle = $value . SITE_TITLE_SEPERATOR . CoreModel::getModuleSetting('core', 'site_title_'. FRONTEND_LANGUAGE, SITE_DEFAULT_TITLE);
				else $this->pageTitle = $value . SITE_TITLE_SEPERATOR . $this->pageTitle;
			}
		}

	}
}
?>