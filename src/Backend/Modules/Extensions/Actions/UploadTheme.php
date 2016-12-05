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

    private $ignoreList = ['__MACOSX'];

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
        // Call parent, this will probably add some general CSS/JS or other required files
        parent::execute();

        // Zip extension is required for theme upload
        if (!extension_loaded('zlib')) {
            $this->tpl->assign('zlibIsMissing', true);
        }

        if (!$this->isWritable()) {
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
        // The form is submitted
        if (!$this->frm->isSubmitted()) {
            return;
        }

        /** @var $fileFile \SpoonFormFile */
        $fileFile = $this->frm->getField('file');
        $zip = null;
        $zipFiles = null;

        // Validate the file. Check if the file field is filled and if it's a zip.
        if ($fileFile->isFilled(BL::err('FieldIsRequired')) &&
            $fileFile->isAllowedExtension(array('zip'), sprintf(BL::getError('ExtensionNotAllowed'), 'zip'))
        ) {
            // Create ziparchive instance
            $zip = new ZipArchive();

            // Try and open it
            if ($zip->open($fileFile->getTempFileName()) === true) {
                // zip file needs to contain some files
                if ($zip->numFiles > 0) {
                    $infoXml = $this->findInfoFileInZip($zip);

                    // Throw error if info.xml is not found
                    if ($infoXml === null) {
                        return $fileFile->addError(
                            sprintf(BL::getError('NoInformationFile'), $fileFile->getFileName())
                        );
                    }

                    // Parse xml
                    try {
                        // Load info.xml
                        $infoXml = @new \SimpleXMLElement($infoXml, LIBXML_NOCDATA, false);

                        // Convert xml to useful array
                        $this->info = BackendExtensionsModel::processThemeXml($infoXml);

                        // Empty data (nothing useful)
                        if (empty($this->info)) {
                            return $fileFile->addError(BL::getMessage('InformationFileIsEmpty'));
                        }

                        // Define the theme name, based on the info.xml file.
                        $this->themeName = $this->info['name'];
                    } catch (Exception $e) {
                        // Warning that the information file is corrupt
                        return $fileFile->addError(BL::getMessage('InformationFileCouldNotBeLoaded'));
                    }

                    // Wow wow, you are trying to upload an already existing theme
                    if (BackendExtensionsModel::existsTheme($this->themeName)) {
                        return $fileFile->addError(sprintf(BL::getError('ThemeAlreadyExists'), $this->themeName));
                    }

                    $zipFiles = $this->getValidatedFilesList($zip);
                } else {
                    // Empty zip file
                    $fileFile->addError(BL::getError('FileIsEmpty'));
                }
            } else {
                // Something went very wrong, probably corrupted
                return $fileFile->addError(BL::getError('CorruptedFile'));
            }
        }

        // Passed all validation
        if ($this->frm->isCorrect() && $zip !== null) {
            // Unpack the zip. If the files were not found inside a parent directory, we create the theme directory.
            $themePath = FRONTEND_PATH . '/Themes';
            if ($this->parentFolderName === null) {
                $themePath .= "/{$this->themeName}";
            }
            $zip->extractTo($themePath, $zipFiles);

            // Rename the original name of the parent folder from the zip to the correct theme foldername.
            $fs = new Filesystem();
            $parentZipFolderPath = $themePath . '/' . $this->parentFolderName;
            if ($this->parentFolderName !== $this->themeName &&
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
            if ($this->checkIfPathContainsIgnoredWord($fileName) ||
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
        foreach ($this->ignoreList as $ignoreItem) {
            if (mb_stripos($path, $ignoreItem) !== false) {
                return true;
            }
        }

        return false;
    }
}
