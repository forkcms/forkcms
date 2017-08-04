<?php

namespace Backend\Core\Tests\Engine;

use Backend\Core\Engine\DataGridFunctions;
use PHPUnit\Framework\TestCase;

class DataGridFunctionsTest extends TestCase
{
    public function testShowImage(): void
    {
        $path = '/src/Frontend/Files/Media/Backend/01';
        $imageName = 'image-01.jpg';
        $title = 'cowboy henk';
        $url = 'http://www.test.com/private/nl/edit?id=1';
        $width = 100;
        $height = 100;

        self::assertEquals(
            '<a href="' . $url . '" title="' . $title . '"><img src="' . $path . '/' . $imageName . '" alt="' . $title . '" width="' . $width . '" height="' . $height . '" /></a>',
            DataGridFunctions::showImage(
                $path,
                $imageName,
                $title,
                $url,
                $width,
                $height
            )
        );

        self::assertEquals(
            '<img src="' . $path . '/' . $imageName . '" alt="' . $title . '" />',
            DataGridFunctions::showImage(
                $path,
                $imageName,
                $title
            )
        );
    }
}
