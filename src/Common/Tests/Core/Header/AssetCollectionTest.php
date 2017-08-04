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

        $asset1 = new Asset(__DIR__ . '/../../../../../js/vendors/jquery-ui.min.js', false, Priority::standard());
        $asset2 = new Asset(__DIR__ . '/../../../../../js/vendors/slick.min.js', false, Priority::module());
        $asset3 = new Asset(__DIR__ . '/../../../../../js/vendors/photoswipe.min.js', false, Priority::module());
        $asset4 = new Asset(__DIR__ . '/../../../../../js/vendors/bootstrap.min.js', false, Priority::core());
        $asset5 = new Asset(__DIR__ . '/../../../../../js/vendors/jquery.min.js', false, Priority::core());

        $assetCollection->add($asset1, false);
        $assetCollection->add($asset2, false);
        $assetCollection->add($asset3, false);
        $assetCollection->add($asset4, false);
        $assetCollection->add($asset5, false);

        $unorderedAssets = $assetCollection->getAssets();
        $this->assertEquals($asset1, $unorderedAssets[$asset1->getFile()]);
        $this->assertEquals($asset2, $unorderedAssets[$asset2->getFile()]);
        $this->assertEquals($asset3, $unorderedAssets[$asset3->getFile()]);
        $this->assertEquals($asset4, $unorderedAssets[$asset4->getFile()]);
        $this->assertEquals($asset5, $unorderedAssets[$asset5->getFile()]);

        $orderedAssets = $assetCollection->getAssets(true);
        $this->assertEquals($asset1, $orderedAssets[2]);
        $this->assertEquals($asset2, $orderedAssets[3]);
        $this->assertEquals($asset3, $orderedAssets[4]);
        $this->assertEquals($asset4, $orderedAssets[0]);
        $this->assertEquals($asset5, $orderedAssets[1]);
    }
}
