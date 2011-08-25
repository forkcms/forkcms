<?php

/**
 * Fork NG CLI tool
 *
 * @author	 Jelmer Snoeck <jelmer.snoeck@netlash.com>
 *
 */
class FT
{
	/**
	 * The working directory
	 *
	 * @var	string
	 */
	private $workingDir, $frontendPath, $backendPath;


	/**
	 * CLI Path, this will be used to get base files
	 *
	 * @var	string
	 */
	private $cliPath;


	/**
	 * Start the Fork Tool
	 *
	 * @return	void
	 * @param 	array $argv		The arguments passed by the CLI
	 */
	public static function start($argv)
	{
		// are there any arguments given?
		if(count($argv) < 3) return;

		$ft = new self();
		$ft->execute();

		// set working dir and cli path
		$ft->workingDir = getcwd();
		$ft->cliPath = $argv[0];

		// get home directory
		$ft->getHomeDir();

		// get command and command name
		$command = $argv[1];
		$svar = $argv[2];
		$tvar = $argv[3];

		// check what to do
		switch($command)
		{
			case 'module':
				$ft->createModule($svar);
			break;
			case 'widget':
				$ft->createWidget($svar, $tvar);
			break;
		}
	}


	/**
	 * Execute the fork tool
	 *
	 * @return	void
	 */
	private function execute()
	{
	}


	/**
	 * Create Widget
	 */
	private function createWidget($module, $name)
	{
		// check if the widget doesn't exists to continue
		if(!is_dir($this->frontendPath . 'modules/' . $module . '/widgets/' . $name))
		{
			// @todo fix _ to uppercase name

			// check if widget php dir is available
			if(!is_dir($this->frontendPath . 'modules/' . $module . '/widgets')) mkdir($this->frontendPath . 'modules/' . $module . '/widgets');

			// widget template
			$modTemplate = $this->cliPath . 'widget/widget.php';
			$fhModTemplate = fopen($modTemplate, "r");
			$tdModTemplate = fread($fhModTemplate, filesize($modTemplate));
			$tdModTemplate = str_replace('tempnameuc', ucfirst($module), $tdModTemplate);
			$tdModTemplate = str_replace('tempname', $module, $tdModTemplate);
			$tdModTemplate = str_replace('wname', ucfirst($name), $tdModTemplate);

			// create widget
			$modFile = fopen($this->frontendPath . 'modules/' . $module . '/widgets/' . $name . '.php', 'w');
			fwrite($modFile, $tdModTemplate);
			fclose($modFile);

			// check if the widget dir is available
			if(!is_dir($this->frontendPath . 'modules/' . $module . '/layout/widgets')) mkdir($this->frontendPath . 'modules/' . $module . '/layout/widgets');

			// widget template
			$modTemplate = $this->cliPath . 'widget/widget.tpl';
			$fhModTemplate = fopen($modTemplate, "r");
			$tdModTemplate = fread($fhModTemplate, filesize($modTemplate));
			$tdModTemplate = str_replace('tempnameuc', ucfirst($module), $tdModTemplate);
			$tdModTemplate = str_replace('tempname', $module, $tdModTemplate);
			$tdModTemplate = str_replace('wname', ucfirst($name), $tdModTemplate);

			// make new widget template
			$widTemplate = fopen($this->frontendPath . 'modules/' . $module . '/layout/widgets/' . $name . '.tpl', 'w');
			fwrite($widTemplate, $tdModTemplate);
			fclose($widTemplate);

			// widget created
			echo "The widget '$name' is created.\n";
		}
		// widget exists
		else echo "The widget already exists.\n";
	}


