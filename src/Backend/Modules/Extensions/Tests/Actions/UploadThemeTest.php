<?php

namespace Backend\Modules\Extensions\Tests\Actions;

use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\Filesystem\Filesystem;
use ZipArchive;

class UploadThemeTest extends BackendWebTestCase
{
    private const THEME_NAME = 'Fork Test Theme';
    private const URL_UPLOAD_THEME = '/private/en/extensions/upload_theme';
    private const URL_THEMES_INDEX = '/private/en/extensions/themes';

    /**
     * @var string
     */
    private $fileName;

    public function testAuthenticationIsNeeded(Client $client): void
    {
        self::assertAuthenticationIsNeeded($client, self::URL_UPLOAD_THEME);
    }

    public function testUploadPage(Client $client): void
    {
        $this->login($client);

        self::assertPageLoadedCorrectly(
            $client,
            self::URL_UPLOAD_THEME,
            [
                'Install',
                '<label for="file" class="control-label">',
            ]
        );
    }

    /**
     * Test that we cannot upload a theme without info.xml file
     */
    public function testUploadThemeZipWithoutInfoFile(Client $client): void
    {
        // Generate zip with no info.xml
        $archive = $this->createZip();
        $archive->close();

        $this->submitThemeUploadForm($client);

        // We should get a 200 and show an error.
        self::assertIs200($client);
        self::assertResponseHasContent($client->getResponse(), 'We could not find an info.xml');
    }

    /**
     * Test if we can upload a theme with a zip that contains a subfolder containing the themefiles.
     */
    public function testUploadThemeZipGithub(Client $client): void
    {
        $archive = $this->createZip();
        $archive->addFromString(self::THEME_NAME . '/info.xml', $this->getSampleInfoXmlContents(self::THEME_NAME));
        $archive->close();

        $this->submitThemeUploadForm($client);
        $client->followRedirect();

        self::assertIs200($client);
        self::assertCurrentUrlContains($client, self::URL_THEMES_INDEX);
        self::assertResponseHasContent($client->getResponse(), self::THEME_NAME);
    }

    /**
     * Test if we can upload a theme with a zip that contains only the files (not wrapped in a parent folder).
     */
    public function testUploadThemeNoParentFolder(Client $client): void
    {
        $archive = $this->createZip();
        $archive->addFromString('info.xml', $this->getSampleInfoXmlContents(self::THEME_NAME));
        $archive->close();

        $this->submitThemeUploadForm($client);
        $client->followRedirect();

        // We should get a 200 and be redirected to the themes index page.
        self::assertIs200($client);
        self::assertCurrentUrlContains($client, self::URL_THEMES_INDEX);
        self::assertResponseHasContent($client->getResponse(), self::THEME_NAME);
    }

    protected function tearDown(): void
    {
        $fs = new Filesystem();

        // Clear the $_FILES
        $_FILES = [];

        // Remove the generated zip file
        if ($this->fileName !== null && file_exists("{$this->fileName}.zip")) {
            $fs->remove("{$this->fileName}.zip");
        }

        // Remove the uploaded theme folder
        $fs->remove(FRONTEND_PATH . '/Themes/' . self::THEME_NAME);

        parent::tearDown();
    }

    /**
     * @param string $themeName
     *
     * @return string
     */
    private function getSampleInfoXmlContents(string $themeName): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?><theme><name>' . $themeName . '</name><version>1.0.0</version><requirements><minimum_version>4.0.0</minimum_version></requirements><thumbnail>thumbnail.png</thumbnail><description><![CDATA[Fork CMS Test]]></description><authors><author><name>Fork CMS</name><url>http://www.fork-cms.com</url></author></authors><metanavigation supported="false" /><templates></templates></theme>';
    }

    private function submitThemeUploadForm(Client $client): void
    {
        $this->login($client);
        $client->request('GET', self::URL_UPLOAD_THEME);
        $form = $this->getFormForSubmitButton($client, 'Install');

        $_FILES['file'] = [
            'name' => "{$this->fileName}.zip",
            'type' => 'application/zip',
            'tmp_name' => "{$this->fileName}.zip",
            'error' => 0,
            'size' => 0,
        ];

        $this->submitEditForm($client, $form, ['form' => 'upload']);
    }

    private function createZip(): ZipArchive
    {
        $this->fileName = tempnam(sys_get_temp_dir(), 'Theme');
        $filePath = $this->fileName . '.zip';
        $archive = new ZipArchive();
        $archive->open($filePath, ZipArchive::CREATE);
        $archive->addEmptyDir(self::THEME_NAME);

        if (file_exists($archive->filename)) {
            throw new FileNotFoundException('Could not create zip file with theme');
        }

        return $archive;
    }
}
