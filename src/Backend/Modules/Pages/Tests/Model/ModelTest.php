<?php

namespace Backend\Modules\Pages\Tests\Model;

use Backend\Modules\Pages\Engine\Model as BackendPagesModel;
use Backend\Core\Engine\Model as BackendModel;
use Common\WebTestCase;

class ModelTest extends WebTestCase
{
    public function testUrlIsEncoded(): void
    {
        self::assertEquals(
            'http://www.google.be/Quote',
            Model::getEncodedRedirectUrl('http://www.google.be/Quote')
        );
        self::assertEquals(
            'http://www.google.be/Quote%22HelloWorld%22',
            Model::getEncodedRedirectUrl('http://www.google.be/Quote"HelloWorld"')
        );
        self::assertEquals(
            'http://www.google.be/Quote%27HelloWorld%27',
            Model::getEncodedRedirectUrl("http://www.google.be/Quote'HelloWorld'")
        );
        self::assertEquals(
            'http://cédé.be/Quote%22HelloWorld%22',
            Model::getEncodedRedirectUrl('http://cédé.be/Quote"HelloWorld"')
        );
    }

    public function testGetTreeHtml(): void
    {
        $client = static::createClient();
        $this->login($client);

        // go to edit page to get a form token

        self::assertEquals($this->getTestTree(BackendModel::getToken()), BackendPagesModel::getTreeHTML());
    }

    public function getTestTree(string $token): string
    {
        return '<h4>{$lblAuthenticationMainNavigation}</h4>
<div class="clearfix" data-tree="main">
    <ul>
        <li id="page-"1 rel="home" data-jstree=\'{"opened": true, "type":"home"}\'>            <a href="/private/en/pages/edit?id=1&token=' . $token . '"><ins>&#160;</ins>Home</a>
<ul>
<li id="page-407" rel="page" data-jstree=\'{"type":"page"}\'">
    <a href="/private/en/pages/edit?id=407&token=' . $token . '"><ins>&#160;</ins>Blog</a>
</li>
<li id="page-408" rel="page" data-jstree=\'{"type":"page"}\'">
    <a href="/private/en/pages/edit?id=408&token=' . $token . '"><ins>&#160;</ins>FAQ</a>
</li>
<li id="page-409" rel="page" data-jstree=\'{"type":"page"}\'">
    <a href="/private/en/pages/edit?id=409&token=' . $token . '"><ins>&#160;</ins>Contact</a>
</li>
</ul>
        </li>
    </ul>
</div>
<h4>{$lblAuthenticationFooter}</h4>
<div class="clearfix" data-tree="footer">
    <ul>
        <li id="page-2" rel="sitemap" data-jstree=\'{"type":"sitemap"}\'">
            <a href="/private/en/pages/edit?id=2&token=' . $token . '"><ins>&#160;</ins>Sitemap</a>
        </li>
        <li id="page-3" rel="page" data-jstree=\'{"type":"page"}\'">
            <a href="/private/en/pages/edit?id=3&token=' . $token . '"><ins>&#160;</ins>Disclaimer</a>
        </li>
    </ul>
</div>
<h4>{$lblAuthenticationRoot}</h4>
<div class="clearfix" data-tree="root">
    <ul>
        <li id="page-404" rel="error" data-jstree=\'{"type":"error"}\'">
            <a href="/private/en/pages/edit?id=404&token=' . $token . '"><ins>&#160;</ins>404</a>
        </li>
        <li id="page-405" rel="page" data-jstree=\'{"type":"page"}\'">
            <a href="/private/en/pages/edit?id=405&token=' . $token . '"><ins>&#160;</ins>Search</a>
        </li>
        <li id="page-406" rel="page" data-jstree=\'{"type":"page"}\'">
            <a href="/private/en/pages/edit?id=406&token=' . $token . '"><ins>&#160;</ins>Tags</a>
        </li>
        <li id="page-410" rel="page" data-jstree=\'{"type":"page"}\'">
            <a href="/private/en/pages/edit?id=410&token=' . $token . '"><ins>&#160;</ins>Sent mailings</a>
<ul>
<li id="page-411" rel="page" data-jstree=\'{"type":"page"}\'">
    <a href="/private/en/pages/edit?id=411&token=' . $token . '"><ins>&#160;</ins>Subscribe</a>
</li>
<li id="page-412" rel="page" data-jstree=\'{"type":"page"}\'">
    <a href="/private/en/pages/edit?id=412&token=' . $token . '"><ins>&#160;</ins>Unsubscribe</a>
</li>
</ul>
        </li>
        <li id="page-413" rel="page" data-jstree=\'{"type":"page"}\'">
            <a href="/private/en/pages/edit?id=413&token=' . $token . '"><ins>&#160;</ins>Activate</a>
        </li>
        <li id="page-414" rel="page" data-jstree=\'{"type":"page"}\'">
            <a href="/private/en/pages/edit?id=414&token=' . $token . '"><ins>&#160;</ins>Forgot password</a>
        </li>
        <li id="page-415" rel="page" data-jstree=\'{"type":"page"}\'">
            <a href="/private/en/pages/edit?id=415&token=' . $token . '"><ins>&#160;</ins>Reset password</a>
        </li>
        <li id="page-416" rel="page" data-jstree=\'{"type":"page"}\'">
            <a href="/private/en/pages/edit?id=416&token=' . $token . '"><ins>&#160;</ins>Resend activation e-mail</a>
        </li>
        <li id="page-417" rel="page" data-jstree=\'{"type":"page"}\'">
            <a href="/private/en/pages/edit?id=417&token=' . $token . '"><ins>&#160;</ins>Login</a>
        </li>
        <li id="page-418" rel="page" data-jstree=\'{"type":"page"}\'">
            <a href="/private/en/pages/edit?id=418&token=' . $token . '"><ins>&#160;</ins>Register</a>
        </li>
        <li id="page-419" rel="page" data-jstree=\'{"type":"page"}\'">
            <a href="/private/en/pages/edit?id=419&token=' . $token . '"><ins>&#160;</ins>Logout</a>
        </li>
        <li id="page-420" rel="page" data-jstree=\'{"type":"page"}\'">
            <a href="/private/en/pages/edit?id=420&token=' . $token . '"><ins>&#160;</ins>Profile</a>
<ul>
<li id="page-421" rel="page" data-jstree=\'{"type":"page"}\'">
    <a href="/private/en/pages/edit?id=421&token=' . $token . '"><ins>&#160;</ins>Profile settings</a>
</li>
<li id="page-422" rel="page" data-jstree=\'{"type":"page"}\'">
    <a href="/private/en/pages/edit?id=422&token=' . $token . '"><ins>&#160;</ins>Change email</a>
</li>
<li id="page-423" rel="page" data-jstree=\'{"type":"page"}\'">
    <a href="/private/en/pages/edit?id=423&token=' . $token . '"><ins>&#160;</ins>Change password</a>
</li>
</ul>
        </li>
    </ul>
</div>
';
    }
}
