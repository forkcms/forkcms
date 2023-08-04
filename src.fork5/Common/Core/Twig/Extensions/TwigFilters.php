<?php

namespace Common\Core\Twig\Extensions;

/**
 * Contains all Forkcms filters for Twig
 */

use Backend\Core\Engine\Authentication;
use Twig\Environment;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigFilters
{
    /**
     * //http://twig.sensiolabs.org/doc/advanced.html#id2
     * returns a collection of Twig SimpleFilters
     *
     * @param Environment $twig
     * @param string $app
     */
    public static function addFilters(Environment $twig, string $app): void
    {
        if ($app === 'Backend') {
            $twig->addFunction(
                new TwigFunction(
                    'isAllowedAction',
                    [Authentication::class, 'isAllowedAction']
                )
            );
            $twig->addFunction(
                new TwigFunction(
                    'isAllowedModule',
                    [Authentication::class, 'isAllowedModule']
                )
            );
        }
        $app .= '\Core\Engine\TemplateModifiers';
        $twig->addFilter(new TwigFilter('getpageinfo', $app.'::getPageInfo'));
        $twig->addFilter(new TwigFilter('highlight', $app.'::highlightCode'));
        $twig->addFilter(new TwigFilter('profilesetting', $app.'::profileSetting'));
        $twig->addFilter(new TwigFilter('formatcurrency', $app.'::formatCurrency', ['is_safe' => ['html']]));
        $twig->addFilter(new TwigFilter('usersetting', $app.'::userSetting'));
        $twig->addFilter(new TwigFilter('uppercase', $app.'::uppercase'));
        $twig->addFilter(new TwigFilter('rand', $app.'::random'));
        $twig->addFilter(new TwigFilter('formatfloat', $app.'::formatFloat'));
        $twig->addFilter(new TwigFilter('truncate', $app . '::truncate', ['is_safe' => ['html']]));
        $twig->addFilter(new TwigFilter('camelcase', $app.'::camelCase'));
        $twig->addFilter(new TwigFilter('snakeCase', $app.'::snakeCase'));
        $twig->addFilter(new TwigFilter('stripnewlines', $app.'::stripNewlines'));
        $twig->addFilter(new TwigFilter('formatnumber', $app.'::formatNumber'));
        $twig->addFilter(new TwigFilter('tolabel', $app.'::toLabel'));
        $twig->addFilter(new TwigFilter('cleanupplaintext', $app.'::cleanupPlainText'));

        // exposed PHP functions
        $twig->addFilter(new TwigFilter('urlencode', 'urlencode'));
        $twig->addFilter(new TwigFilter('rawurlencode', 'rawurlencode'));
        $twig->addFilter(new TwigFilter('striptags', 'strip_tags'));
        $twig->addFilter(new TwigFilter('addslashes', 'addslashes'));
        $twig->addFilter(new TwigFilter('count', 'count'));
        $twig->addFilter(new TwigFilter('is_array', 'is_array'));
        $twig->addFilter(new TwigFilter('ucfirst', 'ucfirst'));

        // Functions navigation
        $twig->addFunction(
            new TwigFunction(
                'getnavigation',
                $app . '::getNavigation',
                ['is_safe' => ['html']]
            )
        );
        $twig->addFunction(
            new TwigFunction(
                'getsubnavigation',
                $app . '::getSubNavigation',
                ['is_safe' => ['html']]
            )
        );
        $twig->addFunction(
            new TwigFunction(
                'parsewidget',
                $app . '::parseWidget',
                ['is_safe' => ['html']]
            )
        );

        // Function URL
        $twig->addFunction(
            new TwigFunction(
                'geturl',
                $app . '::getUrl'
            )
        );
        $twig->addFunction(
            new TwigFunction(
                'geturlforextraid',
                $app . '::getUrlForExtraId'
            )
        );
        $twig->addFunction(
            new TwigFunction(
                'geturlforblock',
                $app . '::getUrlForBlock'
            )
        );

        // boolean functions
        $twig->addFunction(
            new TwigFunction(
                'showbool',
                $app . '::showBool',
                ['is_safe' => ['html']]
            )
        );

        // @Deprecated We should look for replacements because they run on spoon library
        // after we have those we can remove them

        $twig->addFilter(new TwigFilter('spoondate', $app . '::spoonDate'));
        $twig->addFilter(new TwigFilter('formatdate', $app . '::formatDate'));
        $twig->addFilter(new TwigFilter('formattime', $app . '::formatTime'));
        $twig->addFilter(new TwigFilter('timeago', $app . '::timeAgo'));
        $twig->addFilter(new TwigFilter('formatdatetime', $app . '::formatDateTime'));
    }
}
