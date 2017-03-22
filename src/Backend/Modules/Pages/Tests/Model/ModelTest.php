<?php

namespace Backend\Modules\Pages\Tests\Model;

use Backend\Modules\Pages\Engine\Model;
use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
    public function testUrlIsEncoded()
    {
        self::assertEquals(
            'http://www.google.be/Quote',
            Model::getEncodedRedirectURL('http://www.google.be/Quote')
        );
        self::assertEquals(
            'http://www.google.be/Quote%22HelloWorld%22',
            Model::getEncodedRedirectURL('http://www.google.be/Quote"HelloWorld"')
        );
        self::assertEquals(
            'http://www.google.be/Quote%27HelloWorld%27',
            Model::getEncodedRedirectURL("http://www.google.be/Quote'HelloWorld'")
        );
        self::assertEquals(
            'http://cédé.be/Quote%22HelloWorld%22',
            Model::getEncodedRedirectURL('http://cédé.be/Quote"HelloWorld"')
        );
    }
}
