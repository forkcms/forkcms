<?php

namespace ForkCMS\Modules\Backend\Controller;

use ForkCMS\Core\Domain\Header\Breadcrumb\Breadcrumb;
use ForkCMS\Core\Domain\Header\Breadcrumb\BreadcrumbCollection;
use ForkCMS\Modules\Backend\Domain\NavigationItem\NavigationItemRepository;
use ForkCMS\Modules\Backend\Domain\User\User;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

class LoginController
{
    public function __construct(
        private readonly Environment $twig,
        private readonly AuthenticationUtils $authenticationUtils,
        private readonly Security $security,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly NavigationItemRepository $navigationItemRepository,
        private readonly BreadcrumbCollection $breadcrumbs,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $currentUser = $this->security->getUser();
        if ($currentUser instanceof User) {
            return new RedirectResponse(
                $this->navigationItemRepository->findFirstWithSlugForUser($currentUser)->getSlug()?->generateRoute($this->urlGenerator)
            );
        }

        // get the login error if there is one
        $error = $this->authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $this->authenticationUtils->getLastUsername();
        $this->breadcrumbs->add(new Breadcrumb(TranslationKey::label('LogIn')));

        return new Response(
            $this->twig->render(
                '@Backend/Backend/login.html.twig',
                [
                    'last_username' => $lastUsername,
                    'error' => $error,
                ]
            )
        );
    }
}
