<?php

namespace ForkCMS\Modules\Backend\Controller;

use ForkCMS\Core\Domain\Header\Header;
use ForkCMS\Modules\Backend\Domain\NavigationItem\NavigationItemRepository;
use ForkCMS\Modules\Backend\Domain\User\User;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleSettings;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class LoginController
{
    public function __construct(
        private readonly Environment $twig,
        private readonly AuthenticationUtils $authenticationUtils,
        private readonly TranslatorInterface $translator,
        private readonly Security $security,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly NavigationItemRepository $navigationItemRepository,
        private readonly Header $header,
        private readonly ModuleSettings $moduleSettings,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $currentUser = $this->security->getUser();
        if ($currentUser instanceof User) {
            return new RedirectResponse(
                $this->navigationItemRepository->findFirstWithSlugForUser($currentUser)
                    ->getSlug()?->generateRoute($this->urlGenerator)
            );
        }

        // get the login error if there is one
        $error = $this->authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $this->authenticationUtils->getLastUsername();
        $this->header->parse($this->twig);

        return new Response(
            $this->twig->render(
                '@Backend/Backend/login.html.twig',
                [
                    'last_username' => $lastUsername,
                    'error' => $error,
                    'page_title' => $this->translator->trans(TranslationKey::label('LogIn')),
                    'SITE_TITLE' => $this->moduleSettings->get(
                        ModuleName::fromString('Frontend'),
                        'site_title_' . $request->getLocale(),
                        $_ENV['SITE_DEFAULT_TITLE']
                    ),
                    'SITE_URL' => $_ENV['SITE_PROTOCOL'] . '://' . $_ENV['SITE_DOMAIN'],
                    'jsFiles' => [],
                ]
            )
        );
    }
}
