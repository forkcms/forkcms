<?php

namespace Backend\Core\Engine\Base;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Common\Exception\RedirectException;
use ForkCMS\App\KernelLoader;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Header;
use Backend\Core\Engine\TwigTemplate;

/**
 * This is the base-object for widgets
 */
class Widget extends KernelLoader
{
    /**
     * The column wherein the widget should be shown
     *
     * @var string
     */
    private $column = 'left';

    /**
     * The header object
     *
     * @var Header
     */
    protected $header;

    /**
     * The position in the column where the widget should be shown
     *
     * @var int
     */
    private $position;

    /**
     * Required rights needed for this widget.
     *
     * @var array
     */
    protected $rights = array();

    /**
     * The template to use
     *
     * @var string
     */
    private $templatePath;

    /**
     * A reference to the current template
     *
     * @var TwigTemplate
     */
    public $tpl;

    /**
     * The constructor will set some properties, it populates the parameter array with urldecoded
     * values for ease of use.
     *
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        parent::__construct($kernel);

        $this->tpl = $this->getContainer()->get('template');
        $this->header = $this->getContainer()->get('header');
    }

    /**
     * Display, this wil output the template to the browser
     * If no template is specified we build the path form the current module and action
     *
     * @param string $template The template to use.
     */
    protected function display($template = null)
    {
        if ($template !== null) {
            $this->templatePath = (string) $template;
        }
    }

    /**
     * Get the column
     *
     * @return string
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * Get the position
     *
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Get the template path
     *
     * @return mixed
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    /**
     * Is this widget allowed for this user?
     *
     * @return bool
     */
    public function isAllowed()
    {
        foreach ($this->rights as $rights) {
            list($module, $action) = explode('/', $rights);

            // check action rights
            if (isset($module) && isset($action)) {
                if (!BackendAuthentication::isAllowedAction($action, $module)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Set column for the widget
     *
     * @param string $column Possible values are: left, middle, right.
     */
    protected function setColumn($column)
    {
        $allowedColumns = array('left', 'middle', 'right');
        $this->column = \SpoonFilter::getValue((string) $column, $allowedColumns, 'left');
    }

    /**
     * Set the position for the widget
     *
     * @param int $position The position for the widget.
     */
    protected function setPosition($position)
    {
        $this->position = (int) $position;
    }

    /**
     * Redirect to a given URL
     *
     * @param string $url The URL whereto will be redirected.
     * @param int $code The redirect code, default is 302 which means this is a temporary redirect.
     *
     * @throws RedirectException
     */
    public function redirect($url, $code = 302)
    {
        $response = new RedirectResponse($url, $code);

        throw new RedirectException('Redirect', $response);
    }
}
