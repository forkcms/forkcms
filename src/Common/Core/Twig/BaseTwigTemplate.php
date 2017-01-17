<?php

namespace Common\Core\Twig;

use Common\ModulesSettings;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Twig_Environment;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a twig template wrapper
 * that glues spoon libraries and code standards with twig.
 */
abstract class BaseTwigTemplate extends TwigEngine
{
    /**
     * Language.
     *
     * @var string
     */
    protected $language;

    /**
     * Should we add slashes to each value?
     *
     * @var bool
     */
    protected $addSlashes = false;

    /**
     * Debug mode.
     *
     * @var bool
     */
    protected $debugMode = false;

    /**
     * List of form objects.
     *
     * @var array
     */
    protected $forms = array();

    /**
     * List of assigned variables.
     *
     * @var array
     */
    protected $variables = array();

    /**
     * @var ModulesSettings
     */
    protected $forkSettings;

    /**
     * Spoon assign method.
     *
     * @param string|array $key
     * @param mixed  $values
     */
    public function assign($key, $values = null)
    {
        if (is_array($key)) {
            $this->assignArray($key);

            return;
        }

        // in all other cases
        $this->variables[$key] = $values;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function assignGlobal($key, $value)
    {
        $this->environment->addGlobal($key, $value);
    }

    /**
     * Assign an entire array with keys & values.
     *
     * @param array $variables This array with keys and values will be used to search and replace in the template file.
     * @param string[optional] $prefix An optional prefix eg. 'lbl' that can be used.
     * @param string[optional] $suffix An optional suffix eg. 'msg' that can be used.
     */
    public function assignArray(array $variables, $index = null)
    {
        // artifacts?
        if (!empty($index) && isset($variables['Core'])) {
            unset($variables['Core']);
            $tmp[$index] = $variables;
            $variables = $tmp;
        }

        // merge the variables array_merge might be to slow for bigger sites
        // as array_merge tend to slow down at +100 keys
        foreach ($variables as $key => $val) {
            $this->variables[$key] = $val;
        }
    }

    /**
     * Adds a form to the template.
     *
     * @param SpoonForm $form The form-instance to add.
     */
    public function addForm($form)
    {
        $this->forms[$form->getName()] = $form;
    }

    /**
     * Retrieves the already assigned variables.
     *
     * @return array
     */
    public function getAssignedVariables()
    {
        return $this->variables;
    }

    /** @todo Refactor out constants #1106
     * We need to deprecate this asap
     *
     * @param Twig_Environment $twig
     */
    protected function startGlobals(&$twig)
    {
        // some old globals
        $twig->addGlobal('var', '');
        $twig->addGlobal('CRLF', "\n");
        $twig->addGlobal('TAB', "\t");
        $twig->addGlobal('now', time());
        $twig->addGlobal('LANGUAGE', $this->language);
        $twig->addGlobal('is'.strtoupper($this->language), true);
        $twig->addGlobal('debug', $this->debugMode);

        $twig->addGlobal('timestamp', time());

        // constants that should be protected from usage in the template
        $notPublicConstants = array('DB_TYPE', 'DB_DATABASE', 'DB_HOSTNAME', 'DB_USERNAME', 'DB_PASSWORD');

        // get all defined constants
        $constants = get_defined_constants(true);

        // remove protected constants aka constants that should not be used in the template
        foreach ($constants['user'] as $key => $value) {
            if (!in_array($key, $notPublicConstants)) {
                $twig->addGlobal($key, $value);
            }
        }

        /* Setup Backend for the Twig environment. */
        if (!$this->forkSettings) {
            return;
        }

        $twig->addGlobal('timeFormat', $this->forkSettings->get('Core', 'time_format'));
        $twig->addGlobal('dateFormatShort', $this->forkSettings->get('Core', 'date_format_short'));
        $twig->addGlobal('dateFormatLong', $this->forkSettings->get('Core', 'date_format_long'));

        // old theme checker
        if ($this->forkSettings->get('Core', 'theme') !== null) {
            $twig->addGlobal('THEME', $this->forkSettings->get('Core', 'theme', 'default'));
            $twig->addGlobal(
                'THEME_URL',
                '/src/Frontend/Themes/'.$this->forkSettings->get('Core', 'theme', 'default')
            );
        }

        // settings
        $twig->addGlobal(
            'SITE_TITLE',
            $this->forkSettings->get('Core', 'site_title_'.$this->language, SITE_DEFAULT_TITLE)
        );
        $twig->addGlobal(
            'SITE_URL',
            SITE_URL
        );
        $twig->addGlobal(
            'SITE_DOMAIN',
            SITE_DOMAIN
        );

        // facebook stuff
        if ($this->forkSettings->get('Core', 'facebook_admin_ids', null) !== null) {
            $twig->addGlobal(
                'FACEBOOK_ADMIN_IDS',
                $this->forkSettings->get('Core', 'facebook_admin_ids', null)
            );
        }
        if ($this->forkSettings->get('Core', 'facebook_app_id', null) !== null) {
            $twig->addGlobal(
                'FACEBOOK_APP_ID',
                $this->forkSettings->get('Core', 'facebook_app_id', null)
            );
        }
        if ($this->forkSettings->get('Core', 'facebook_app_secret', null) !== null) {
            $twig->addGlobal(
                'FACEBOOK_APP_SECRET',
                $this->forkSettings->get('Core', 'facebook_app_secret', null)
            );
        }

        // twitter stuff
        if ($this->forkSettings->get('Core', 'twitter_site_name', null) !== null) {
            // strip @ from twitter username
            $twig->addGlobal(
                'TWITTER_SITE_NAME',
                ltrim($this->forkSettings->get('Core', 'twitter_site_name', null), '@')
            );
        }
    }

    /**
     * Should we execute addSlashed on the locale?
     *
     * @param bool $enabled Enable addslashes.
     */
    public function setAddSlashes($enabled = true)
    {
        $this->addSlashes = (bool) $enabled;
    }
}
