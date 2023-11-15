<?php

declare(strict_types=1);

use FriendsOfTwig\Twigcs;

return Twigcs\Config\Config::create()
    ->addFinder(
        Twigcs\Finder\TemplateFinder::create()->in(__DIR__ . '/src')
    )
    ->addFinder(
        Twigcs\Finder\TemplateFinder::create()->in(__DIR__ . '/templates')
    )
    ->setName('Fork CMS config')
    ->setSeverity('error')
    ->setReporter('console')
    ->setRuleSet(ForkCMS\Core\Ruleset\TwigcsForkRuleset::class)
    ->setDisplay('blocking');
