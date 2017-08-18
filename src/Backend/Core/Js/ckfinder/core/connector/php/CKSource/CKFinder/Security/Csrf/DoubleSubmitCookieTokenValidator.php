<?php

/*
 * CKFinder
 * ========
 * http://cksource.com/ckfinder
 * Copyright (C) 2007-2016, CKSource - Frederico Knabben. All rights reserved.
 *
 * The software, this file and its contents are subject to the CKFinder
 * License. Please read the license.txt file before using, installing, copying,
 * modifying or distribute this file or part of its contents. The contents of
 * this file is part of the Source Code of CKFinder.
 */

namespace CKSource\CKFinder\Security\Csrf;

use Symfony\Component\HttpFoundation\Request;

/**
 * The DoubleSubmitCookieTokenValidator class.
 *
 * Checks if the request contains a valid token that matches the value sent in the cookie.
 *
 * @see https://www.owasp.org/index.php/Cross-Site_Request_Forgery_(CSRF)_Prevention_Cheat_Sheet#Double_Submit_Cookies
 */
class DoubleSubmitCookieTokenValidator implements TokenValidatorInterface
{
    /**
     * The name of the request parameter that should contain the token.
     *
     * @var string
     */
    protected $tokenParamName;

    /**
     * The name of the cookie key that should contain the token.
     * @var string
     */
    protected $tokenCookieName;

    /**
     * The minimal length ot the token.
     *
     * @var int
     */
    protected $minTokenLength;

    /**
     * DoubleSubmitCookieTokenValidator constructor.
     *
     * @param string $tokenParamName
     * @param string $tokenCookieName
     * @param int    $minTokenLength
     */
    public function __construct($tokenParamName = 'ckCsrfToken', $tokenCookieName = 'ckCsrfToken', $minTokenLength = 32)
    {
        $this->tokenParamName = $tokenParamName;
        $this->tokenCookieName = $tokenCookieName;
        $this->minTokenLength = $minTokenLength;
    }

    /**
     * Checks if the request contains a valid CSRF token.
     *
     * @param Request $request
     *
     * @return bool `true` if the token is valid, `false` otherwise.
     */
    public function validate(Request $request)
    {
        $paramToken = trim((string) $request->get($this->tokenParamName));
        $cookieToken = trim((string) $request->cookies->get($this->tokenCookieName));

        if (strlen($paramToken) >= $this->minTokenLength && strlen($cookieToken) >= $this->minTokenLength) {
            return $paramToken === $cookieToken;
        }

        return false;
    }
}
