<?php

/**
 * This is the module upload-action.
 * It will install a module given via a compressed zip file.
 *
 * @package		backend
 * @subpackage	extensions
 *
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		3.0.0
 */
class BackendExtensionsModuleUpload extends BackendBaseActionAdd
{
	/**
	 * Execute the action.
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// zip extension is required for module upload
		// @todo do not allow upload if we do not have write rights
		if(extension_loaded('zlib'))
		{
			// load form
			$this->loadForm();

			// validate le form
			$this->validateForm();

			// parse
			$this->parse();
		}

		// show message that we are missing an extension
		else $this->tpl->assign('extensionIsMissing', true);

		// display the page
		$this->display();
	}


	/**
	 * Create a form and its elements.
	 *
	 * @return	void
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
	 *
	 * @return	void
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
				// only xml files allowed
				if($fileFile->isAllowedExtension(array('zip'), sprintf(BL::getError('ExtensionNotAllowed'), 'xml')))
				{
					// create ziparchive instance
					$zip = new ZipArchive();

					// try and open it
					if($zip->open($fileFile->getTempFileName()) === true)
					{
						// zip file needs to contain some files
						if($zip->numFiles > 0)
						{
							// directories we are allowed to upload to
							$allowedDirectories = array(
								'backend/modules',
								'frontend/modules'
							);

							// list of validated files (these files will actually be unpacked)
							$files = array();

							// name of the module we are trying to upload
							$moduleName = null;

							// check every file in the zip
							for($i = 0; $i < $zip->numFiles; $i++)
							{
								// get the file name
								$file = $zip->statIndex($i);
								$fileName = $file['name'];

								// check if the file is in one of the valid directories
								foreach($allowedDirectories as $directory)
								{
									// yay, in a valid directory
									if(stripos($fileName, $directory) !== false)
									{
										// extract the module name from the url
										$tmpName = trim(str_ireplace($directory, '', $fileName), '/');
										if($tmpName == '') break;
										$chunks = explode('/', $tmpName);
										$tmpName = $chunks[0];

										// first module we find, store the name
										if($moduleName === null) $moduleName = $tmpName;

										// the name does not match the previous madule we found, skip the file
										elseif($moduleName !== $tmpName) break;

										// passed all our tests, store it for extraction
										$files[] = $fileName;

										// got to next file
										break;
									}
								}
							}

							// after filtering we have some files to extract
							if(count($files) > 0)
							{
								// module already installed?
								if(!BackendExtensionsModel::isModuleInstalled($moduleName))
								{
									// installer in array?
									if(!in_array('backend/modules/' . $moduleName . '/installer/installer.php', $files))
									{
										$fileFile->addError(BL::getError('NoModuleInstallerFound'));
									}
								}

								// wow wow, you are trying to upload an already existing module
								else $fileFile->addError(BL::getError('ModuleAlreadyExists'));
							}

							// after filtering no files left (nothing useful found)
							else $fileFile->addError(BL::getError('FileContentsIsUseless'));
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
				// unpack everything from $files

				// run installer

				// redirect with fireworks
			}
		}
	}
}

?>