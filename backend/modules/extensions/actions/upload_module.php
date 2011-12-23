<?php

/**
 * This is the module upload-action.
 * It will install a module via a compressed zip file.
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class BackendExtensionsUploadModule extends BackendBaseActionAdd
{
	/**
	 * Execute the action.
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// zip extension is required for module upload
		if(!extension_loaded('zlib')) $this->tpl->assign('zlibIsMissing', true);

		// ZipArchive class is required for module upload
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
		if(!BackendExtensionsModel::isWritable(FRONTEND_MODULES_PATH)) return false;
		if(!BackendExtensionsModel::isWritable(BACKEND_MODULES_PATH)) return false;

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
							// directories we are allowed to upload to
							$allowedDirectories = array(
								'backend/modules/',
								'frontend/modules/'
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
									if(stripos($fileName, $directory) === 0)
									{
										// extract the module name from the url
										$tmpName = trim(str_ireplace($directory, '', $fileName), '/');
										if($tmpName == '') break;
										$chunks = explode('/', $tmpName);
										$tmpName = $chunks[0];

										// ignore hidden files
										if(substr(basename($fileName), 0, 1) == '.') break;

										// first module we find, store the name
										elseif($moduleName === null) $moduleName = $tmpName;

										// the name does not match the previous madule we found, skip the file
										elseif($moduleName !== $tmpName) break;

										// passed all our tests, store it for extraction
										$files[] = $fileName;

										// go to next file
										break;
									}
								}
							}

							// after filtering we have some files to extract
							if(count($files) > 0)
							{
								// module already exists on the filesystem
								if(!BackendExtensionsModel::existsModule($moduleName))
								{
									// installer in array?
									if(!in_array('backend/modules/' . $moduleName . '/installer/installer.php', $files))
									{
										$fileFile->addError(sprintf(BL::getError('NoInstallerFile'), $moduleName));
									}
								}

								// wow wow, you are trying to upload an already existing module
								else $fileFile->addError(sprintf(BL::getError('ModuleAlreadyExists'), $moduleName));
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
				// unpack module files
				$zip->extractTo(PATH_WWW, $files);

				// run installer
				BackendExtensionsModel::installModule($moduleName);

				// redirect with fireworks
				$this->redirect(BackendModel::createURLForAction('modules') . '&report=module-installed&var=' . $moduleName . '&highlight=row-module_' . $moduleName);
			}
		}
	}
}
