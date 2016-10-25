<?php

namespace Backend\Modules\Extensions\Actions;

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Extensions\Engine\Model as BackendExtensionsModel;
use Exception;
use Symfony\Component\Filesystem\Filesystem;
use ZipArchive;

/**
 * This is the theme upload-action.
 * It will install a theme via a compressed zip file.
 */
class UploadTheme extends BackendBaseActionAdd
{
    const INFO_FILE = 'info.xml';
    const IGNORE_LIST = ['__MACOSX'];

    /**
     * @var array
     */
    private $info;

    /**
     * @var string
     */
    private $infoFilePath;

    /**
     * @var string
     */
    private $themeName;

    /**
     * @var string
     */
    private $parentFolderName;

    /**
     * Execute the action.
     */
    public function execute()
    {
        // call parent, this will probably add some general CSS/JS or other required files
        parent::execute();

        // zip extension is required for theme upload
        if (!extension_loaded('zlib')) {
            $this->tpl->assign('zlibIsMissing', true);
        }

        // ZipArchive class is required for theme upload
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
     * Do we have write rights to the modules folders?
     *
     * @return bool
     */
    private function isWritable()
    {
        return BackendExtensionsModel::isWritable(FRONTEND_PATH . '/Themes');
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
            /** @var $fileFile \SpoonFormFile */
            $fileFile = $this->frm->getField('file');

            // validate the file
            if ($fileFile->isFilled(BL::err('FieldIsRequired'))) {
                // only zip files allowed
                if ($fileFile->isAllowedExtension(array('zip'), sprintf(BL::getError('ExtensionNotAllowed'), 'zip'))) {
                    // create ziparchive instance
                    $zip = new ZipArchive();

                    // try and open it
                    if ($zip->open($fileFile->getTempFileName()) === true) {
                        // zip file needs to contain some files
                        if ($zip->numFiles > 0) {
                            $infoXml = $this->findInfoFileInZip($zip);

                            // Throw error if info.xml is not found
                            if ($infoXml === false) {
                                return $fileFile->addError(sprintf(BL::getError('NoInformationFile')));
                            }

                            // parse xml
                            try {
                                // load info.xml
                                $infoXml = @new \SimpleXMLElement($infoXml, LIBXML_NOCDATA, false);

                                // convert xml to useful array
                                $this->info = BackendExtensionsModel::processThemeXml($infoXml);

                                // empty data (nothing useful)
                                if (empty($this->info)) {
                                    $fileFile->addError(BL::getMessage('InformationFileIsEmpty'));
                                }

                                // Define the theme name, based on the info.xml file.
                                $this->themeName = $this->info['name'];
                            } catch (Exception $e) {
                                // warning that the information file is corrupt
                                $fileFile->addError(BL::getMessage('InformationFileCouldNotBeLoaded'));
                            }

                            // wow wow, you are trying to upload an already existing theme
                            if (BackendExtensionsModel::existsTheme($this->themeName)) {
                                $fileFile->addError(sprintf(BL::getError('ThemeAlreadyExists'), $this->themeName));
                            }

                            $files = $this->getValidatedFilesList($zip);
                        } else {
                            // empty zip file
                            $fileFile->addError(BL::getError('FileIsEmpty'));
                        }
                    } else {
                        // something went very wrong, probably corrupted
                        $fileFile->addError(BL::getError('CorruptedFile'));
                    }
                }
            }

            // Passed all validation
            if ($this->frm->isCorrect()) {
                // Unpack the zip. If the files were not found inside a parent directory, we create the theme directory.
                $themePath = FRONTEND_PATH . '/Themes';
                if ($this->parentFolderName === null) {
                    $themePath .= "/{$this->themeName}";
                }
                $zip->extractTo($themePath, $files);

                // Rename the original name of the parent folder from the zip to the correct theme foldername.
                $fs = new Filesystem();
                $parentZipFolderPath = $themePath . '/' . $this->parentFolderName;
                if (
                    $this->parentFolderName !== $this->themeName &&
                    $this->parentFolderName !== null &&
                    $fs->exists($parentZipFolderPath)
                ) {
                    $fs->rename($parentZipFolderPath, "$themePath/{$this->themeName}");
                }

                // Run installer
                BackendExtensionsModel::installTheme($this->themeName);

                // Redirect with fireworks
                $this->redirect(BackendModel::createURLForAction('Themes') . '&report=theme-installed&var=' . $this->themeName);
            }
        }
    }

    /**
     * Two ideal situations possible: we have a zip with files including info.xml, or we have a zip with the theme-folder.
     *
     * @param ZipArchive $zip
     * @return string
     */
    private function findInfoFileInZip(ZipArchive $zip)
    {
        for ($i = 0; $i < $zip->numFiles; $i++) {
            if (mb_stripos($zip->getNameIndex($i), self::INFO_FILE) !== false) {
                $infoFile = $zip->statIndex($i);

                // Check that the file is not found inside a directory to ignore.
                if ($this->checkIfPathContainsIgnoredWord($infoFile['name'])) {
                    continue;
                }

                $this->infoFilePath = $infoFile['name'];
                $this->info = $zip->getFromName($infoFile['name']);
                break;
            }
        }

        return $this->info;
    }

    /**
     * Create a list of files. These are the files that will actuall be unpacked to the Themes folder.
     * Either we have a zip that contains 1 parent directory with files inside (directory not necessarily named like
     * the theme) and we extract those files. Or we have a zip that directly contains the theme files and we should
     * prepend them with the theme folder.
     *
     * @param ZipArchive $zip
     * @return String[]
     */
    private function getValidatedFilesList($zip)
    {
        $this->parentFolderName = $this->extractFolderNameBasedOnInfoFile($this->infoFilePath);

        // Check every file in the zip
        $files = array();
        for ($i = 0; $i < $zip->numFiles; ++$i) {
            // Get the file name
            $file = $zip->statIndex($i);
            $fileName = $file['name'];

            // We skip all the files that are outside of the theme folder or on the ignore list.
            if (
                $this->checkIfPathContainsIgnoredWord($fileName) ||
                (!empty($this->parentFolderName) && mb_stripos($fileName, $this->parentFolderName) !== 0)
            ) {
                continue;
            }

            $files[] = $fileName;
        }

        return $files;
    }

    /**
     * @param string $infoFilePath
     * @return string
     */
    private function extractFolderNameBasedOnInfoFile($infoFilePath)
    {
        $pathParts = explode('/', $infoFilePath);

        if (count($pathParts) > 1) {
            return $pathParts[0];
        }

        return null;
    }

    /**
     * @param string $path
     * @return bool Path contains a to-be-ignored word.
     */
    private function checkIfPathContainsIgnoredWord($path)
    {
        foreach(self::IGNORE_LIST as $ignoreItem) {
            if (mb_stripos($path, $ignoreItem) !== false) {
                return true;
            }
        }

        return false;
    }
}
