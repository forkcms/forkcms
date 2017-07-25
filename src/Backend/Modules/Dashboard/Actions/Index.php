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
use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Base\Widget as BackendBaseWidget;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Exception as BackendException;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Groups\Engine\Model as BackendGroupsModel;

/**
 * This is the index-action (default), it will display the login screen
 */
class Index extends BackendBaseActionIndex
{
    /**
     * The widgets
     *
     * @var array
     */
    private $widgets = [];

    public function execute(): void
    {
        parent::execute();
        $this->loadData();
        $this->parse();
        $this->display();
    }

    private function loadData(): void
    {
        $modules = BackendModel::getModules();
        $filesystem = new Filesystem();

        // fetch the hidden widgets for all groups the user is in
        $hiddenWidgets = [];
        $userGroups = BackendAuthentication::getUser()->getGroups();
        $groupCount = count($userGroups);
        foreach ($userGroups as $group) {
            foreach (BackendGroupsModel::getSetting($group, 'hidden_on_dashboard') as $module => $widgets) {
                foreach ($widgets as $widget) {
                    $hiddenWidgets[] = $module . $widget;
                }
            }
        }

        // only widgets hidden for all user groups should really be hidden
        $hiddenWidgets = array_count_values($hiddenWidgets);
        $hiddenWidgets = array_filter(
            $hiddenWidgets,
            function ($hiddenCount) use ($groupCount) {
                return $hiddenCount === $groupCount;
            }
        );

        // loop all modules
        foreach ($modules as $module) {
            // build pathName
            $pathName = BACKEND_MODULES_PATH . '/' . $module;

            // you have sufficient rights?
            if (BackendAuthentication::isAllowedModule($module)
                && $filesystem->exists($pathName . '/Widgets')
            ) {
                $finder = new Finder();
                $finder->name('*.php');

                // loop widgets
                foreach ($finder->files()->in($pathName . '/Widgets') as $file) {
                    /** @ver $file \SplFileInfo */
                    $widgetName = $file->getBasename('.php');
                    $className = 'Backend\\Modules\\' . $module . '\\Widgets\\' . $widgetName;
                    if ($module == 'Core') {
                        $className = 'Backend\\Core\\Widgets\\' . $widgetName;
                    }

                    // if the widget is hidden for all the users groups, don't render it
                    if (array_key_exists($module . $widgetName, $hiddenWidgets)) {
                        continue;
                    }

                    if (!class_exists($className)) {
                        throw new BackendException('The widgetfile ' . $className . ' could not be found.');
                    }

                    // create instance
                    /** @var $instance BackendBaseWidget */
                    $instance = new $className($this->getKernel());

                    // has rights
                    if (!$instance->isAllowed()) {
                        continue;
                    }

                    $instance->execute();

                    // user sequence provided?
                    $title = \SpoonFilter::ucfirst(BL::lbl(\SpoonFilter::toCamelCase($module))) . ': ' . BL::lbl(\SpoonFilter::toCamelCase($widgetName));
                    $templatePath = $instance->getTemplatePath();

                    // reset template path
                    if ($templatePath == null) {
                        $templatePath = '/' . $module . '/Layout/Widgets/' . $widgetName . '.html.twig';
                    }

                    $templating = $this->get('template');
                    $content = trim($templating->getContent($templatePath));

                    if (empty($content)) {
                        continue;
                    }

                    // build item
                    $item = [
                        'content' => $content,
                        'module' => $module,
                        'widget' => $widgetName,
                        'title' => $title,
                    ];

                    // add on new position if no position is set or if the position is already used
                    $this->widgets[] = $item;
                }
            }
        }
    }

    protected function parse(): void
    {
        parent::parse();

        // show report
        if ($this->getRequest()->query->get('password_reset') === 'success') {
            $this->template->assign('reportMessage', BL::msg('PasswordResetSuccess', 'core'));
            $this->template->assign('report', true);
        }

        // assign
        $this->template->assign('widgets', $this->widgets);
    }
}
