<?php

namespace Backend\Modules\Extensions\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;
use Symfony\Component\Filesystem\Filesystem;

/**
 * This is the module upload-action.
 * It will install a module via a compressed zip file.
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 */
class UploadModule extends BackendBaseActionAdd
{
    /**
     * Execute the action.
     */
    public function execute()
    {
        // call parent, this will probably add some general CSS/JS or other required files
        parent::execute();

        // zip extension is required for module upload
        if (!extension_loaded('zlib')) {
            $this->tpl->assign('zlibIsMissing', true);
        }

        // \ZipArchive class is required for module upload
        if (!class_exists('\ZipArchive')) {
            $this->tpl->assign('ZipArchiveIsMissing', true);
        } elseif (!$this->isWritable()) {
            // we need write rights to upload files
            $this->tpl->assign('notWritable', true);
        } else {
            // everything allright, we can upload
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
        /** @var $fileFile \SpoonFormFile */
        $fileFile = $this->frm->getField('file');

        // create \ziparchive instance
        $zip = new \ZipArchive();

        // try and open it
        if ($zip->open($fileFile->getTempFileName()) !== true) {
            $fileFile->addError(BL::getError('CorruptedFile'));
        }

        // zip file needs to contain some files
        if ($zip->numFiles == 0) {
            $fileFile->addError(BL::getError('FileIsEmpty'));
            return;
        }

        // directories we are allowed to upload to
        $allowedDirectories = array(
            'src/Backend/Modules/',
            'src/Frontend/Modules/',
            'library/external/'
        );

        // name of the module we are trying to upload
        $moduleName = null;

        // there are some complications
        $warnings = array();

        // has the module zip one level of folders too much?
        $prefix = '';

        // check every file in the zip
        for ($i = 0; $i < $zip->numFiles; $i++) {
            // get the file name
            $file = $zip->statIndex($i);
            $fileName = $file['name'];

            if ($i === 0 && $fileName !== 'src/' && $fileName !== 'library/') {
                $prefix = $fileName;
            }

            // check if the file is in one of the valid directories
            foreach ($allowedDirectories as $directory) {
                // yay, in a valid directory
                if (stripos($fileName, $prefix . $directory) === 0) {
                    // we have a library file
                    if ($directory == $prefix . 'library/external/') {
                        // strip the prefix from the filename if necessary
                        $notPrefixedFileName = $fileName;
                        if (!empty($prefix)) {
                            $notPrefixedFileName = substr($fileName, strlen($prefix));
                        }

                        if (!is_file(PATH_WWW . '/' . $fileName)) {
                            $files[] = $fileName;
                        } else {
                            $warnings[] = sprintf(BL::getError('LibraryFileAlreadyExists'), $fileName);
                        }
                        break;
                    }

                    // extract the module name from the url
                    $tmpName = trim(str_ireplace($prefix . $directory, '', $fileName), '/');
                    if ($tmpName == '') {
                        break;
                    }
                    $chunks = explode('/', $tmpName);
                    $tmpName = $chunks[0];

                    // ignore hidden files
                    if (substr(basename($fileName), 0, 1) == '.') {
                        break;
                    } elseif ($moduleName === null) {
                        // first module we find, store the name
                        $moduleName = $tmpName;
                    } elseif ($moduleName !== $tmpName) {
                        // the name does not match the previous module we found, skip the file
                        break;
                    }

                    // passed all our tests, store it for extraction
                    $files[] = $fileName;

                    // go to next file
                    break;
                }
            }
        }

        // after filtering no files left (nothing useful found)
        if (count($files) == 0) {
            $fileFile->addError(BL::getError('FileContentsIsUseless'));
            return;
        }

        // module already exists on the filesystem
        if (BackendExtensionsModel::existsModule($moduleName)) {
            $fileFile->addError(sprintf(BL::getError('ModuleAlreadyExists'), $moduleName));
            return;
        }

        // installer in array?
        if (!in_array($prefix . 'src/Backend/Modules/' . $moduleName . '/Installer/Installer.php', $files)) {
            $fileFile->addError(sprintf(BL::getError('NoInstallerFile'), $moduleName));
            return;
        }

        // unpack module files
        $zip->extractTo(PATH_WWW, $files);

        // place all the items in the prefixed folders in the right folders
        if (!empty($prefix)) {
            $fs = new Filesystem();
            foreach ($files as &$file) {
                $fullPath = PATH_WWW . '/' . $file;
                $newPath = str_replace(
                    PATH_WWW . '/' . $prefix,
                    PATH_WWW . '/',
                    $fullPath
                );

                if ($fs->exists($fullPath) && is_dir($fullPath)) {
                    $fs->mkdir($newPath);
                } elseif ($fs->exists($fullPath) && is_file($fullPath)) {
                    $fs->copy(
                        $fullPath,
                        $newPath
                    );
                }
            }

            $fs->remove(PATH_WWW . '/' . $prefix);
        }

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
        if (!BackendExtensionsModel::isWritable(FRONTEND_MODULES_PATH)) {
            return false;
        }
        if (!BackendExtensionsModel::isWritable(BACKEND_MODULES_PATH)) {
            return false;
        }

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
        if ($this->frm->isSubmitted()) {
            // shorten field variables
            $fileFile = $this->frm->getField('file');

            // validate the file
            if ($fileFile->isFilled(BL::err('FieldIsRequired')) && $fileFile->isAllowedExtension(array('zip'), sprintf(BL::getError('ExtensionNotAllowed'), 'zip'))) {
                $moduleName = $this->installModule();
            }

            // passed all validation
            if ($this->frm->isCorrect()) {
                // by now, the module has already been installed in processZipFile()

                // redirect with fireworks
                $this->redirect(BackendModel::createURLForAction('Modules') . '&report=module-installed&var=' . $moduleName . '&highlight=row-module_' . $moduleName);
            }
        }
    }
}
