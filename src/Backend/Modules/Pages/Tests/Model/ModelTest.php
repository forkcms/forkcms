<?php

namespace Backend\Modules\Pages\Tests\Model;

use Backend\Modules\Pages\Engine\Model;

class ModelTest extends \PHPUnit_Framework_TestCase
{
    public function testUrlIsEncoded()
    {
        $this->assertEquals(
            'http://www.google.be/Quote',
            Model::getEncodedRedirectURL('http://www.google.be/Quote')
        );
        $this->assertEquals(
            'http://www.google.be/Quote%22HelloWorld%22',
            Model::getEncodedRedirectURL('http://www.google.be/Quote"HelloWorld"')
        );
        $this->assertEquals(
            'http://www.google.be/Quote%27HelloWorld%27',
            Model::getEncodedRedirectURL("http://www.google.be/Quote'HelloWorld'")
        );
        $this->assertEquals(
            'http://cédé.be/Quote%22HelloWorld%22',
            Model::getEncodedRedirectURL('http://cédé.be/Quote"HelloWorld"')
        );
    }
}
