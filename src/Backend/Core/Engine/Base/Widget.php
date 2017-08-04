<?php

namespace Backend\Core\Engine\Base;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Model;
use Common\Exception\RedirectException;
use ForkCMS\App\KernelLoader;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    private $position = 0;

    /**
     * Required rights needed for this widget.
     *
     * @var array
     */
    protected $rights = [];

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
    protected $template;

    /**
     * The constructor will set some properties, it populates the parameter array with urldecoded
     * values for ease of use.
     *
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        parent::__construct($kernel);

        $this->template = $this->getContainer()->get('template');
        $this->header = $this->getContainer()->get('header');
    }

    /**
     * Display, this wil output the template to the browser
     * If no template is specified we build the path form the current module and action
     *
     * @param string $template The template to use.
     */
    protected function display(string $template = null): void
    {
        if ($template !== null) {
            $this->templatePath = (string) $template;
        }
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getTemplatePath(): ?string
    {
        return $this->templatePath;
    }

    /**
     * Is this widget allowed for this user?
     *
     * @return bool
     */
    public function isAllowed(): bool
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
    protected function setColumn(string $column): void
    {
        $allowedColumns = ['left', 'middle', 'right'];
        $this->column = \SpoonFilter::getValue($column, $allowedColumns, 'left');
    }

    protected function setPosition(int $position): void
    {
        $this->position = $position;
    }

    /**
     * Redirect to a given URL
     *
     * @param string $url The URL whereto will be redirected.
     * @param int $code The redirect code, default is 302 which means this is a temporary redirect.
     *
     * @throws RedirectException
     */
    public function redirect(string $url, int $code = Response::HTTP_FOUND): void
    {
        throw new RedirectException('Redirect', new RedirectResponse($url, $code));
    }

    public function execute(): void
    {
        // placeholder
    }

    /**
     * Get the request from the container.
     *
     * @return Request
     */
    public function getRequest(): Request
    {
        return Model::getRequest();
    }
}
