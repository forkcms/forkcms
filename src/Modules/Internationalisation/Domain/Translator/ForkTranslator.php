<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Translator;

use BadMethodCallException;
use ForkCMS\Core\Domain\Application\Application;
use ForkCMS\Modules\Backend\Domain\Action\ActionSlug;
use ForkCMS\Modules\Backend\Domain\AjaxAction\AjaxActionSlug;
use ForkCMS\Modules\Backend\Domain\User\User;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationDomain;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Translation\Formatter\MessageFormatterInterface;
use ValueError;

/** This class will make sure that the domain is set correctly */
final class ForkTranslator extends Translator
{
    private ?TranslationDomain $defaultTranslationDomain = null;

    /** @var ?string used for debug reasons */
    private ?string $lastUsedDomain;

    private ?string $fallbackLocale = null;

    /**
     * @param array<string, array<int, string>>$loaderIds
     * @param array<string, mixed>$options
     * @param string[] $enabledLocales
     */
    public function __construct(
        ContainerInterface $container,
        MessageFormatterInterface $formatter,
        string $defaultLocale,
        array $loaderIds = [],
        array $options = [],
        array $enabledLocales = [],
        private readonly ?Security $security = null,
        private readonly ?RequestStack $requestStack = null,
    ) {
        parent::__construct($container, $formatter, $defaultLocale, $loaderIds, $options, $enabledLocales);
    }

    /** @param array<string, mixed> $parameters */
    public function trans(?string $id, array $parameters = [], string $domain = null, string $locale = null): string
    {
        $isValidator = $domain === 'validators';
        if ($isValidator) {
            $domain = null;
        }

        if ($this->fallbackLocale === null) {
            $user = $this->security?->getUser();
            $this->fallbackLocale = ($user instanceof User ? $user->getSetting('locale') : null) ?? $this->getLocale();
        }
        $locale = $locale ?? $this->fallbackLocale;

        if (!$this->requestStack instanceof RequestStack) {
            return $this->getTranslationAndStoreDomain($id, $parameters, $domain, $locale);
        }

        if ($this->defaultTranslationDomain === null) {
            $this->defaultTranslationDomain = $this->determineDefaultTranslationDomain();
        }

        $domain ??= $this->defaultTranslationDomain->getDomain();

        $translated = $this->getTranslationAndStoreDomain($id, $parameters, $domain, $locale);

        if ($translated !== $id) {
            return $translated;
        }

        try {
            $fallbackDomain = TranslationDomain::fromDomain($domain)->getFallback();
        } catch (ValueError | InvalidArgumentException | BadMethodCallException) {
            // Not a fork translation domain or no fallback available
            if ($isValidator) {
                return $this->getTranslationAndStoreDomain($id, $parameters, 'validator', $locale);
            }

            return $translated;
        }

        if ($fallbackDomain === null) {
            if ($isValidator) {
                return $this->getTranslationAndStoreDomain($id, $parameters, 'validator', $locale);
            }

            return $translated;
        }

        $domain = $fallbackDomain->getDomain();

        // use the fallback of the application
        $translated = $this->getTranslationAndStoreDomain($id, $parameters, $domain, $locale, false);

        if ($translated !== $id || !$isValidator) {
            return $translated;
        }

        return $this->getTranslationAndStoreDomain($id, $parameters, 'validator', $locale);
    }

    public function setDefaultTranslationDomain(TranslationDomain $defaultTranslationDomain): void
    {
        $this->defaultTranslationDomain = $defaultTranslationDomain;
    }

    public function getDefaultTranslationDomain(): TranslationDomain
    {
        if ($this->defaultTranslationDomain === null) {
            $this->defaultTranslationDomain = $this->determineDefaultTranslationDomain();
        }

        return $this->defaultTranslationDomain;
    }

    public function getLastUsedDomain(): ?string
    {
        return $this->lastUsedDomain;
    }

    /** @param array<string, mixed> $parameters */
    private function getTranslationAndStoreDomain(
        ?string $id,
        array $parameters = [],
        string $domain = null,
        string $locale = null,
        bool $storeDomainIfTranslationWasNotFound = true
    ): string {
        $translated = parent::trans($id, $parameters, $domain, $locale);

        if ($storeDomainIfTranslationWasNotFound || $id !== $translated) {
            $this->lastUsedDomain = $domain;
        }

        return $translated;
    }

    private function determineDefaultTranslationDomain(): TranslationDomain
    {
        $mainRequest = $this->requestStack->getMainRequest();
        if ($mainRequest instanceof Request) {
            if ($mainRequest->attributes->has('_locale_application')) {
                $application = Application::tryFrom($mainRequest->attributes->get('_locale_application'));
                if ($application instanceof Application) {
                    return new TranslationDomain($application);
                }
            }
            return match ($mainRequest->get('_route')) {
                'backend_action',
                'backend_login' => ActionSlug::fromRequest($mainRequest)->getTranslationDomain(),
                'backend_2fa_login' => ActionSlug::fromRequest($mainRequest)->getTranslationDomain(),
                'backend_2fa_login_check' => ActionSlug::fromRequest($mainRequest)->getTranslationDomain(),
                'backend_ajax' => AjaxActionSlug::fromRequest($mainRequest)->getTranslationDomain(),
                default => new TranslationDomain(Application::FRONTEND),
            };
        }

        return new TranslationDomain(Application::CONSOLE);
    }
}
