<?php

namespace Common\Tests\Core\Header;

use Common\Core\Header\JsData;
use PHPUnit\Framework\TestCase;

class JsDataTest extends TestCase
{
    public function testInitialData(): void
    {
        $data = ['language' => 'en'];

        $this->assertContains(json_encode($data), (string) new JsData($data));
    }

    public function testJavascriptAssignment(): void
    {
        $jsData = new JsData();

        $this->assertContains('<script>var jsData = ', (string) $jsData);
        $this->assertContains('</script>', (string) $jsData);
    }

    public function testAddingData(): void
    {
        $jsData = new JsData();

        $jsData->add('Blog', 'lorem', 'ipsum');

        $this->assertContains(json_encode(['Blog' => ['lorem' => 'ipsum']]), (string) $jsData);
    }
}
