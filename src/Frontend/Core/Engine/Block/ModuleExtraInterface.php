<?php

namespace Frontend\Core\Engine\Block;

use Frontend\Core\Engine\TwigTemplate;
use Symfony\Component\HttpKernel\KernelInterface;

interface ModuleExtraInterface
{
    /**
     * @param KernelInterface $kernel
     * @param string $module The module to load.
     * @param string $action|null The action to load.
     * @param mixed $data The data that was passed from the database.
     */
    public function __construct(KernelInterface $kernel, string $module, string $action = null, $data = null);

    /**
     * Execute the extra
     * We will build the class name, initialise the class and call the execute method.
     */
    public function execute(): void;

    /**
     * Get the assigned template.
     *
     * @return TwigTemplate
     */
    public function getTemplate(): TwigTemplate;

    /**
     * The content that will be parsed into the template
     *
     * @return string
     */
    public function getContent(): string;

    /**
     * The template that will be used
     *
     * @return string
     */
    public function getTemplatePath(): string;
}
