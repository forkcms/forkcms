<?php

namespace Install\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Response;

/**
 * The base-class for all installer-steps
 *
 * @author Davy Hellemans <davy@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Matthias Mullie <forkcms@mullie.eu>
 */
class Step extends \KernelLoader
{
    /**
     * Form
     *
     * @var \SpoonForm
     */
    protected $frm;

    /**
     * List of all modules (required, hidden and found on the filesystem).
     * Keep in mind that the order of the required modules is the actual
     * order in which we're going to install these modules.
     *
     * @var array
     */
    protected $modules = array(
        'required' => array(
            'Locale',
            'Settings',
            'Users',
            'Groups',
            'Extensions',
            'Pages',
            'Search',
            'ContentBlocks',
            'Tags'
        ),
        'hidden' => array('Authentication', 'Dashboard', 'Error'),
        'optional' => array()
    );

    /**
     * Step number
     *
     * @var    int
     */
    protected $step;

    /**
     * Template
     *
     * @var    \SpoonTemplate
     */
    protected $tpl;

    /**
     * @param int $step The step to load.
     */
    public function __construct($step)
    {
        // set setup
        $this->step = (int) $step;
    }

    /**
     * Initialize the base models and the required step objects.
     */
    public function initialize()
    {
        // skip step 1
        if ($this->step > 1) {
            // load spoon
            require_once 'spoon/spoon.php';

            // create template
            $this->tpl = new \SpoonTemplate();
            $this->tpl->setForceCompile(true);
            $this->tpl->setCompileDirectory(dirname(__FILE__) . '/../Cache/');

            // assign the path
            if (defined('PATH_WWW')) {
                $this->tpl->assign('PATH_WWW', PATH_WWW);
            }

            // create form
            $this->frm = new \SpoonForm('step' . $this->step, '/install?step=' . $this->step);
            $this->frm->setParameter('class', 'forkForms submitWithLink');
            $this->frm->setParameter('id', 'installForm');
        }
    }

    /**
     * @return Response
     */
    public function display()
    {
        $stepTemplate = dirname(__FILE__) . '/../Layout/Templates/Step' . $this->step . '.tpl';
        $stepContent = $this->tpl->getContent($stepTemplate, false, true);

        return new Response($stepContent, 200);
    }

    /**
     * Parses the form into the template
     */
    protected function parseForm()
    {
        if ($this->step > 1) {
            $this->frm->parse($this->tpl);
        }
    }
}
