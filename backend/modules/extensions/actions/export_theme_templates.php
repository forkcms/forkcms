<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Exports templates in the selected theme, for ease when packaging themes.
 *
 * @author Steven Dickinson <forkcms@orangestems.co.uk>
 */
class BackendExtensionsExportThemeTemplates extends BackendBaseActionEdit {
	/**
	 * All available themes
	 *
	 * @var    array
	 */
	private $availableThemes;

	/**
	 * The current selected theme
	 *
	 * @var    string
	 */
	private $selectedTheme;

	/**
	 * Execute the action
	 */
	public function execute() {
		parent::execute();
		$this->loadData();
		$this->parse();
		$this->display();
	}

	/**
	 * Load the selected theme, falling back to default if none specified.
	 */
	private function loadData() {
		// get data
		$this->selectedTheme = $this->getParameter('theme', 'string');

		// build available themes
		foreach (BackendExtensionsModel::getThemes() as $theme) $this->availableThemes[$theme['value']] = $theme['label'];

		// determine selected theme, based upon submitted form or default theme
		$this->selectedTheme = SpoonFilter::getValue($this->selectedTheme, array_keys($this->availableThemes), BackendModel::getModuleSetting('core', 'theme', 'core'));
	}

	/**
	 * Export the templates as XML.
	 */
	protected function parse() {

		$xml = BackendExtensionsModel::createTemplateXmlForExport($this->selectedTheme);

		$filename = 'templates_' . BackendModel::getUTCDate('d-m-Y') . '.xml';
		$headers = array();
		$headers[] = "Content-type: text/xml";
		$headers[] = "Content-disposition: attachment; filename='${filename}'";

		SpoonHTTP::setHeaders($headers);

		echo $xml;
		exit;
	}
}
