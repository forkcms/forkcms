<?php

namespace Common\Tests\Core\Header;

use Common\Core\Header\Asset;
use Common\Core\Header\AssetCollection;
use Common\Core\Header\Minifier;
use Common\Core\Header\Priority;
use PHPUnit\Framework\TestCase;

class AssetCollectionTest extends TestCase
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
        self::assertEquals($asset1, $unorderedAssets[$asset1->getFile()]);
        self::assertEquals($asset2, $unorderedAssets[$asset2->getFile()]);
        self::assertEquals($asset3, $unorderedAssets[$asset3->getFile()]);
        self::assertEquals($asset4, $unorderedAssets[$asset4->getFile()]);
        self::assertEquals($asset5, $unorderedAssets[$asset5->getFile()]);

        $orderedAssets = $assetCollection->getAssets(true);
        self::assertEquals($asset1, $orderedAssets[2]);
        self::assertEquals($asset2, $orderedAssets[3]);
        self::assertEquals($asset3, $orderedAssets[4]);
        self::assertEquals($asset4, $orderedAssets[0]);
        self::assertEquals($asset5, $orderedAssets[1]);
    }
}
