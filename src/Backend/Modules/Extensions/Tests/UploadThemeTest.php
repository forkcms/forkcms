<?php

namespace Backend\Modules\Extensions\Tests;

use Common\WebTestCase;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\Filesystem\Filesystem;
use ZipArchive;

/**
 * Class UploadThemeTest
 */
class UploadThemeTest extends WebTestCase
{
    const THEME_NAME = 'Fork Test Theme';
    const URL_UPLOAD_THEME = '/private/en/extensions/upload_theme';
    const URL_THEMES_INDEX = '/private/en/extensions/themes';

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var Client
     */
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->logout($this->client);
        $this->client->setMaxRedirects(1);
        $this->client->request('GET', self::URL_UPLOAD_THEME);

        $this->login($this->client);

        $this->client->request('GET', self::URL_UPLOAD_THEME);
    }

    /**
     * Test that we cannot upload a theme without info.xml file
     */
    public function testUploadThemeZipWithoutInfoFile(): void
    {
        // Generate zip with no info.xml
        $this->fileName = tempnam(sys_get_temp_dir(), 'Theme');
        $filePath = $this->fileName . '.zip';
        $archive = new ZipArchive();
        $archive->open($filePath, ZipArchive::CREATE);
        $archive->addEmptyDir($this->fileName);
        $archive->close();

        if (file_exists($archive->filename)) {
            throw new FileNotFoundException('Could not create zip file with theme');
        }

        $this->submitThemeUploadForm();

        // We should get a 200 and show an error.
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('We could not find an info.xml', $this->client->getResponse()->getContent());
    }

    /**
     * Test if we can upload a theme with a zip that contains a subfolder containing the themefiles.
     */
    public function testUploadThemeZipGithub(): void
    {
        // Generate zip with no info.xml
        $this->fileName = tempnam(sys_get_temp_dir(), 'Theme');
        $filePath = $this->fileName . '.zip';
        $baseName = self::THEME_NAME;
        $archive = new ZipArchive();
        $archive->open($filePath, ZipArchive::CREATE);
        $archive->addEmptyDir($baseName);
        $archive->addFromString("$baseName/info.xml", $this->getSampleInfoXmlContents($baseName));
        $archive->close();

        if (file_exists($archive->filename)) {
            throw new FileNotFoundException('Could not create zip file with theme');
        }

        $this->submitThemeUploadForm();

        // We should get a 200 and be redirected to the themes index page.
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains(self::URL_THEMES_INDEX, $this->client->getHistory()->current()->getUri());
        self::assertContains(self::THEME_NAME, $this->client->getResponse()->getContent());
    }

    /**
     * Test if we can upload a theme with a zip that contains only the files (not wrapped in a parent folder).
     */
    public function testUploadThemeNoParentFolder(): void
    {
        // Generate zip with no info.xml
        $this->fileName = tempnam(sys_get_temp_dir(), 'Theme');
        $filePath = $this->fileName . '.zip';
        $baseName = self::THEME_NAME;
        $archive = new ZipArchive();
        $archive->open($filePath, ZipArchive::CREATE);
        $archive->addFromString('info.xml', $this->getSampleInfoXmlContents($baseName));
        $archive->close();

        if (file_exists($archive->filename)) {
            throw new FileNotFoundException('Could not create zip file with theme');
        }

        $this->submitThemeUploadForm();

        // We should get a 200 and be redirected to the themes index page.
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains(self::URL_THEMES_INDEX, $this->client->getHistory()->current()->getUri());
        self::assertContains(self::THEME_NAME, $this->client->getResponse()->getContent());
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

    private function submitThemeUploadForm(): void
    {
        $form = $this->client->getCrawler()->selectButton('Install')->form();

        $_FILES['file'] = [
            'name' => "{$this->fileName}.zip",
            'type' => 'application/zip',
            'tmp_name' => "{$this->fileName}.zip",
            'error' => 0,
            'size' => 0,
        ];

        $this->submitEditForm($this->client, $form, ['form' => 'upload']);
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

        $this->logout($this->client);
        parent::tearDown();
    }
}
