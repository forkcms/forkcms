<?php

/**
 * This is the theme install-action.
 * It will install the theme given via the "theme" GET parameter.
 *
 * @author Matthias Mullie <forkcms@mullie.eu>
 */
class BackendExtensionsInstallTheme extends BackendBaseActionIndex
{
	/**
	 * Theme we ant to install.
	 *
	 * @var string
	 */
	private $currentTheme;

	/**
	 * Execute the action.
	 */
	public function execute()
	{
		// get parameters
		$this->currentTheme = $this->getParameter('theme', 'string');

		// does the item exist
		if($this->currentTheme !== null && BackendExtensionsModel::existsTheme($this->currentTheme))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// make sure this theme can be installed
			$this->validateInstall();

			try
			{
				// do the actual install
				BackendExtensionsModel::installTheme($this->currentTheme);

				// redirect to index with a success message
				$this->redirect(BackendModel::createURLForAction('themes') . '&report=theme-installed&var=' . $this->currentTheme);
			}
			catch(Exception $e)
			{
				// redirect to index with a success message
				$this->redirect(BackendModel::createURLForAction('themes') . '&report=information-file-is-empty&var=' . $this->currentTheme);
			}
		}

		// no item found, redirect to index, because somebody is fucking with our url
		else $this->redirect(BackendModel::createURLForAction('themes') . '&error=non-existing');
	}

	/**
	 * Validate if the theme can be installed.
	 */
	private function validateInstall()
	{
		// already installed
		if(BackendExtensionsModel::isThemeInstalled($this->currentTheme))
		{
			$this->redirect(BackendModel::createURLForAction('themes') . '&error=already-installed&var=' . $this->currentTheme);
		}

		// no information file present
		if(!SpoonFile::exists(FRONTEND_PATH . '/themes/' . $this->currentTheme . '/info.xml'))
		{
			$this->redirect(BackendModel::createURLForAction('themes') . '&error=no-information-file&var=' . $this->currentTheme);
		}
	}
}
