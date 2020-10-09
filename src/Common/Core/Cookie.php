<?php

namespace Common\Core;

use Symfony\Component\HttpFoundation\Cookie as SymfonyCookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

final class Cookie
{
    /**
     * @var ResponseHeaderBag
     */
    private $newCookiesHeaderBag;

    public function __construct()
    {
        $this->newCookiesHeaderBag = new ResponseHeaderBag();
    }

    /**
     * Creates a new instance of the Symfony cookie
     *
     * It will automatically make them secure if you are on https and will set the domain if it isn't set
     *
     * @param string $name The name of the cookie
     * @param string|null $value The value of the cookie
     * @param int $expire The number of seconds before the cookie expires, defaults to 30 days
     * @param string $path The path on the server in which the cookie will be available on
     * @param string|null $domain The domain that the cookie is available to
     * @param bool|null $secure Whether the cookie should only be transmitted over a secure HTTPS connection from the client. When null is past it will be secure only when set on https
     * @param bool $httpOnly Whether the cookie will be made accessible only through the HTTP protocol
     * @param bool $raw Whether the cookie value should be sent with no url encoding
     * @param string|null $sameSite Whether the cookie will be available for cross-site requests
     */
    public function set(
        string $name,
        string $value = null,
        int $expire = 2592000,
        string $path = '/',
        string $domain = null,
        bool $secure = null,
        bool $httpOnly = true,
        bool $raw = false,
        string $sameSite = SymfonyCookie::SAMESITE_LAX
    ): void {
        $this->setCookie(
            new SymfonyCookie(
                $name,
                $value,
                time() + $expire,
                $path,
                $this->normalizeDomain($domain),
                $this->normalizeSecure($secure),
                $httpOnly,
                $raw,
                $sameSite
            )
        );
    }

    public function setCookie(SymfonyCookie $cookie): void
    {
        $this->newCookiesHeaderBag->setCookie($cookie);
    }

    public function delete(
        string $name,
        string $path = '/',
        string $domain = null,
        bool $secure = null,
        bool $httpOnly = true
    ): void {
        // clear in the browser
        $this->newCookiesHeaderBag->clearCookie(
            $name,
            $path,
            $this->normalizeDomain($domain),
            $this->normalizeSecure($secure),
            $httpOnly
        );

        // remove from the new cookies array
        $this->newCookiesHeaderBag->removeCookie($name, $path, $this->normalizeDomain($domain));

        // remove from the request
        unset($_COOKIE[$name]);
        Model::getRequest()->cookies->remove($name);
    }

    public function get(string $name, string $default = null): ?string
    {
        return Model::getRequest()->cookies->get($name, $default);
    }

    public function has(string $name): bool
    {
        return Model::getRequest()->cookies->has($name);
    }

    public function all(): array
    {
        return Model::getRequest()->cookies->all();
    }

    /**
     * This will set the secure flag to true if no specific secure flag is given and we are on https
     *
     * @param bool|null $secure
     *
     * @return bool
     */
    private function normalizeSecure(bool $secure = null): bool
    {
        if ($secure !== null) {
            return $secure;
        }

        return Model::getRequest()->isSecure();
    }

    /**
     * This will set the domain to the current host if the domain is null
     *
     * @param string|null $domain
     *
     * @return string|null
     */
    private function normalizeDomain(string $domain = null): ?string
    {
        if ($domain !== null) {
            return $domain;
        }

        if (Model::requestIsAvailable()) {
            return '.' . Model::getRequest()->getHost();
        }

        return null;
    }

    /**
     * Has the visitor allowed cookies?
     * @deprecated remove this in Fork 6, the privacy consent dialog should be used
     *
     * @return bool
     */
    public function hasAllowedCookies(): bool
    {
        return $this->get('cookie_bar_agree', 'N') === 'Y';
    }

    /**
     * Has the cookiebar been hidden by the visitor
     * @deprecated remove this in Fork 6, the privacy consent dialog should be used
     *
     * @return bool
     */
    public function hasHiddenCookieBar(): bool
    {
        return $this->get('cookie_bar_hide', 'N') === 'Y';
    }

    public function attachToResponse(Response $response): void
    {
        foreach ($this->newCookiesHeaderBag->getCookies() as $cookie) {
            $response->headers->setCookie($cookie);
        }
    }
}
