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
    ->setName('my-config')
    ->setSeverity('warning')
    ->setReporter('json')
    ->setRuleSet(Twigcs\Ruleset\Official::class)
    ;
