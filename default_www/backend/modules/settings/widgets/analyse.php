<?php

/**
 * This widget will analyze the CMS warnings
 *
 * @package		backend
 * @subpackage	settings
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendSettingsWidgetAnalyse extends BackendBaseWidget
{
	/**
	 * Execute the widget
	 *
	 * @return	void
	 */
	public function execute()
	{
		// set column
		$this->setColumn('left');

		// set position
		$this->setPosition(1);

		// parse
		$this->parse();

		// display
		$this->display();
	}


	/**
	 * Parse into template
	 *
	 * @return	void
	 */
	private function parse()
	{
		// init vars
		$warnings = BackendSettingsModel::getWarnings();

		// assign warnings
		if(!empty($warnings)) $this->tpl->assign('warnings', $warnings);
	}
}

?>