	/**
	 * Create a new module
	 *
	 * @return
	 */
	private function createModule($name)
	{
		// check if the module doesn't exists to continue
		if(!is_dir($this->backendPath . 'modules/' . $name) && !is_dir($this->frontendPath . 'modules/' . $name))
		{
			// make backend dirs
			mkdir($this->backendPath . 'modules/' . $name);
			mkdir($this->backendPath . 'modules/' . $name . '/actions');
			mkdir($this->backendPath . 'modules/' . $name . '/engine');
			mkdir($this->backendPath . 'modules/' . $name . '/installer');
			mkdir($this->backendPath . 'modules/' . $name . '/installer/data');
			mkdir($this->backendPath . 'modules/' . $name . '/layout');
			mkdir($this->backendPath . 'modules/' . $name . '/layout/templates');

			// module template
			$modTemplate = $this->cliPath . 'module/backend/model.php';
			$fhModTemplate = fopen($modTemplate, "r");
			$tdModTemplate = fread($fhModTemplate, filesize($modTemplate));
			$tdModTemplate = str_replace('tempnameuc', ucfirst($name), $tdModTemplate);
			$tdModTemplate = str_replace('tempname', $name, $tdModTemplate);

			// create model
			$modFile = fopen($this->backendPath . 'modules/' . $name . '/engine/model.php', 'w');
			fwrite($modFile, $tdModTemplate);
			fclose($modFile);

			// install template
			$modTemplate = $this->cliPath . 'module/backend/install.php';
			$fhModTemplate = fopen($modTemplate, "r");
			$tdModTemplate = fread($fhModTemplate, filesize($modTemplate));
			$tdModTemplate = str_replace('tempnameuc', ucfirst($name), $tdModTemplate);
			$tdModTemplate = str_replace('tempname', $name, $tdModTemplate);

			// create model
			$modFile = fopen($this->backendPath . 'modules/' . $name . '/installer/install.php', 'w');
			fwrite($modFile, $tdModTemplate);
			fclose($modFile);

			// config template
			$modTemplate = $this->cliPath . 'module/backend/config.php';
			$fhModTemplate = fopen($modTemplate, "r");
			$tdModTemplate = fread($fhModTemplate, filesize($modTemplate));
			$tdModTemplate = str_replace('tempnameuc', ucfirst($name), $tdModTemplate);
			$tdModTemplate = str_replace('tempname', $name, $tdModTemplate);

			// create model
			$modFile = fopen($this->backendPath . 'modules/' . $name . '/config.php', 'w');
			fwrite($modFile, $tdModTemplate);
			fclose($modFile);

			// index action template
			$modTemplate = $this->cliPath . 'module/backend/index.php';
			$fhModTemplate = fopen($modTemplate, "r");
			$tdModTemplate = fread($fhModTemplate, filesize($modTemplate));
			$tdModTemplate = str_replace('tempnameuc', ucfirst($name), $tdModTemplate);
			$tdModTemplate = str_replace('tempname', $name, $tdModTemplate);

			// create model
			$modFile = fopen($this->backendPath . 'modules/' . $name . '/actions/index.php', 'w');
			fwrite($modFile, $tdModTemplate);
			fclose($modFile);


			// index action template
			$modTemplate = $this->cliPath . 'module/backend/index.tpl';
			$fhModTemplate = fopen($modTemplate, "r");
			$tdModTemplate = fread($fhModTemplate, filesize($modTemplate));
			$tdModTemplate = str_replace('tempnameuc', ucfirst($name), $tdModTemplate);
			$tdModTemplate = str_replace('tempname', $name, $tdModTemplate);

			// create model
			$modFile = fopen($this->backendPath . 'modules/' . $name . '/layout/templates/index.tpl', 'w');
			fwrite($modFile, $tdModTemplate);
			fclose($modFile);

			// make frontend
			mkdir($this->frontendPath . 'modules/' . $name);
			mkdir($this->frontendPath . 'modules/' . $name . '/actions');
			mkdir($this->frontendPath . 'modules/' . $name . '/engine');
			mkdir($this->frontendPath . 'modules/' . $name . '/layout');
			mkdir($this->frontendPath . 'modules/' . $name . '/layout/templates');

			// module template
			$modTemplate = $this->cliPath . 'module/frontend/model.php';
			$fhModTemplate = fopen($modTemplate, "r");
			$tdModTemplate = fread($fhModTemplate, filesize($modTemplate));
			$tdModTemplate = str_replace('tempnameuc', ucfirst($name), $tdModTemplate);
			$tdModTemplate = str_replace('tempname', $name, $tdModTemplate);

			// create model
			$modFile = fopen($this->frontendPath . 'modules/' . $name . '/engine/model.php', 'w');
			fwrite($modFile, $tdModTemplate);
			fclose($modFile);

			// module template
			$modTemplate = $this->cliPath . 'module/frontend/index.php';
			$fhModTemplate = fopen($modTemplate, "r");
			$tdModTemplate = fread($fhModTemplate, filesize($modTemplate));
			$tdModTemplate = str_replace('tempnameuc', ucfirst($name), $tdModTemplate);
			$tdModTemplate = str_replace('tempname', $name, $tdModTemplate);

			// create model
			$modFile = fopen($this->frontendPath . 'modules/' . $name . '/actions/index.php', 'w');
			fwrite($modFile, $tdModTemplate);
			fclose($modFile);

			// module template
			$modTemplate = $this->cliPath . 'module/frontend/config.php';
			$fhModTemplate = fopen($modTemplate, "r");
			$tdModTemplate = fread($fhModTemplate, filesize($modTemplate));
			$tdModTemplate = str_replace('tempnameuc', ucfirst($name), $tdModTemplate);
			$tdModTemplate = str_replace('tempname', $name, $tdModTemplate);

			// create model
			$modFile = fopen($this->frontendPath . 'modules/' . $name . '/config.php', 'w');
			fwrite($modFile, $tdModTemplate);
			fclose($modFile);

			// create model
			$modFile = fopen($this->frontendPath . 'modules/' . $name . '/layout/templates/index.tpl', 'w');
			fwrite($modFile, '');
			fclose($modFile);

			echo "Module '" . ucfirst($name) . "' created.\n";
		}
		// module already exists
		else echo "This module already exists. Please choose another name.\n";
	}


	/**
	 * Gets the home directory of the current path
	 *
	 * @return	void
	 */
	private function getHomeDir()
	{
		// are we in default_www or library?
		$posDefWWW = strpos($this->workingDir, 'default_www');
		$posDefLib = strpos($this->workingDir, 'library');

		// we're not in one of forks working dirs
		if(empty($posDefWWW) && empty($posDefWWW))
		{
			// is there a library path and default_www path available?
			if(!is_dir($this->workingDir . '/default_www') || !is_dir($this->workingDir . '/library')) print "This is not a valid Fork NG path. Please initiate in your home folder of your project. \n";

			// create working paths
			$this->frontendPath = $this->workingDir . '/default_www/frontend/';
			$this->backendPath = $this->workingDir . '/default_www/backend/';
		}
		// we're in one
		else
		{
			// where to split on
			$splitChar = (!empty($posDefWWW)) ? 'default_www' : 'library';

			// split the directory to go into default_www
			$workingDir = explode($splitChar, $this->workingDir);
			$workingDir = $workingDir[0];

			// create paths
			$this->frontendPath = $workingDir . 'default_www/frontend/';
			$this->backendPath = $workingDir . 'default_www/backend/';
		}

		// create real cli path
		$this->cliPath = substr($this->cliPath, 0, -6);
	}
}

FT::start($argv);

?>