<?php

namespace Backend\Core\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Installer for the core
 */
class CoreInstaller extends ModuleInstaller
{
    public function install(): void
    {
        // validate variables
        if ($this->getVariable('default_language') === null) {
            throw new \SpoonException('Default frontend language is not provided.');
        }
        if ($this->getVariable('default_interface_language') === null) {
            throw new \SpoonException('Default backend language is not provided.');
        }
        if ($this->getVariable('site_domain') === null) {
            throw new \SpoonException('Site domain is not provided.');
        }
        if ($this->getVariable('spoon_debug_email') === null) {
            throw new \SpoonException('Spoon debug email is not provided.');
        }
        if ($this->getVariable('site_title') === null) {
            throw new \SpoonException('Site title is not provided.');
        }

        // import SQL
        $this->importSQL(__DIR__ . '/Data/install.sql');

        // add core modules
        $this->addModule('Core');
        $this->addModule('Authentication');
        $this->addModule('Dashboard');
        $this->addModule('Error');

        $this->setRights();
        $this->configureDefaultSettings();

        // add core navigation
        $this->setNavigation(null, 'Dashboard', 'dashboard/index', null, 1);
        $this->setNavigation(null, 'Modules', null, null, 4);
    }

    private function setRights(): void
    {
        $this->setModuleRights(1, 'Dashboard');
        $this->setActionRights(1, 'Dashboard', 'Index');
    }

    private function configureDefaultSettings(): void
    {
        // languages settings
        $this->setSetting('Core', 'languages', $this->getLanguages(), true);
        $this->setSetting('Core', 'active_languages', $this->getLanguages(), true);
        $this->setSetting('Core', 'redirect_languages', $this->getLanguages(), true);
        $this->setSetting('Core', 'default_language', $this->getVariable('default_language'), true);
        $this->setSetting('Core', 'interface_languages', $this->getInterfaceLanguages(), true);
        $this->setSetting('Core', 'default_interface_language', $this->getVariable('default_interface_language'), true);

        // other settings
        $this->setSetting('Core', 'theme');
        $this->setSetting('Core', 'akismet_key', '');
        $this->setSetting('Core', 'google_maps_key', '');
        $this->setSetting('Core', 'max_num_revisions', 20);
        $this->setSetting('Core', 'site_domains', [$this->getVariable('site_domain')]);
        $this->setSetting('Core', 'site_html_header', '');
        $this->setSetting('Core', 'site_html_footer', '');

        // date & time
        $this->setSetting('Core', 'date_format_short', 'j.n.Y');
        $this->setSetting(
            'Core',
            'date_formats_short',
            [
                'j/n/Y',
                'j-n-Y',
                'j.n.Y',
                'n/j/Y',
                'n/j/Y',
                'n/j/Y',
                'd/m/Y',
                'd-m-Y',
                'd.m.Y',
                'm/d/Y',
                'm-d-Y',
                'm.d.Y',
                'j/n/y',
                'j-n-y',
                'j.n.y',
                'n/j/y',
                'n-j-y',
                'n.j.y',
                'd/m/y',
                'd-m-y',
                'd.m.y',
                'm/d/y',
                'm-d-y',
                'm.d.y',
            ]
        );
        $this->setSetting('Core', 'date_format_long', 'l j F Y');
        $this->setSetting(
            'Core',
            'date_formats_long',
            [
                'j F Y',
                'D j F Y',
                'l j F Y',
                'j F, Y',
                'D j F, Y',
                'l j F, Y',
                'd F Y',
                'd F, Y',
                'F j Y',
                'D F j Y',
                'l F j Y',
                'F d, Y',
                'D F d, Y',
                'l F d, Y',
            ]
        );
        $this->setSetting('Core', 'time_format', 'H:i');
        $this->setSetting('Core', 'time_formats', ['H:i', 'H:i:s', 'g:i a', 'g:i A']);

        // number formats
        $this->setSetting('Core', 'number_format', 'dot_nothing');
        $this->setSetting(
            'Core',
            'number_formats',
            [
                'comma_nothing' => '10000,25',
                'dot_nothing' => '10000.25',
                'dot_comma' => '10,000.25',
                'comma_dot' => '10.000,25',
                'dot_space' => '10000.25',
                'comma_space' => '10 000,25',
            ]
        );

        // e-mail settings
        $this->setSetting(
            'Core',
            'mailer_from',
            ['name' => 'Fork CMS', 'email' => $this->getVariable('spoon_debug_email')]
        );
        $this->setSetting(
            'Core',
            'mailer_to',
            ['name' => 'Fork CMS', 'email' => $this->getVariable('spoon_debug_email')]
        );
        $this->setSetting(
            'Core',
            'mailer_reply_to',
            ['name' => 'Fork CMS', 'email' => $this->getVariable('spoon_debug_email')]
        );

        // smtp settings
        $this->setSetting('Core', 'smtp_server', $this->getVariable('smtp_server'));
        $this->setSetting('Core', 'smtp_port', $this->getVariable('smtp_port'));
        $this->setSetting('Core', 'smtp_username', $this->getVariable('smtp_username'));
        $this->setSetting('Core', 'smtp_password', $this->getVariable('smtp_password'));

        // default titles
        $siteTitles = [
            'en' => 'My website',
            'bg' => 'уебсайта си',
            'zh' => '我的网站',
            'cs' => 'můj web',
            'nl' => 'Mijn website',
            'fr' => 'Mon site web',
            'de' => 'Meine Webseite',
            'el' => 'ιστοσελίδα μου',
            'hu' => 'Hhonlapom',
            'it' => 'Il mio sito web',
            'ja' => '私のウェブサイト',
            'lt' => 'mano svetainė',
            'pl' => 'moja strona',
            'ro' => 'site-ul meu',
            'ru' => 'мой сайт',
            'es' => 'Mi sitio web',
            'sv' => 'min hemsida',
            'tr' => 'web siteme',
            'uk' => 'мій сайт',
        ];

        // language specific
        foreach ($this->getLanguages() as $language) {
            // set title
            $this->setSetting(
                'Core',
                'site_title_' . $language,
                (isset($siteTitles[$language])) ? $siteTitles[$language] : $this->getVariable('site_title')
            );
        }

        // ckfinder
        $this->setSetting('Core', 'ckfinder_license_name', 'Fork CMS');
        $this->setSetting('Core', 'ckfinder_license_key', 'VNA6-BP17-T7D3-CP1B-EMJF-X7Q3-5THF');

        // Enable the cookie bar by default when the timezone is in europe
        $this->setSetting(
            'Core',
            'show_cookie_bar',
            date_default_timezone_get() && strpos(mb_strtolower(date_default_timezone_get()), 'europe') === 0
        );
    }
}
