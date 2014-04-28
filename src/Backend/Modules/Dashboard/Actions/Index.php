<?php

namespace Backend\Modules\Dashboard\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Base\Widget as BackendBaseWidget;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Exception as BackendException;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Groups\Engine\Model as BackendGroupsModel;

/**
 * This is the index-action (default), it will display the login screen
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Index extends BackendBaseActionIndex
{
    /**
     * The widgets
     *
     * @var	array
     */
    private $widgets = array('left' => array(), 'middle' => array(), 'right' => array());

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        $this->loadData();
        $this->parse();
        $this->display();
    }

    /**
     * Load the data
     */
    private function loadData()
    {
        $modules = BackendModel::getModules();
        $userSequence = BackendAuthentication::getUser()->getSetting('dashboard_sequence');
        $fs = new Filesystem();

        // user sequence does not exist?
        if (!isset($userSequence)) {
            // get group ID of user
            $groupId = BackendAuthentication::getUser()->getGroupId();

            // get group preset
            $userSequence = BackendGroupsModel::getSetting($groupId, 'dashboard_sequence');
        }

        // loop all modules
        foreach ($modules as $module) {
            // build pathName
            $pathName = BACKEND_MODULES_PATH . '/' . $module;

            // you have sufficient rights?
            if (
                BackendAuthentication::isAllowedModule($module) &&
                $fs->exists($pathName . '/Widgets')
            ) {
                $finder = new Finder();
                $finder->name('*.php');

                // loop widgets
                foreach ($finder->files()->in($pathName . '/Widgets') as $file) {
                    /** @ver $file \SplFileInfo */
                    $widgetName = $file->getBaseName('.php');
                    $className = 'Backend\\Modules\\' . $module . '\\Widgets\\' . $widgetName;
                    if ($module == 'Core') {
                        $className = 'Backend\\Core\\Widgets\\' . $widgetName;
                    }

                    if (!class_exists($className)) {
                        throw new BackendException('The widgetfile is present, but the classname should be: ' . $className . '.');
                    }

                    // present?
                    $present = (isset($userSequence[$module][$widgetName]['present'])) ? $userSequence[$module][$widgetName]['present'] : false;

                    // if not present, continue
                    if (!$present) {
                        continue;
                    }

                    // create instance
                    /** @var $instance BackendBaseWidget */
                    $instance = new $className($this->getKernel());

                    // has rights
                    if (!$instance->isAllowed()) {
                        continue;
                    }

                    // hidden?
                    $hidden = (isset($userSequence[$module][$widgetName]['hidden'])) ? $userSequence[$module][$widgetName]['hidden'] : false;

                    // execute instance if it is not hidden
                    if (!$hidden) {
                        $instance->execute();
                    }

                    // user sequence provided?
                    $column = (isset($userSequence[$module][$widgetName]['column'])) ? $userSequence[$module][$widgetName]['column'] : $instance->getColumn();
                    $position = (isset($userSequence[$module][$widgetName]['position'])) ? $userSequence[$module][$widgetName]['position'] : $instance->getPosition();
                    $title = \SpoonFilter::ucfirst(BL::lbl(\SpoonFilter::toCamelCase($module))) . ': ' . BL::lbl(\SpoonFilter::toCamelCase($widgetName));
                    $templatePath = $instance->getTemplatePath();

                    // reset template path
                    if ($templatePath == null) {
                        $templatePath = BACKEND_PATH . '/Modules/' . $module . '/Layout/Widgets/' . $widgetName . '.tpl';
                    }

                    // build item
                    $item = array(
                        'template' => $templatePath,
                        'module' => $module,
                        'widget' => $widgetName,
                        'title' => $title,
                        'hidden' => $hidden
                    );

                    // add on new position if no position is set or if the position is already used
                    if ($position === null || isset($this->widgets[$column][$position])) {
                        $this->widgets[$column][] = $item;
                    } else {
                        // add on requested position
                        $this->widgets[$column][$position] = $item;
                    }
                }
            }
        }

        // sort the widgets
        foreach ($this->widgets as &$column) {
            ksort($column);
        }
    }

    /**
     * Parse the page with its widgets.
     */
    protected function parse()
    {
        parent::parse();

        // show report
        if ($this->getParameter('password_reset') == 'success') {
            $this->tpl->assign('reportMessage', BL::msg('PasswordResetSuccess', 'core'));
            $this->tpl->assign('report', true);
        }

        // assign
        $this->tpl->assign('leftColumn', $this->widgets['left']);
        $this->tpl->assign('middleColumn', $this->widgets['middle']);
        $this->tpl->assign('rightColumn', $this->widgets['right']);
    }
}
