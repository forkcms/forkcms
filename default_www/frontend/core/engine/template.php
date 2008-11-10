<?php
require_once 'spoon/template/template.php';

class ForkTemplate extends SpoonTemplate
{
	public function __construct()
	{
		// set cache directory
		$this->setCacheDirectory(FRONTEND_CACHE_PATH .'/cached_templates');

		// set compile directory
		$this->setCompileDirectory(FRONTEND_CACHE_PATH .'/templates');

		// when debugging the template should be recompiled every time
		$this->setForceCompile(SPOON_DEBUG);
	}


	public function display($name)
	{
		// assign constants
		$this->assign('FRONTEND_CORE_PATH', FRONTEND_CORE_PATH);

//		$this->assignArray(FrontendLanguage::getLabels(), 'lbl');

		parent::display($name);

	}
}
?>