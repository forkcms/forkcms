<?php

namespace Common\Core\Twig\Extensions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Contains all Forkcms filters for Twig
 */
use Twig_Environment;
use Twig_SimpleFilter;
use Twig_SimpleFunction;

class TwigFilters
{
    /**
     * //http://twig.sensiolabs.org/doc/advanced.html#id2
     * returns a collection of Twig SimpleFilters
     *
     * @param Twig_Environment $twig
     * @param string $app
     */
    public static function addFilters(Twig_Environment $twig, string $app): void
    {
        $app .= '\Core\Engine\TemplateModifiers';
        $twig->addFilter(new Twig_SimpleFilter('getpageinfo', $app.'::getPageInfo'));
        $twig->addFilter(new Twig_SimpleFilter('highlight', $app.'::highlightCode'));
        $twig->addFilter(new Twig_SimpleFilter('profilesetting', $app.'::profileSetting'));
        $twig->addFilter(new Twig_SimpleFilter('formatcurrency', $app.'::formatCurrency', ['is_safe' => ['html']]));
        $twig->addFilter(new Twig_SimpleFilter('usersetting', $app.'::userSetting'));
        $twig->addFilter(new Twig_SimpleFilter('uppercase', $app.'::uppercase'));
        $twig->addFilter(new Twig_SimpleFilter('rand', $app.'::random'));
        $twig->addFilter(new Twig_SimpleFilter('formatfloat', $app.'::formatFloat'));
        $twig->addFilter(new Twig_SimpleFilter('truncate', $app.'::truncate'));
        $twig->addFilter(new Twig_SimpleFilter('camelcase', $app.'::camelCase'));
        $twig->addFilter(new Twig_SimpleFilter('snakeCase', $app.'::snakeCase'));
        $twig->addFilter(new Twig_SimpleFilter('stripnewlines', $app.'::stripNewlines'));
        $twig->addFilter(new Twig_SimpleFilter('formatnumber', $app.'::formatNumber'));
        $twig->addFilter(new Twig_SimpleFilter('tolabel', $app.'::toLabel'));
        $twig->addFilter(new Twig_SimpleFilter('cleanupplaintext', $app.'::cleanupPlainText'));

        // exposed PHP functions

        $twig->addFilter(new Twig_SimpleFilter('urlencode', 'urlencode'));
        $twig->addFilter(new Twig_SimpleFilter('rawurlencode', 'rawurlencode'));
        $twig->addFilter(new Twig_SimpleFilter('striptags', 'strip_tags'));
        $twig->addFilter(new Twig_SimpleFilter('addslashes', 'addslashes'));
        $twig->addFilter(new Twig_SimpleFilter('count', 'count'));
        $twig->addFilter(new Twig_SimpleFilter('is_array', 'is_array'));
        $twig->addFilter(new Twig_SimpleFilter('ucfirst', 'ucfirst'));

        // Functions navigation
        $twig->addFunction(new Twig_SimpleFunction(
            'getnavigation',
            $app.'::getNavigation',
            ['is_safe' => ['html']]
        ));
        $twig->addFunction(new Twig_SimpleFunction(
            'getsubnavigation',
            $app.'::getSubNavigation',
            ['is_safe' => ['html']]
        ));
        $twig->addFunction(new Twig_SimpleFunction(
            'parsewidget',
            $app.'::parseWidget',
            ['is_safe' => ['html']]
        ));

        // Function URL

        $twig->addFunction(new Twig_SimpleFunction(
            'geturl',
            $app.'::getUrl'
        ));
        $twig->addFunction(new Twig_SimpleFunction(
            'geturlforextraid',
            $app.'::getUrlForExtraId'
        ));
        $twig->addFunction(new Twig_SimpleFunction(
            'geturlforblock',
            $app.'::getUrlForBlock'
        ));

        // boolean functions

        $twig->addFunction(new Twig_SimpleFunction(
            'showbool',
            $app.'::showBool',
            ['is_safe' => ['html']]
        ));

        // @Deprecated We should look for replacements because they run on spoon library
        // after we have those we can remove them

        $twig->addFilter(new Twig_SimpleFilter('spoondate', $app.'::spoonDate'));
        $twig->addFilter(new Twig_SimpleFilter('formatdate', $app.'::formatDate'));
        $twig->addFilter(new Twig_SimpleFilter('formattime', $app.'::formatTime'));
        $twig->addFilter(new Twig_SimpleFilter('timeago', $app.'::timeAgo'));
        $twig->addFilter(new Twig_SimpleFilter('formatdatetime', $app.'::formatDateTime'));
    }
}
