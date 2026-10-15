<?php

namespace Backend\Modules\Pages\Tests\Ajax;

use Backend\Core\Tests\BackendWebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Regression test for the stored-XSS vulnerability (CWE-79) in the Pages/UploadFile AJAX action: an uploaded svg
 * is stored under the web root and served from the site origin as an active document, so the stored bytes may
 * never contain a script element or an event handler.
 */
class UploadFileTest extends BackendWebTestCase
{
    private const AJAX_URL = '/backend/ajax';
    private const UPLOAD_TYPE = 'UserTemplate';

    /** @var string[] */
    private array $writtenFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->writtenFiles as $writtenFile) {
            @unlink($writtenFile);
        }
        $this->writtenFiles = [];

        parent::tearDown();
    }

    public function testUploadedSvgIsStrippedOfItsActiveContent(KernelBrowser $client): void
    {
        $this->login($client);

        // the payload from the security report
        $response = $this->uploadFile(
            $client,
            'up-xss.svg',
            '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="60"'
            . ' onload="document.documentElement.setAttribute(\'data-xss\', document.domain)">'
            . '<script>document.documentElement.setAttribute("data-xss-script", document.domain)</script>'
            . '<text x="5" y="30">svg</text>'
            . '</svg>'
        );

        self::assertSame(200, $response->getStatusCode());

        $payload = json_decode($response->getContent(), true);
        self::assertSame(200, $payload['code']);

        $storedContents = $this->readStoredFile($payload['data']);

        self::assertStringNotContainsStringIgnoringCase('<script', $storedContents);
        self::assertStringNotContainsStringIgnoringCase('onload', $storedContents);
        self::assertStringNotContainsStringIgnoringCase('document.domain', $storedContents);

        // the drawing itself has to survive, an svg upload is a supported feature
        self::assertStringContainsString('<text', $storedContents);
        self::assertStringContainsString('width="200"', $storedContents);
    }

    public function testUploadedSvgIsSanitisedRegardlessOfTheExtensionCasing(KernelBrowser $client): void
    {
        $this->login($client);

        $response = $this->uploadFile(
            $client,
            'up-xss.SVG',
            '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script><text>svg</text></svg>'
        );

        self::assertSame(200, $response->getStatusCode());

        $payload = json_decode($response->getContent(), true);
        self::assertSame(200, $payload['code']);

        self::assertStringNotContainsStringIgnoringCase('<script', $this->readStoredFile($payload['data']));
    }

    /**
     * The extension allowlist and the mime type are never checked against each other, so an svg renamed to ".png"
     * is an accepted upload. The stored bytes stay an active document, so they still have to be sanitized.
     */
    public function testUploadedSvgIsSanitisedWhenItIsNamedAfterAnotherImageType(KernelBrowser $client): void
    {
        $this->login($client);

        $response = $this->uploadFile(
            $client,
            'up-xss.png',
            '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="60" onload="alert(1)">'
            . '<script>alert(2)</script><text x="5" y="30">svg</text></svg>'
        );

        self::assertSame(200, $response->getStatusCode());

        $payload = json_decode($response->getContent(), true);
        self::assertSame(200, $payload['code']);

        $storedContents = $this->readStoredFile($payload['data']);

        self::assertStringNotContainsStringIgnoringCase('<script', $storedContents);
        self::assertStringNotContainsStringIgnoringCase('onload', $storedContents);
        self::assertStringContainsString('<text', $storedContents);
    }

    public function testNonImageExtensionsAreStillRejected(KernelBrowser $client): void
    {
        $this->login($client);

        $response = $this->uploadFile($client, 'payload.html', '<script>alert(1)</script>');

        self::assertSame(400, $response->getStatusCode());
    }

    private function readStoredFile(string $fileName): string
    {
        $path = FRONTEND_FILES_PATH . '/Pages/' . self::UPLOAD_TYPE . '/' . $fileName;
        $this->writtenFiles[] = $path;

        self::assertFileExists($path);

        return file_get_contents($path);
    }

    private function uploadFile(KernelBrowser $client, string $fileName, string $contents)
    {
        $temporaryPath = tempnam(sys_get_temp_dir(), 'fork_upload_');
        file_put_contents($temporaryPath, $contents);

        // render a backend page so a csrf token is present in the session
        $client->request('GET', '/private/en/dashboard');
        $token = $client->getRequest()->getSession()->get('csrf_token');

        $client->request(
            'POST',
            self::AJAX_URL,
            [
                'fork' => ['module' => 'Pages', 'action' => 'UploadFile', 'language' => 'en'],
                'type' => self::UPLOAD_TYPE,
            ],
            ['file' => new UploadedFile($temporaryPath, $fileName, 'image/svg+xml', null, true)],
            [
                'HTTP_X_CSRF_TOKEN' => $token,
                'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
            ]
        );

        @unlink($temporaryPath);

        return $client->getResponse();
    }
}
