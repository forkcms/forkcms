<?php

namespace ForkCMS\Core\Domain\Header;

use ForkCMS\Core\Domain\Application\Application;
use ForkCMS\Core\Domain\Header\Asset\Asset;
use ForkCMS\Core\Domain\Header\Asset\AssetCollection;
use ForkCMS\Core\Domain\Header\Asset\Priority;
use ForkCMS\Core\Domain\Header\Breadcrumb\Breadcrumb;
use ForkCMS\Core\Domain\Header\Breadcrumb\BreadcrumbCollection;
use ForkCMS\Core\Domain\Header\FlashMessage\FlashMessage;
use ForkCMS\Core\Domain\Header\Meta\MetaCollection;
use ForkCMS\Core\Domain\Header\Meta\MetaLink;
use ForkCMS\Modules\Backend\Domain\Action\ModuleAction;
use ForkCMS\Modules\Backend\Domain\User\User;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Frontend\Domain\Meta\Meta;
use ForkCMS\Modules\Frontend\Domain\Privacy\ConsentDialog;
use ForkCMS\Modules\Internationalisation\Domain\Translator\DataCollectorTranslator;
use ForkCMS\Modules\Internationalisation\Domain\Translator\ForkTranslator;
use InvalidArgumentException;
use LogicException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This class will be used to alter the head-part of the HTML-document that will be created by he Backend
 * Therefore it will handle meta-stuff (title, including JS, including CSS, ...).
 */
final class Header
{
    public readonly JsData $jsData;

    public readonly AssetCollection $cssFiles;
    public readonly AssetCollection $jsFiles;

    public function __construct(
        public readonly BreadcrumbCollection $breadcrumbs,
        public readonly PageTitle $pageTitle,
        public readonly ContentTitle $contentTitle,
        public readonly MetaCollection $meta,
        private readonly RequestStack $requestStack,
        KernelInterface $kernel,
        Security $security,
        TranslatorInterface $translator,
        ConsentDialog $consentDialog,
    ) {
        $this->jsData = $this->initJsData($kernel, $security, $translator, $consentDialog);
        $this->jsFiles = new AssetCollection();
        $this->cssFiles = new AssetCollection();
    }

    public function appendMeta(Meta $meta): void
    {
        $this->contentTitle->overwriteContentTitle($meta->getTitle());
        $this->meta->addDescription($meta->getDescription(), $meta->isDescriptionOverwrite());
        $this->meta->addKeywords($meta->getKeywords(), $meta->isKeywordsOverwrite());
        $this->meta->setSEOFollow($meta->getSEOFollow());
        $this->meta->setSEOIndex($meta->getSEOIndex());

        if ($meta->isCanonicalUrlOverwrite() && $meta->getCanonicalUrl() !== null && $meta->getCanonicalUrl() !== '') {
            $this->meta->addMetaLink(MetaLink::canonical($meta->getCanonicalUrl()));
        }
    }

    private function initJsData(
        KernelInterface $kernel,
        Security $security,
        TranslatorInterface $translator,
        ConsentDialog $consentDialog
    ): JsData {
        $defaults = [
            'default_locale' => $kernel->getContainer()->getParameter('kernel.default_locale'),
            'debug' => $kernel->isDebug(),
            'session_timeout' => $this->getFirstPossibleSessionTimeout(),
            'privacyConsent' => $consentDialog,
        ];
        $defaults['locale'] = $translator->getLocale();
        $user = $security->getUser();
        if ($user instanceof User) {
            $defaults['locale'] = $user->getSetting('locale', $defaults['locale']);
        }
        if ($translator instanceof ForkTranslator || $translator instanceof DataCollectorTranslator) {
            $translationDomain = $translator->getDefaultTranslationDomain();
            $defaults['default_translation_domain'] = $translationDomain->getDomain();
            $fallbackDomain = $translationDomain->getFallback()?->getDomain();
            $defaultTranslationDomain = $fallbackDomain ?? $defaults['default_translation_domain'];
            $defaults['default_translation_domain_fallback'] = $defaultTranslationDomain;
        }

        return new JsData($defaults);
    }

    public function addJsData(ModuleName $module, string $key, mixed $value): void
    {
        $this->jsData->add($module, $key, $value);
    }

    private function getFirstPossibleSessionTimeout(): int
    {
        $garbageCollectionMaxLifeTime = (int) ini_get('session.gc_maxlifetime');
        $cookieLifetime = (int) ini_get('session.cookie_lifetime');

        if ($cookieLifetime === 0 || $cookieLifetime < $garbageCollectionMaxLifeTime) {
            return $garbageCollectionMaxLifeTime;
        }

        return $cookieLifetime;
    }

    public function addFlashMessage(FlashMessage $flashMessage): void
    {
        /** @var Session $session */
        $session = $this->requestStack->getSession();
        try {
            $session->getFlashBag()->add(
                $flashMessage->getType()->value,
                $flashMessage->getMessage()
            );
        } catch (SessionNotFoundException $e) {
            throw new LogicException(
                'You cannot use the addFlash method if sessions are disabled. ' .
                'Enable them in "config/packages/framework.yaml".',
                0,
                $e
            );
        }
    }

    public function addJs(Asset $assets): void
    {
        $this->jsFiles->add($assets);
    }

    public function addCss(Asset $assets): void
    {
        $this->cssFiles->add($assets);
    }

    public function addBreadcrumb(Breadcrumb $breadcrumb): void
    {
        $this->breadcrumbs->add($breadcrumb);
    }

    public function addAssetsForAction(ModuleAction $moduleAction): void
    {
        $module = $moduleAction->getModule();
        try {
            $this->addJs(
                Asset::forModule(
                    Application::BACKEND,
                    $module,
                    'js/' . $module . '.js',
                    priority: Priority::forModuleName($module)
                )
            );
        } catch (InvalidArgumentException) {
            // No module js file found
        }

        try {
            $this->addJs(
                Asset::forModule(
                    Application::BACKEND,
                    $module,
                    'js/' . $moduleAction->getAction()->getName() . '.js',
                    priority: Priority::forModuleName($module)
                )
            );
        } catch (InvalidArgumentException) {
            // No module action js file found
        }

        try {
            $this->addCss(
                Asset::forModule(
                    Application::BACKEND,
                    $module,
                    'css/' . $module . '.css',
                    priority: Priority::forModuleName($module)
                )
            );
        } catch (InvalidArgumentException) {
            // No module css file found
        }

        try {
            $this->addCss(
                Asset::forModule(
                    Application::BACKEND,
                    $module,
                    'css/' . $moduleAction->getAction()->getName() . '.css',
                    priority: Priority::forModuleName($module)
                )
            );
        } catch (InvalidArgumentException) {
            // No module action css file found
        }
    }
}
