<?php

/**
 * This is the location-widget: 1 specific address
 *
 * @package		frontend
 * @subpackage	location
 *
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.1
 */
class FrontendLocationWidgetLocation extends FrontendBaseWidget
{
	/**
	 * Execute the extra
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent
		parent::execute();

		// load template
		$this->loadTemplate();

		// parse
		$this->parse();
	}


	/**
	 * Parse the data into the template
	 *
	 * @return	void
	 */
	private function parse()
	{
		// show message
		$this->tpl->assign('widgetLocationItems', FrontendLocationModel::get((int) $this->data['id']));

		// hide form
		$this->tpl->assign('widgetLocationSettings', FrontendModel::getModuleSettings('location'));
	}
}

?>