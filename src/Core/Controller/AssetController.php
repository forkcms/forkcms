<?php

namespace ForkCMS\Core\Controller;

use ForkCMS\Core\Domain\Application\Application;
use ForkCMS\Core\Domain\Application\ApplicationResolver;
use ForkCMS\Core\Domain\Header\Asset\Asset;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleNameResolver;
use ForkCMS\Modules\Extensions\Domain\Theme\ThemeRepository;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class AssetController
{
    public function __construct(private readonly ThemeRepository $themeRepository)
    {
    }

    public function __invoke(
        #[ValueResolver(ModuleNameResolver::class)]
        ?ModuleName $moduleName,
        #[ValueResolver(ApplicationResolver::class)]
        ?Application $application,
        string $path
    ): Response {
        try {
            if ($moduleName === null || $application === null) {
                $asset = Asset::forTheme($this->themeRepository->getActiveTheme(), $path);
            } else {
                $asset = Asset::forModule($application, $moduleName, $path);
            }
        } catch (InvalidArgumentException $invalidArgumentException) {
            throw new NotFoundHttpException('File not found', $invalidArgumentException);
        }

        return new BinaryFileResponse($asset->filePath);
    }
}
