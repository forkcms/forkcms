<?php

namespace ForkCMS\Bundle\CoreBundle\Tests\Validator;

use ForkCMS\Bundle\CoreBundle\Validator\UrlValidator;
use PHPUnit\Framework\TestCase;

class UrlValidatorTest extends TestCase
{
    public function testExternalUrlValidation()
    {
        $urlValidator = new UrlValidator();

        $urls = [
            'http://test.com/index.js' => true,
            'https://test.com/index.js' => true,
            '/index.js' => false,
            'index.js' => false,
            'dev/index.js' => false,
            '/dev/index.js' => false,
        ];

        foreach ($urls as $url => $isExternal) {
            $this->assertEquals($isExternal, $urlValidator->isExternalUrl($url), $url . ' was not validated correctly');
        }
    }
}
