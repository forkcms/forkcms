<?php

namespace ForkCMS\Modules\OAuth\Domain\Authentication;

use Doctrine\Common\Collections\ArrayCollection;
use ForkCMS\Modules\Backend\Backend\Actions\AuthenticationLogin;
use ForkCMS\Modules\Backend\Domain\User\User;
use ForkCMS\Modules\Backend\Domain\User\UserRepository;
use ForkCMS\Modules\Backend\Domain\UserGroup\UserGroupRepository;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use TheNetworg\OAuth2\Client\Provider\Azure;
use TheNetworg\OAuth2\Client\Provider\AzureResourceOwner;

class AzureAuthenticator extends OAuth2Authenticator implements AuthenticationEntryPointInterface
{
    const ORIGIN = 'azure';

    public function __construct(
        private readonly Azure $azure,
        private readonly RequestStack $requestStack,
        private readonly TranslatorInterface $translator,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly RouterInterface $router,
        private readonly UserRepository $userRepository,
        private readonly UserGroupRepository $userGroupRepository,
    ) {
    }

    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        return new RedirectResponse(
            $this->router->generate('connect_azure_start'),
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'connect_azure_check';
    }

    public function authenticate(Request $request): Passport
    {
        /** @var OAuth2ClientInterface $client */
        $client = new OAuth2Client(
            $this->azure,
            $this->requestStack,
        );
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client) {
                /** @var AzureResourceOwner $azureUser */
                $azureUser = $client->fetchUserFromToken($accessToken);

                $roles = $azureUser->claim('roles');

                if ($roles === null) {
                    $roles = [];
                }

                $userGroups = $this->getGroupsFromRoles($roles);

                if (count($userGroups) === 0) {
                    return null;
                }

                /** @var User $existingUser */
                $existingUser = $this->userRepository->findOneByEmail($azureUser->claim('email'));

                if ($existingUser) {
                    $existingUser->getUserGroups()->clear();
                    foreach ($userGroups as $userGroup) {
                        $existingUser->addUserGroup($userGroup);
                    }

                    $this->userRepository->save($existingUser, true);

                    return $existingUser;
                }

                $user = new User(
                    $azureUser->claim('email'),
                    null,
                    $azureUser->claim('name'),
                    true,
                    false,
                    new ArrayCollection($userGroups)
                );

                $this->userRepository->save($user);

                return $user;
            })
        );
    }

    private function getGroupsFromRoles(array $roles): array
    {
        $userGroups = [];

        foreach ($roles as $role) {
            $userGroup = $this->userGroupRepository->findOneBy(['oAuthRole' => $role]);

            if ($userGroup) {
                $userGroups[] = $userGroup;
            }
        }

        return $userGroups;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse(
            AuthenticationLogin::getActionSlug()->generateRoute($this->urlGenerator)
        );
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $this->requestStack->getSession()->getFlashBag()->add(
            'error',
            $this->translator->trans('login.error', [], 'azure')
        );

        return new RedirectResponse('/private');
    }
}
