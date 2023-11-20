<?php

namespace ForkCMS\Modules\Extensions\tests;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;

abstract class ForkFixture extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return [ModuleName::fromFQCN(static::class)->getName()];
    }
}
