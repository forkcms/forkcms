<?php

namespace ForkCMS\Bundle\CoreBundle\Tests\Validator;

use ForkCMS\Bundle\CoreBundle\Validator\UrlValidator;
use PHPUnit\Framework\TestCase;

class UrlValidatorTest extends TestCase
{
    public function testValidExternalUrlValidation(): void
    {
        $urlValidator = new UrlValidator();

        $urls = [
            'http://test.com/index.js',
            'https://test.com/index.js',
        ];

        foreach ($urls as $url) {
            self::assertTrue($urlValidator->isExternalUrl($url), $url . ' was not validated correctly');
        }
    }

    public function testInvalidExternalUrlValidation(): void
    {
        $urlValidator = new UrlValidator();

        $urls = [
            '/index.js',
            'index.js',
            'dev/index.js',
            '/dev/index.js',
        ];

        foreach ($urls as $url) {
            self::assertFalse($urlValidator->isExternalUrl($url), $url . ' was not validated correctly');
        }
    }
}
