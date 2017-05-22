<?php

namespace Common\Tests\Core\Header;

use Common\Core\Header\Asset;
use Common\Core\Header\AssetCollection;
use Common\Core\Header\Minifier;
use Common\Core\Header\Priority;

class AssetCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testAddAssetToCollection(): void
    {
        $assetCollection = new AssetCollection(Minifier::js(__DIR__, __DIR__, __DIR__));

        $asset = new Asset(__DIR__ . '/../../../../../js/vendors/bootstrap.min.js', false, Priority::core());
        $asset2 = new Asset(__DIR__ . '/../../../../../js/vendors/jquery-ui.min.js', false, Priority::standard());
        $asset3 = new Asset(__DIR__ . '/../../../../../js/vendors/jquery.min.js', false, Priority::core());

        $assetCollection->add($asset, false);
        $assetCollection->add($asset2, false);
        $assetCollection->add($asset3, false);

        $unorderedAssets = $assetCollection->getAssets();
        $this->assertEquals($asset, $unorderedAssets[$asset->getFile()]);
        $this->assertEquals($asset2, $unorderedAssets[$asset2->getFile()]);
        $this->assertEquals($asset3, $unorderedAssets[$asset3->getFile()]);

        $orderedAssets = $assetCollection->getAssets(true);
        $this->assertEquals($asset, $orderedAssets[0]);
        $this->assertEquals($asset2, $orderedAssets[2]);
        $this->assertEquals($asset3, $orderedAssets[1]);
    }
}
