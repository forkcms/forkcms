<?php

namespace ForkCMS\Core\Ruleset;

use FriendsOfTwig\Twigcs\RegEngine\RulesetBuilder;
use FriendsOfTwig\Twigcs\RegEngine\RulesetConfigurator;
use FriendsOfTwig\Twigcs\Rule;
use FriendsOfTwig\Twigcs\Ruleset\RulesetInterface;
use FriendsOfTwig\Twigcs\Ruleset\TemplateResolverAwareInterface;
use FriendsOfTwig\Twigcs\TemplateResolver\NullResolver;
use FriendsOfTwig\Twigcs\TemplateResolver\TemplateResolverInterface;
use FriendsOfTwig\Twigcs\Validator\Violation;

class TwigcsForkRuleset implements RulesetInterface, TemplateResolverAwareInterface
{
    private int $twigMajorVersion;

    private TemplateResolverInterface $resolver;

    public function __construct(int $twigMajorVersion)
    {
        $this->twigMajorVersion = $twigMajorVersion;
        $this->resolver = new NullResolver();
    }

    public function getRules(): array
    {
        $configurator = new RulesetConfigurator();
        $configurator->setTwigMajorVersion($this->twigMajorVersion);
        $builder = new RulesetBuilder($configurator);

        return [
            new Rule\RegEngineRule(Violation::SEVERITY_ERROR, $builder->build()),
            new Rule\TrailingSpace(Violation::SEVERITY_ERROR),
            new Rule\UnusedMacro(Violation::SEVERITY_WARNING, $this->resolver),
            new Rule\UnusedVariable(Violation::SEVERITY_WARNING, $this->resolver),
        ];
    }

    public function setTemplateResolver(TemplateResolverInterface $resolver): void
    {
        $this->resolver = $resolver;
    }
}
