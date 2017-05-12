<?php

namespace Common\Tests\Core\Header;

use Common\Core\Header\Asset;
use Common\Core\Header\AssetCollection;
use Common\Core\Header\Minifier;

class AssetCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testAddAssetToCollection(): void
    {
        $assetCollection = new AssetCollection(Minifier::js(__DIR__, __DIR__, __DIR__));

        $asset = new Asset(__DIR__ . '/../../../../../js/vendors/jquery.min.js');

        $assetCollection->add($asset, false);
        $this->assertContains($asset, $assetCollection->getAssets());
    }
}
