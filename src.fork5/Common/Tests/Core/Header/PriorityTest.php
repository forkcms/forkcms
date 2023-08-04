<?php

namespace Common\Tests\Core\Header;

use Common\Core\Header\Priority;
use PHPUnit\Framework\TestCase;

class PriorityTest extends TestCase
{
    public function testPriorityEquals(): void
    {
        self::assertTrue(Priority::widget()->equals(Priority::widget()));
    }

    public function testPriorityNotEquals(): void
    {
        self::assertFalse(Priority::widget()->equals(Priority::core()));
    }

    public function testPriorityComparison(): void
    {
        $priorities = [
            Priority::debug(),
            Priority::standard(),
            Priority::widget(),
            Priority::core(),
            Priority::module(),
        ];

        $sortedPriorities = [
            Priority::core(),
            Priority::standard(),
            Priority::module(),
            Priority::widget(),
            Priority::debug(),
        ];

        usort(
            $priorities,
            function (Priority $priority1, Priority $priority2) {
                return $priority1->compare($priority2);
            }
        );

        foreach ($sortedPriorities as $key => $priority) {
            self::assertTrue($priorities[$key]->equals($priority), 'Priorities not sorted correctly');
        }
    }

    public function testPriorityForModule(): void
    {
        self::assertTrue(Priority::core()->equals(Priority::forModule('Core')));
        self::assertTrue(Priority::module()->equals(Priority::forModule('Blog')));
        self::assertTrue(Priority::module()->equals(Priority::forModule('ContentBlocks')));
    }
}
