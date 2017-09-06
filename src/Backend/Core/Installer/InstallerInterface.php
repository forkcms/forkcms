<?php

namespace Backend\Core\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The installer interface
 */
interface InstallerInterface
{

    public function install(): void;

    /**
     * @return null|\Symfony\Component\Console\Input\InputInterface
     */
    public function getInput(): ?InputInterface;

    /**
     * @param null|\Symfony\Component\Console\Input\InputInterface $input
     */
    public function setInput($input): void;

    /**
     * @return null|\Symfony\Component\Console\Output\OutputInterface
     */
    public function getOutput(): ?OutputInterface;

    /**
     * @param null|\Symfony\Component\Console\Output\OutputInterface $output
     */
    public function setOutput($output): void;

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getVariable(string $name);

    /**
     * @param array $variables
     */
    public function setVariables(array $variables): void;

    /**
     * @return array
     */
    public function getPromptVariables(): array;
}
