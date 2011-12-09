<?php

/**
 * This is the theme upload-action.
 * It will install a theme via a compressed zip file.
 *
 * @author Matthias Mullie <matthias@mullie.eu>
 */
class BackendExtensionsUploadTheme extends BackendBaseActionAdd
{
	/**
	 * Execute the action.
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// zip extension is required for theme upload
		if(!extension_loaded('zlib')) $this->tpl->assign('zlibIsMissing', true);

		// ZipArchive class is required for theme upload
		if(!class_exists('ZipArchive')) $this->tpl->assign('ZipArchiveIsMissing', true);

		// we need write rights to upload files
		elseif(!$this->isWritable()) $this->tpl->assign('notWritable', true);

		// oke, we can upload
		else
		{
			$this->loadForm();
			$this->validateForm();
			$this->parse();
		}

		// display the page
		$this->display();
	}

	/**
	 * Do we have write rights to the modules folders?
	 *
	 * @return bool
	 */
	private function isWritable()
	{
		// check if writable
		if(!BackendExtensionsModel::isWritable(FRONTEND_PATH . '/themes')) return false;

		// everything is writeable
		return true;
	}

	/**
	 * Create a form and its elements.
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('upload');

		// create and add elements
		$this->frm->addFile('file');
	}

	/**
	 * Validate a submitted form and process it.
	 */
	private function validateForm()
	{
		// the form is submitted
		if($this->frm->isSubmitted())
		{
			// shorten field variables
			$fileFile = $this->frm->getField('file');

			// validate the file
			if($fileFile->isFilled(BL::err('FieldIsRequired')))
			{
				// only zip files allowed
				if($fileFile->isAllowedExtension(array('zip'), sprintf(BL::getError('ExtensionNotAllowed'), 'zip')))
				{
					// create ziparchive instance
					$zip = new ZipArchive();

					// try and open it
					if($zip->open($fileFile->getTempFileName()) === true)
					{
						// zip file needs to contain some files
						if($zip->numFiles > 0)
						{
							// get first entry (= the theme folder)
							$file = $zip->statIndex(0);

							// name of the module we are trying to upload
							$themeName = trim($file['name'], '/');

							// find info.xml
							$infoXml = $zip->getFromName($themeName . '/info.xml');

							// add error if info.xml is not found
							if($infoXml === false)
							{
								$fileFile->addError(sprintf(BL::getError('NoInformationFile'), $themeName));
							}
							else
							{
								// parse xml
								try
								{
									// load info.xml
									$infoXml = @new SimpleXMLElement($infoXml, LIBXML_NOCDATA, false);

									// convert xml to useful array
									$this->information = BackendExtensionsModel::processThemeXml($infoXml);

									// empty data (nothing useful)
									if(empty($this->information)) $fileFile->addError(BL::getMessage('InformationFileIsEmpty'));

									// check if theme name in info.xml matches folder name
									if($this->information['name'] != $themeName) $fileFile->addError(BL::err('ThemeNameDoesntMatch'));
								}

								// warning that the information file is corrupt
								catch(Exception $e)
								{
									$fileFile->addError(BL::getMessage('InformationFileCouldNotBeLoaded'));
								}
							}

							// wow wow, you are trying to upload an already existing theme
							if(BackendExtensionsModel::existsTheme($themeName)) $fileFile->addError(sprintf(BL::getError('ThemeAlreadyExists'), $themeName));

							// list of validated files (these files will actually be unpacked)
							$files = array();

							// check every file in the zip
							for($i = 0; $i < $zip->numFiles; $i++)
							{
								// get the file name
								$file = $zip->statIndex($i);
								$fileName = $file['name'];

								// yay, in a valid directory
								if(stripos($fileName, $themeName . '/') === 0)
								{
									// valid file, add to extraction-list
									$files[] = $fileName;
								}
							}
						}

						// empty zip file
						else $fileFile->addError(BL::getError('FileIsEmpty'));
					}

					// something went very wrong, probably corrupted
					else $fileFile->addError(BL::getError('CorruptedFile'));
				}
			}

			// passed all validation
			if($this->frm->isCorrect())
			{
				// unpack module files
				$zip->extractTo(FRONTEND_PATH . '/themes', $files);

				// run installer
				BackendExtensionsModel::installTheme($themeName);

				// redirect with fireworks
				$this->redirect(BackendModel::createURLForAction('themes') . '&report=theme-installed&var=' . $themeName);
			}
		}
	}
}
