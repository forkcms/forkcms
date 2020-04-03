<?php

namespace Common\Tests\Core\Header;

use Common\Core\Header\JsData;
use PHPUnit\Framework\TestCase;

class JsDataTest extends TestCase
{
    public function testInitialData(): void
    {
        $data = ['language' => 'en'];

        self::assertContains(json_encode($data), (string) new JsData($data));
    }

    public function testJavascriptAssignment(): void
    {
        $jsData = new JsData();

        self::assertContains('<script>var jsData = ', (string) $jsData);
        self::assertContains('</script>', (string) $jsData);
    }

    public function testAddingData(): void
    {
        $jsData = new JsData();

        $jsData->add('Blog', 'lorem', 'ipsum');

        self::assertContains(json_encode(['Blog' => ['lorem' => 'ipsum']]), (string) $jsData);
    }
}
