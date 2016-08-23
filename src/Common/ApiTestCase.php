<?php

namespace Common;

/**
 * ApiTestCase is the base class for functional tests.
 */
abstract class ApiTestCase extends WebTestCase
{
    public function getAuthorizationParameters()
    {
        $email = 'noreply@fork-cms.com';
        $nonce = time();
        $secret = sha1(md5($nonce) . md5($email . '54f0fb1222403'));

        return array(
            'email' => $email,
            'nonce' => $nonce,
            'secret' => $secret,
        );
    }
}
