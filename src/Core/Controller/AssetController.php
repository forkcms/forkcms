<?php

namespace ForkCMS\Core\Controller;

use ForkCMS\Core\Domain\Application\Application;
use ForkCMS\Core\Domain\Header\Asset\Asset;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class AssetController
{
    public function __invoke(ModuleName $moduleName, Application $application, string $path): Response
    {
        try {
            $asset = Asset::forModule($application, $moduleName, $path);
        } catch (InvalidArgumentException $invalidArgumentException) {
            throw new NotFoundHttpException('File not found', $invalidArgumentException);
        }

        return new Response(file_get_contents($asset->filePath));
    }
}
