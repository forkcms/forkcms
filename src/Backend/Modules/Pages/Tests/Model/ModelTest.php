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
            BackendPagesModel::getEncodedRedirectUrl('http://www.google.be/Quote')
        );
        self::assertEquals(
            'http://www.google.be/Quote%22HelloWorld%22',
            BackendPagesModel::getEncodedRedirectUrl('http://www.google.be/Quote"HelloWorld"')
        );
        self::assertEquals(
            'http://www.google.be/Quote%27HelloWorld%27',
            BackendPagesModel::getEncodedRedirectUrl("http://www.google.be/Quote'HelloWorld'")
        );
        self::assertEquals(
            'http://cédé.be/Quote%22HelloWorld%22',
            BackendPagesModel::getEncodedRedirectUrl('http://cédé.be/Quote"HelloWorld"')
        );
    }

    public function testGetTreeHtml(): void
    {
        $client = static::createClient();
        $this->login($client);

        // go to edit page to get a form token
        $tree = BackendPagesModel::getTreeHTML();

        self::assertEquals($this->getTestTree(BackendModel::getToken()), $tree);
    }

    public function getTestTree(string $token): string
    {
        return '<h4>{$lblAuthenticationMainNavigation}</h4>'
               . '<div class="clearfix" data-tree="main">'
               . '<ul>'
               . '<li id="page-1" rel=\'home\' data-jstree=\'{"type":"home"}\' data-allow-children="true" data-allow-move="false">'
               . '<a href="/private/en/pages/page_edit?token=' . $token . '&id=1">'
               . '<ins>&#160;</ins>Home</a>'
               . '<ul>'
               . '<li id="page-407" rel=\'page\' data-jstree=\'{"type":"page"}\' data-allow-children="true" data-allow-move="true">'
               . '<a href="/private/en/pages/page_edit?token=' . $token . '&id=407">'
               . '<ins>&#160;</ins>Blog</a>'
               . '</li>'
               . '<li id="page-408" rel=\'page\' data-jstree=\'{"type":"page"}\' data-allow-children="true" data-allow-move="true">'
               . '<a href="/private/en/pages/page_edit?token=' . $token . '&id=408">'
               . '<ins>&#160;</ins>FAQ</a>'
               . '</li>'
               . '<li id="page-409" rel=\'page\' data-jstree=\'{"type":"page"}\' data-allow-children="true" data-allow-move="true">'
               . '<a href="/private/en/pages/page_edit?token=' . $token . '&id=409">'
               . '<ins>&#160;</ins>Contact</a>'
               . '</li>'
               . '</ul>'
               . '</li>'
               . '</ul>'
               . '</div>'
               . '<h4>{$lblAuthenticationFooter}</h4>'
               . '<div class="clearfix" data-tree="footer">'
               . '<ul>'
               . '<li id="page-2" rel=\'sitemap\' data-jstree=\'{"type":"sitemap"}\' data-allow-children="true" data-allow-move="true">'
               . '<a href="/private/en/pages/page_edit?token=' . $token . '&id=2">'
               . '<ins>&#160;</ins>Sitemap</a>'
               . '</li>'
               . '<li id="page-3" rel=\'page\' data-jstree=\'{"type":"page"}\' data-allow-children="true" data-allow-move="true">'
               . '<a href="/private/en/pages/page_edit?token=' . $token . '&id=3">'
               . '<ins>&#160;</ins>Disclaimer</a>'
               . '</li>'
               . '</ul>'
               . '</div>'
               . '<h4>{$lblAuthenticationRoot}</h4>'
               . '<div class="clearfix" data-tree="root">'
               . '<ul>'
               . '<li id="page-404" rel=\'error\' data-jstree=\'{"type":"error"}\' data-allow-children="false" data-allow-move="false">'
               . '<a href="/private/en/pages/page_edit?token=' . $token . '&id=404">'
               . '<ins>&#160;</ins>404</a>'
               . '</li>'
               . '<li id="page-405" rel=\'page\' data-jstree=\'{"type":"page"}\' data-allow-children="true" data-allow-move="true">'
               . '<a href="/private/en/pages/page_edit?token=' . $token . '&id=405">'
               . '<ins>&#160;</ins>Search</a>'
               . '</li>'
               . '<li id="page-406" rel=\'page\' data-jstree=\'{"type":"page"}\' data-allow-children="true" data-allow-move="true">'
               . '<a href="/private/en/pages/page_edit?token=' . $token . '&id=406">'
               . '<ins>&#160;</ins>Tags</a>'
               . '</li>'
               . '<li id="page-410" rel=\'page\' data-jstree=\'{"type":"page"}\' data-allow-children="true" data-allow-move="true">'
               . '<a href="/private/en/pages/page_edit?token=' . $token . '&id=410">'
               . '<ins>&#160;</ins>Sent mailings</a>'
               . '<ul>'
               . '<li id="page-411" rel=\'page\' data-jstree=\'{"type":"page"}\' data-allow-children="true" data-allow-move="true">'
               . '<a href="/private/en/pages/page_edit?token=' . $token . '&id=411">'
               . '<ins>&#160;</ins>Subscribe</a>'
               . '</li>'
               . '<li id="page-412" rel=\'page\' data-jstree=\'{"type":"page"}\' data-allow-children="true" data-allow-move="true">'
               . '<a href="/private/en/pages/page_edit?token=' . $token . '&id=412">'
               . '<ins>&#160;</ins>Unsubscribe</a>'
               . '</li>'
               . '</ul>'
               . '</li>'
               . '<li id="page-413" rel=\'page\' data-jstree=\'{"type":"page"}\' data-allow-children="true" data-allow-move="true">'
               . '<a href="/private/en/pages/page_edit?token=' . $token . '&id=413">'
               . '<ins>&#160;</ins>Activate</a>'
               . '</li>'
               . '<li id="page-414" rel=\'page\' data-jstree=\'{"type":"page"}\' data-allow-children="true" data-allow-move="true">'
               . '<a href="/private/en/pages/page_edit?token=' . $token . '&id=414">'
               . '<ins>&#160;</ins>Forgot password</a>'
               . '</li>'
               . '<li id="page-415" rel=\'page\' data-jstree=\'{"type":"page"}\' data-allow-children="true" data-allow-move="true">'
               . '<a href="/private/en/pages/page_edit?token=' . $token . '&id=415">'
               . '<ins>&#160;</ins>Reset password</a>'
               . '</li>'
               . '<li id="page-416" rel=\'page\' data-jstree=\'{"type":"page"}\' data-allow-children="true" data-allow-move="true">'
               . '<a href="/private/en/pages/page_edit?token=' . $token . '&id=416">'
               . '<ins>&#160;</ins>Resend activation e-mail</a>'
               . '</li>'
               . '<li id="page-417" rel=\'page\' data-jstree=\'{"type":"page"}\' data-allow-children="true" data-allow-move="true">'
               . '<a href="/private/en/pages/page_edit?token=' . $token . '&id=417">'
               . '<ins>&#160;</ins>Login</a>'
               . '</li>'
               . '<li id="page-418" rel=\'page\' data-jstree=\'{"type":"page"}\' data-allow-children="true" data-allow-move="true">'
               . '<a href="/private/en/pages/page_edit?token=' . $token . '&id=418">'
               . '<ins>&#160;</ins>Register</a>'
               . '</li>'
               . '<li id="page-419" rel=\'page\' data-jstree=\'{"type":"page"}\' data-allow-children="true" data-allow-move="true">'
               . '<a href="/private/en/pages/page_edit?token=' . $token . '&id=419">'
               . '<ins>&#160;</ins>Logout</a>'
               . '</li>'
               . '<li id="page-420" rel=\'page\' data-jstree=\'{"type":"page"}\' data-allow-children="true" data-allow-move="true">'
               . '<a href="/private/en/pages/page_edit?token=' . $token . '&id=420">'
               . '<ins>&#160;</ins>Profile</a>'
               . '<ul>'
               . '<li id="page-421" rel=\'page\' data-jstree=\'{"type":"page"}\' data-allow-children="true" data-allow-move="true">'
               . '<a href="/private/en/pages/page_edit?token=' . $token . '&id=421">'
               . '<ins>&#160;</ins>Profile settings</a>'
               . '</li>'
               . '<li id="page-422" rel=\'page\' data-jstree=\'{"type":"page"}\' data-allow-children="true" data-allow-move="true">'
               . '<a href="/private/en/pages/page_edit?token=' . $token . '&id=422">'
               . '<ins>&#160;</ins>Change email</a>'
               . '</li>'
               . '<li id="page-423" rel=\'page\' data-jstree=\'{"type":"page"}\' data-allow-children="true" data-allow-move="true">'
               . '<a href="/private/en/pages/page_edit?token=' . $token . '&id=423">'
               . '<ins>&#160;</ins>Change password</a>'
               . '</li>'
               . '</ul>'
               . '</li>'
               . '</ul>'
               . '</div>';
    }
}
