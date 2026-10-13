<?php

namespace Backend\Modules\MediaLibrary\Tests\Component;

use Backend\Modules\MediaLibrary\Component\UploadHandler;
use Backend\Modules\MediaLibrary\Manager\FileManager;
use Common\ModulesSettings;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;

class UploadHandlerTest extends TestCase
{
    private const UUID = '0f8fad5b-d9cb-469f-a165-70867728950e';

    /** @var string */
    private $directory;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir() . '/upload-handler-test-' . uniqid();
        mkdir($this->directory . '/chunks/' . self::UUID, 0777, true);
        file_put_contents($this->directory . '/chunks/' . self::UUID . '/0', '<?php echo 1;');
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
    }

    public function testCombineChunksRejectsPathTraversalInUuid(): void
    {
        $result = $this->createUploadHandler('../escaped', 'image.jpg')
            ->combineChunks($this->directory . '/uploads');

        self::assertFalse($result['success']);
        self::assertFalse(file_exists($this->directory . '/escaped/image.jpg'));
    }

    public function testCombineChunksRejectsDisallowedExtension(): void
    {
        $result = $this->createUploadHandler(self::UUID, 'index.php')
            ->combineChunks($this->directory . '/uploads');

        self::assertFalse($result['success']);
        self::assertFalse(file_exists($this->directory . '/uploads/' . self::UUID . '/index.php'));
    }

    public function testCombineChunksWritesAllowedFile(): void
    {
        $result = $this->createUploadHandler(self::UUID, 'image.jpg')
            ->combineChunks($this->directory . '/uploads');

        self::assertTrue($result['success']);
        self::assertFileExists($this->directory . '/uploads/' . self::UUID . '/image.jpg');
    }

    private function createUploadHandler(string $uuid, string $fileName): UploadHandler
    {
        $request = new Request(['done' => ''], ['qquuid' => $uuid, 'qqfilename' => $fileName, 'qqtotalparts' => 1]);
        $uploadHandler = new UploadHandler($request, new FileManager($this->createMock(ModulesSettings::class)));
        $uploadHandler->allowedExtensions = ['jpg'];
        $uploadHandler->chunksFolder = $this->directory . '/chunks';

        return $uploadHandler;
    }
}
