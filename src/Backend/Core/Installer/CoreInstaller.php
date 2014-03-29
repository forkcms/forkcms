<?php

namespace Backend\Core\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Finder\Finder;

use Backend\Modules\Locale\Engine\Model as BackendLocaleModel;

/**
 * Installer for the core
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class CoreInstaller extends ModuleInstaller
{
    /**
     * Install the module
     */
    public function install()
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
        if ($this->getVariable('api_email') === null) {
            throw new \SpoonException('API email is not provided.');
        }
        if ($this->getVariable('site_title') === null) {
            throw new \SpoonException('Site title is not provided.');
        }

        // import SQL
        $this->importSQL(dirname(__FILE__) . '/Data/install.sql');

        // add core modules
        $this->addModule('Core');
        $this->addModule('Authentication');
        $this->addModule('Dashboard');
        $this->addModule('Error');

        $this->setRights();
        $this->setSettings();

        // add core navigation
        $this->setNavigation(null, 'Dashboard', 'dashboard/index', null, 1);
        $this->setNavigation(null, 'Modules', null, null, 3);
    }

    /**
     * Set the rights
     */
    private function setRights()
    {
        $this->setModuleRights(1, 'Dashboard');

        $this->setActionRights(1, 'Dashboard', 'Index');
        $this->setActionRights(1, 'Dashboard', 'AlterSequence');
    }

    /**
     * Store the settings
     */
    private function setSettings()
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
        $this->setSetting('Core', 'site_domains', array($this->getVariable('site_domain')));
        $this->setSetting('Core', 'site_html_header', '');
        $this->setSetting('Core', 'site_html_footer', '');

        // date & time
        $this->setSetting('Core', 'date_format_short', 'j.n.Y');
        $this->setSetting(
            'Core',
            'date_formats_short',
            array(
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
                'm.d.y'
            )
        );
        $this->setSetting('Core', 'date_format_long', 'l j F Y');
        $this->setSetting(
            'Core',
            'date_formats_long',
            array(
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
                'l F d, Y'
            )
        );
        $this->setSetting('Core', 'time_format', 'H:i');
        $this->setSetting('Core', 'time_formats', array('H:i', 'H:i:s', 'g:i a', 'g:i A'));

        // number formats
        $this->setSetting('Core', 'number_format', 'dot_nothing');
        $this->setSetting(
            'Core',
            'number_formats',
            array(
                'comma_nothing' => '10000,25',
                'dot_nothing' => '10000.25',
                'dot_comma' => '10,000.25',
                'comma_dot' => '10.000,25',
                'dot_space' => '10000.25',
                'comma_space' => '10 000,25'
            )
        );

        // e-mail settings
        $this->setSetting(
            'Core',
            'mailer_from',
            array('name' => 'Fork CMS', 'email' => $this->getVariable('spoon_debug_email'))
        );
        $this->setSetting(
            'Core',
            'mailer_to',
            array('name' => 'Fork CMS', 'email' => $this->getVariable('spoon_debug_email'))
        );
        $this->setSetting(
            'Core',
            'mailer_reply_to',
            array('name' => 'Fork CMS', 'email' => $this->getVariable('spoon_debug_email'))
        );

        // stmp settings
        $this->setSetting('Core', 'smtp_server', $this->getVariable('smtp_server'));
        $this->setSetting('Core', 'smtp_port', $this->getVariable('smtp_port'));
        $this->setSetting('Core', 'smtp_username', $this->getVariable('smtp_username'));
        $this->setSetting('Core', 'smtp_password', $this->getVariable('smtp_password'));

        // default titles
        $siteTitles = array(
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
            'uk' => 'мій сайт'
        );

        // language specific
        foreach ($this->getLanguages() as $language) {
            // set title
            $this->setSetting(
                'Core',
                'site_title_' . $language,
                (isset($siteTitles[$language])) ? $siteTitles[$language] : $this->getVariable('site_title')
            );
        }

        // create new instance
        require_once PATH_LIBRARY . '/external/fork_api.php';
        $api = new \ForkAPI();

        try {
            // get the keys
            $keys = $api->coreRequestKeys($this->getVariable('site_domain'), $this->getVariable('api_email'));

            // api settings
            $this->setSetting('Core', 'fork_api_public_key', $keys['public']);
            $this->setSetting('Core', 'fork_api_private_key', $keys['private']);

            // set keys
            $api->setPublicKey($keys['public']);
            $api->setPrivateKey($keys['private']);

            // get services
            $services = (array) $api->pingGetServices();

            // set services
            if (!empty($services)) {
                $this->setSetting(
                    'Core',
                    'ping_services',
                    array('services' => $services, 'date' => time())
                );
            }
        } catch (\Exception $e) {
            // we don't need those keys.
        }

        // ckfinder
        $this->setSetting('Core', 'ckfinder_license_name', 'Fork CMS');
        $this->setSetting('Core', 'ckfinder_license_key', 'VNA6-BP17-T7D3-CP1B-EMJF-X7Q3-5THF');
    }
}
