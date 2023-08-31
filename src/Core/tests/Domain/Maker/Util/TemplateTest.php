<?php

namespace ForkCMS\Core\tests\Domain\Maker\Util;

use ForkCMS\Core\Domain\Maker\Util\Template;
use PHPUnit\Framework\TestCase;

class TemplateTest extends TestCase
{
    public function testGetPath(): void
    {
        $this->assertSame(
           'src/Core/templates/Maker/Entity.tpl.php',
            Template::getPath('Entity.tpl.php')
        );
    }
}
