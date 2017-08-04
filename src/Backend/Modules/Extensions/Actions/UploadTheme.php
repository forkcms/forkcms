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

    public function execute(): void
    {
        // Call parent, this will probably add some general CSS/JS or other required files
        parent::execute();

        // Zip extension is required for theme upload
        if (!extension_loaded('zlib')) {
            $this->template->assign('zlibIsMissing', true);
        }

        if (!$this->isWritable()) {
            // we need write rights to upload files
            $this->template->assign('notWritable', true);
        } else {
            // everything allright, we can upload
            $this->buildForm();
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
    private function isWritable(): bool
    {
        return BackendExtensionsModel::isWritable(FRONTEND_PATH . '/Themes');
    }

    private function buildForm(): void
    {
        // create form
        $this->form = new BackendForm('upload');

        // create and add elements
        $this->form->addFile('file');
    }

    private function validateForm(): void
    {
        // The form is submitted
        if (!$this->form->isSubmitted()) {
            return;
        }

        /** @var $fileFile \SpoonFormFile */
        $fileFile = $this->form->getField('file');
        $zip = null;
        $zipFiles = null;

        // Validate the file. Check if the file field is filled and if it's a zip.
        if ($fileFile->isFilled(BL::err('FieldIsRequired')) &&
            $fileFile->isAllowedExtension(['zip'], sprintf(BL::getError('ExtensionNotAllowed'), 'zip'))
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
                        $fileFile->addError(
                            sprintf(BL::getError('NoInformationFile'), $fileFile->getFileName())
                        );

                        return;
                    }

                    // Parse xml
                    try {
                        // Load info.xml
                        $infoXml = @new \SimpleXMLElement($infoXml, LIBXML_NOCDATA, false);

                        // Convert xml to useful array
                        $this->info = BackendExtensionsModel::processThemeXml($infoXml);

                        // Empty data (nothing useful)
                        if (empty($this->info)) {
                            $fileFile->addError(BL::getMessage('InformationFileIsEmpty'));

                            return;
                        }

                        // Define the theme name, based on the info.xml file.
                        $this->themeName = $this->info['name'];
                    } catch (Exception $e) {
                        // Warning that the information file is corrupt
                        $fileFile->addError(BL::getMessage('InformationFileCouldNotBeLoaded'));

                        return;
                    }

                    // Wow wow, you are trying to upload an already existing theme
                    if (BackendExtensionsModel::existsTheme($this->themeName)) {
                        $fileFile->addError(sprintf(BL::getError('ThemeAlreadyExists'), $this->themeName));

                        return;
                    }

                    $zipFiles = $this->getValidatedFilesList($zip);
                } else {
                    // Empty zip file
                    $fileFile->addError(BL::getError('FileIsEmpty'));
                }
            } else {
                // Something went very wrong, probably corrupted
                $fileFile->addError(BL::getError('CorruptedFile'));

                return;
            }
        }

        // Passed all validation
        if ($zip !== null && $this->form->isCorrect()) {
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
            $this->redirect(
                BackendModel::createUrlForAction('Themes') . '&report=theme-installed&var=' . $this->themeName
            );
        }
    }

    /**
     * Two ideal situations possible: we have a zip with files including info.xml, or we have a zip with the theme-folder.
     *
     * @param ZipArchive $zip
     *
     * @return string|null
     */
    private function findInfoFileInZip(ZipArchive $zip): ?string
    {
        for ($i = 0; $i < $zip->numFiles; ++$i) {
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
     *
     * @return string[]
     */
    private function getValidatedFilesList(ZipArchive $zip): array
    {
        $this->parentFolderName = $this->extractFolderNameBasedOnInfoFile($this->infoFilePath);

        // Check every file in the zip
        $files = [];
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
     *
     * @return string|null
     */
    private function extractFolderNameBasedOnInfoFile(string $infoFilePath): ?string
    {
        $pathParts = explode('/', $infoFilePath);

        if (count($pathParts) > 1) {
            return $pathParts[0];
        }

        return null;
    }

    /**
     * @param string $path contains a to-be-ignored word.
     *
     * @return bool
     */
    private function checkIfPathContainsIgnoredWord(string $path): bool
    {
        foreach ($this->ignoreList as $ignoreItem) {
            if (mb_stripos($path, $ignoreItem) !== false) {
                return true;
            }
        }

        return false;
    }
}
