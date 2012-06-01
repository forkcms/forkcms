<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the module upload-action.
 * It will install a module via a compressed zip file.
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
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
	 * Process the zip-file & install the module
	 *
	 * @return string
	 */
	private function installModule()
	{
		// list of validated files (these files will actually be unpacked)
		$files = array();

		// shorten field variables
		$fileFile = $this->frm->getField('file');

		// create ziparchive instance
		$zip = new ZipArchive();

		// try and open it
		if($zip->open($fileFile->getTempFileName()) !== true)
		{
			$fileFile->addError(BL::getError('CorruptedFile'));
		}

		// zip file needs to contain some files
		if($zip->numFiles == 0)
		{
			$fileFile->addError(BL::getError('FileIsEmpty'));
			return;
		}

		// directories we are allowed to upload to
		$allowedDirectories = array(
			'backend/modules/',
			'frontend/modules/',
			'library/external/'
		);

		// name of the module we are trying to upload
		$moduleName = null;

		// there are some complications
		$warnings = array();

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
					// we have a library file
					if($directory == 'library/external/')
					{
						if(!SpoonFile::exists(PATH_WWW . '/' . $fileName)) $files[] = $fileName;
						else $warnings[] = sprintf(BL::getError('LibraryFileAlreadyExists'), $fileName);
						break;
					}

					// extract the module name from the url
					$tmpName = trim(str_ireplace($directory, '', $fileName), '/');
					if($tmpName == '') break;
					$chunks = explode('/', $tmpName);
					$tmpName = $chunks[0];

					// ignore hidden files
					if(substr(basename($fileName), 0, 1) == '.') break;

					// first module we find, store the name
					elseif($moduleName === null) $moduleName = $tmpName;

					// the name does not match the previous module we found, skip the file
					elseif($moduleName !== $tmpName) break;

					// passed all our tests, store it for extraction
					$files[] = $fileName;

					// go to next file
					break;
				}
			}
		}

		// after filtering no files left (nothing useful found)
		if(count($files) == 0)
		{
			$fileFile->addError(BL::getError('FileContentsIsUseless'));
			return;
		}

		// module already exists on the filesystem
		if(BackendExtensionsModel::existsModule($moduleName))
		{
			$fileFile->addError(sprintf(BL::getError('ModuleAlreadyExists'), $moduleName));
			return;
		}

		// installer in array?
		if(!in_array('backend/modules/' . $moduleName . '/installer/installer.php', $files))
		{
			$fileFile->addError(sprintf(BL::getError('NoInstallerFile'), $moduleName));
			return;
		}

		// unpack module files
		$zip->extractTo(PATH_WWW, $files);

		// run installer
		BackendExtensionsModel::installModule($moduleName, $warnings);

		// return the files
		return $moduleName;
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
			if($fileFile->isFilled(BL::err('FieldIsRequired')) && $fileFile->isAllowedExtension(array('zip'), sprintf(BL::getError('ExtensionNotAllowed'), 'zip')))
			{
				$moduleName = $this->installModule();
			}

			// passed all validation
			if($this->frm->isCorrect())
			{
				// by now, the module has already been installed in processZipFile()

				// redirect with fireworks
				$this->redirect(BackendModel::createURLForAction('modules') . '&report=module-installed&var=' . $moduleName . '&highlight=row-module_' . $moduleName);
			}
		}
	}
}
