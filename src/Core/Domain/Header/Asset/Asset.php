<?php

namespace ForkCMS\Core\Domain\Header\Asset;

use DateTimeImmutable;
use ForkCMS\Core\Domain\Application\Application;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Extensions\Domain\Theme\Theme;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;

final class Asset
{
    public readonly DateTimeImmutable $createdOn;
    public readonly string $filePath;

    public function __construct(
        public readonly string $file,
        public readonly bool $addTimestamp = true,
        public readonly Priority $priority = Priority::STANDARD
    ) {
        $this->createdOn = new DateTimeImmutable();
        $this->filePath = $this->getFilePath();
    }

    public function compare(Asset $asset): int
    {
        $comparison = $this->priority->compare($asset->priority);

        if ($comparison !== 0) {
            return $comparison;
        }

        return $this->createdOn <=> $asset->createdOn;
    }

    public function __toString(): string
    {
        if (!$this->addTimestamp) {
            return $this->file;
        }

        if (!file_exists($this->filePath)) {
            return $this->file;
        }

        // check if we need to use a ? or &
        $separator = str_contains($this->file, '?') ? '&' : '?';

        return $this->file . $separator . 'm=' . filemtime($this->filePath);
    }

    private function getFileFromManifest(): string
    {
        static $manifestMap = [];
        if (count($manifestMap) === 0) {
            $finder = new Finder();
            try {
                $manifestFiles = $finder
                    ->path('manifest.json')
                    ->in(__DIR__ . '/../../../../../public/assets/*/*/')
                    ->files();
            } catch (DirectoryNotFoundException $e) {
                throw new RuntimeException('Did you run webpack already?', $e->getCode(), $e);
            }
            foreach ($manifestFiles as $manifestFile) {
                $manifestMap[] = json_decode($manifestFile->getContents(), true, 512, JSON_THROW_ON_ERROR);
            }

            $manifestMap = array_merge(...$manifestMap);
        }

        return $manifestMap[$this->file] ?? $this->file;
    }

    private function getFilePath(): string
    {
        static $root = null;
        static $rootRealPath = null;
        if ($root === null) {
            $root = __DIR__ . '/../../../../../';
            $rootRealPath = realpath($root);
        }

        $path = $root . 'public/' . $this->getFileFromManifest();
        if (!file_exists($path)) {
            $originalPath = $path;
            $path = preg_replace(
                '/public\/assets\/modules\/([A-Z]\w*)\/([A-Z]\w*)\/(.*)/',
                'src/Modules/$2/assets/$1/public/$3',
                $path
            );
            if ($originalPath === $path) {
                $path = preg_replace(
                    '/public\/assets\/themes\/([A-Z]\w*)\/(.*)/',
                    'src/Themes/$1/assets/public/$2',
                    $path
                );
            }
        }
        $realPath = realpath($path);
        if ($realPath === false || !str_starts_with($realPath, $rootRealPath)) {
            throw new InvalidArgumentException(
                'Did you run webpack already? ' .
                'Otherwise the file does not exist or is in a location that is not allowed: ' . $path
            );
        }

        return $realPath;
    }

    public static function forModule(
        Application $application,
        ModuleName $moduleName,
        string $file,
        bool $addTimestamp = true,
        Priority $priority = Priority::STANDARD
    ): self {
        return new self(
            'assets/modules/' . ucfirst($application->value) . '/' . $moduleName->getName() . '/' . $file,
            $addTimestamp,
            $priority
        );
    }

    public static function forTheme(
        Theme $theme,
        string $file,
        bool $addTimestamp = true,
        Priority $priority = Priority::STANDARD
    ): self {
        return new self(
            'assets/themes/' . $theme->getName() . '/' . $file,
            $addTimestamp,
            $priority
        );
    }
}
