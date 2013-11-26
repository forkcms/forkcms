<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * This class will be the base of the objects used in the cms
 *
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 * @author Dave Lens <dave.lens@wijs.be>
 */
class BackendBaseObject extends KernelLoader
{
    /**
     * The current action
     *
     * @var	string
     */
    protected $action;

    /**
     * The actual output
     *
     * @var string
     */
    protected $content;

    /**
     * The current module
     *
     * @var	string
     */
    protected $module;

    /**
     * Get the action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get module
     *
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Set the action
     *
     * @param string $action The action to load.
     * @param string[optional] $module The module to load.
     */
    public function setAction($action, $module = null)
    {
        // set module
        if($module !== null) $this->setModule($module);

        // check if module is set
        if($this->getModule() === null) throw new BackendException('Module has not yet been set.');

        // is this action allowed?
        if(!BackendAuthentication::isAllowedAction($action, $this->getModule())) {
            // set correct headers
            SpoonHTTP::setHeadersByCode(403);

            // throw exception
            throw new BackendException('Action not allowed.');
        }

        // set property
        $this->action = (string) $action;
    }

    /**
     * Set the module
     *
     * @param string $module The module to load.
     */
    public function setModule($module)
    {
        // is this module allowed?
        if(!BackendAuthentication::isAllowedModule($module)) {
            // set correct headers
            SpoonHTTP::setHeadersByCode(403);

            // throw exception
            throw new BackendException('Module not allowed.');
        }

        // set property
        $this->module = $module;
    }

    /**
     * Since the display action in the backend is rather complicated and we
     * want to make this work with our Kernel, I've added this getContent
     * method to extract the output from the actual displaying.
     *
     * With this function we'll be able to get the content and return it as a
     * Symfony output object.
     *
     * @return Response
     */
    public function getContent()
    {
        return new Response(
            $this->content,
            200
        );
    }
}

/**
 * This class implements a lot of functionality that can be extended by a specific action
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Davy Hellemans <davy@spoon-library.com>
 */
class BackendBaseAction extends BackendBaseObject
{
    /**
     * The parameters (urldecoded)
     *
     * @var	array
     */
    protected $parameters = array();

    /**
     * The header object
     *
     * @var	BackendHeader
     */
    protected $header;

    /**
     * A reference to the current template
     *
     * @var	BackendTemplate
     */
    public $tpl;

    /**
     * A reference to the URL-instance
     *
     * @var	BackendURL
     */
    public $URL;

    /**
     * The constructor will set some properties. It populates the parameter array with urldecoded
     * values for easy-use.
     *
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        parent::__construct($kernel);

        // get objects from the reference so they are accessible from the action-object
        $this->tpl = $this->getContainer()->get('template');
        $this->URL = $this->getContainer()->get('url');
        $this->header = $this->getContainer()->get('header');

        // store the current module and action (we grab them from the URL)
        $this->setModule($this->URL->getModule());
        $this->setAction($this->URL->getAction());

        // populate the parameter array, we loop GET and urldecode the values for usage later on
        foreach((array) $_GET as $key => $value) $this->parameters[$key] = $value;
    }

    /**
     * Check if the token is ok
     */
    public function checkToken()
    {
        $fromSession = (SpoonSession::exists('csrf_token')) ? SpoonSession::get('csrf_token') : '';
        $fromGet = SpoonFilter::getGetValue('token', null, '');

        if($fromSession != '' && $fromGet != '' && $fromSession == $fromGet) return;

        // clear the token
        SpoonSession::set('csrf_token', '');

        $this->redirect(
            BackendModel::createURLForAction(
                'index',
                null,
                null,
                array(
                     'error' => 'csrf'
                )
            )
        );
    }

    /**
     * Display, this wil output the template to the browser
     * If no template is specified we build the path form the current module and action
     *
     * @param string[optional] $template The template to use, if not provided it will be based on the action.
     */
    public function display($template = null)
    {
        // parse header
        $this->header->parse();

        /*
         * If no template is specified, we have to build the path ourself. The default template is
         * based on the name of the current action
         */
        if($template === null) {
            $template = BACKEND_MODULE_PATH . '/layout/templates/' . $this->URL->getAction() . '.tpl';
        }

        $this->content = $this->tpl->getContent($template);
    }

    /**
     * Execute the action
     */
    public function execute()
    {
        // add jquery, we will need this in every action, so add it globally
        $this->header->addJS('jquery/jquery.js', 'core', false);
        $this->header->addJS('jquery/jquery.ui.js', 'core', false);
        $this->header->addJS('jquery/jquery.ui.dialog.patch.js', 'core');
        $this->header->addJS('jquery/jquery.tools.js', 'core', false);
        $this->header->addJS('jquery/jquery.backend.js', 'core');

        // add items that always need to be loaded
        $this->header->addJS('utils.js', 'core');
        $this->header->addJS('backend.js', 'core');

        // add module js
        if(is_file(BACKEND_MODULE_PATH . '/js/' . $this->getModule() . '.js')) {
            $this->header->addJS($this->getModule() . '.js');
        }

        // add action js
        if(is_file(BACKEND_MODULE_PATH . '/js/' . $this->getAction() . '.js')) {
            $this->header->addJS($this->getAction() . '.js');
        }

        // add core css files
        $this->header->addCSS('reset.css', 'core');
        $this->header->addCSS('jquery_ui/fork/jquery_ui.css', 'core', false, false);
        $this->header->addCSS('screen.css', 'core');
        $this->header->addCSS('debug.css', 'core');

        // add module specific css
        if(is_file(BACKEND_MODULE_PATH . '/layout/css/' . $this->getModule() . '.css')) {
            $this->header->addCSS($this->getModule() . '.css');
        }

        // store var so we don't have to call this function twice
        $var = array_map('strip_tags', $this->getParameter('var', 'array', array()));

        // is there a report to show?
        if($this->getParameter('report') !== null) {
            // show the report
            $this->tpl->assign('report', true);

            // camelcase the string
            $messageName = strip_tags(SpoonFilter::toCamelCase($this->getParameter('report'), '-'));

            // if we have data to use it will be passed as the var parameter
            if(!empty($var)) $this->tpl->assign('reportMessage', vsprintf(BL::msg($messageName), $var));
            else $this->tpl->assign('reportMessage', BL::msg($messageName));

            // highlight an element with the given id if needed
            if($this->getParameter('highlight')) {
                $this->tpl->assign('highlight', strip_tags($this->getParameter('highlight')));
            }
        }

        // is there an error to show?
        if($this->getParameter('error') !== null) {
            // camelcase the string
            $errorName = strip_tags(SpoonFilter::toCamelCase($this->getParameter('error'), '-'));

            // if we have data to use it will be passed as the var parameter
            if(!empty($var)) $this->tpl->assign('errorMessage', vsprintf(BL::err($errorName), $var));
            else $this->tpl->assign('errorMessage', BL::err($errorName));
        }
    }

    /**
     * Get a parameter for a given key
     * The function will return null if the key is not available
     * By default we will cast the return value into a string, if you want something else specify it by passing the wanted type.
     *
     * @param string $key The name of the parameter.
     * @param string[optional] $type The return-type, possible values are: bool, boolean, int, integer, float, double, string, array.
     * @param mixed[optional] $defaultValue The value that should be returned if the key is not available.
     * @return mixed
     */
    public function getParameter($key, $type = 'string', $defaultValue = null)
    {
        $key = (string) $key;

        // parameter exists
        if(isset($this->parameters[$key]) && $this->parameters[$key] != '') {
            return SpoonFilter::getValue($this->parameters[$key], null, null, $type);
        }

        return $defaultValue;
    }

    /**
     * Parse to template
     */
    protected function parse()
    {

    }

    /**
     * Redirect to a given URL
     *
     * @param string $URL The URL to redirect to.
     */
    public function redirect($URL)
    {
        $response = new RedirectResponse(
            $URL, 302
        );

        /*
         * Since we've got some nested action structure, we'll send this
         * response directly after creating.
         */
        $response->send();

        /*
         * Stop code executing here
         * I know this is ugly as hell, but if we don't do this the code after
         * this call is executed and possibly will trigger errors.
         */
        exit;
    }
}

/**
 * This class implements a lot of functionality that can be extended by the real action.
 * In this case this is the base class for the index action
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendBaseActionIndex extends BackendBaseAction
{
    /**
     * A datagrid instance
     *
     * @var	BackendDataGridDB
     */
    protected $dataGrid;

    /**
     * Execute the current action
     * This method will be overwritten in most of the actions, but still be called to add general stuff
     */
    public function execute()
    {
        parent::execute();
    }

    /**
     * Parse to template
     */
    protected function parse()
    {
        parent::parse();
    }
}

/**
 * This class implements a lot of functionality that can be extended by the real action.
 * In this case this is the base class for the add action
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendBaseActionAdd extends BackendBaseAction
{
    /**
     * The form instance
     *
     * @var	BackendForm
     */
    protected $frm;

    /**
     * The backends meta-object
     *
     * @var	BackendMeta
     */
    protected $meta;

    /**
     * Parse the form
     */
    protected function parse()
    {
        parent::parse();

        if($this->frm) $this->frm->parse($this->tpl);
    }
}

/**
 * This class implements a lot of functionality that can be extended by the real action.
 * In this case this is the base class for the edit action
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendBaseActionEdit extends BackendBaseAction
{
    /**
     * DataGrid with the revisions
     *
     * @var	BackendDataGridDB
     */
    protected $dgRevisions;

    /**
     * The form instance
     *
     * @var	BackendForm
     */
    protected $frm;

    /**
     * The id of the item to edit
     *
     * @var	int
     */
    protected $id;

    /**
     * The backends meta-object
     *
     * @var	BackendMeta
     */
    protected $meta;

    /**
     * The data of the item to edit
     *
     * @var	array
     */
    protected $record;

    /**
     * Parse the form
     */
    protected function parse()
    {
        parent::parse();

        if($this->frm) $this->frm->parse($this->tpl);
    }
}

/**
 * This class implements a lot of functionality that can be extended by the real action.
 * In this case this is the base class for the delete action
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendBaseActionDelete extends BackendBaseAction
{
    /**
     * The id of the item to edit
     *
     * @var	int
     */
    protected $id;

    /**
     * The data of the item to edit
     *
     * @var	array
     */
    protected $record;

    /**
     * Execute the current action
     * This method will be overwritten in most of the actions, but still be called to add general stuff
     */
    public function execute()
    {
        parent::parse();
        parent::checkToken();
    }
}

/**
 * This class implements a lot of functionality that can be extended by a specific AJAX action
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@wijs.be>
 */
class BackendBaseAJAXAction extends BackendBaseObject
{
    const OK = 200;
    const BAD_REQUEST = 400;
    const FORBIDDEN = 403;
    const ERROR = 500;

    /**
     * Execute the action
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function execute()
    {
        return $this->getContent();
    }

    /**
     * Since the display action in the backend is rather complicated and we
     * want to make this work with our Kernel, I've added this getContent
     * method to extract the output from the actual displaying.
     *
     * With this function we'll be able to get the content and return it as a
     * Symfony output object.
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function getContent()
    {
        $statusCode = (isset($this->content['code']) ? $this->content['code'] : 200);

        return new Response(
            json_encode($this->content),
            $statusCode,
            array('content-type' => 'application/json')
        );
    }

    /**
     * Output an answer to the browser
     *
     * @param int $statusCode The status code for the response, use the available constants. (self::OK, self::BAD_REQUEST, self::FORBIDDEN, self::ERROR).
     * @param mixed[optional] $data The data to output.
     * @param string[optional] $message The text-message to send.
     */
    public function output($statusCode, $data = null, $message = null)
    {
        $statusCode = (int) $statusCode;
        if($message !== null) $message = (string) $message;

        $response = array('code' => $statusCode, 'data' => $data, 'message' => $message);
        $this->content = $response;
    }
}

/**
 * This is the base-object for config-files. The module-specific config-files can extend the functionality from this class
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendBaseConfig extends BackendBaseObject
{
    /**
     * The default action
     *
     * @var	string
     */
    protected $defaultAction = 'index';

    /**
     * The disabled actions
     *
     * @var	array
     */
    protected $disabledActions = array();

    /**
     * The disabled AJAX-actions
     *
     * @var	array
     */
    protected $disabledAJAXActions = array();

    /**
     * All the possible actions
     *
     * @var	array
     */
    protected $possibleActions = array();

    /**
     * All the possible AJAX actions
     *
     * @var	array
     */
    protected $possibleAJAXActions = array();

    /**
     * @param KernelInterface $kernel
     * @param string $module The module wherefore this is the configuration-file.
     */
    public function __construct(KernelInterface $kernel, $module)
    {
        parent::__construct($kernel);

        $this->setModule($module);

        // read the possible actions based on the files
        $this->setPossibleActions();
    }

    /**
     * Get the default action
     *
     * @return string
     */
    public function getDefaultAction()
    {
        return $this->defaultAction;
    }

    /**
     * Get the possible actions
     *
     * @return array
     */
    public function getPossibleActions()
    {
        return $this->possibleActions;
    }

    /**
     * Get the possible AJAX actions
     *
     * @return array
     */
    public function getPossibleAJAXActions()
    {
        return $this->possibleAJAXActions;
    }

    /**
     * Set the module
     *
     * We can't rely on the parent setModule function, because config could be called before any authentication is required.
     *
     * @param string $module The module to load.
     */
    public function setModule($module)
    {
        // does this module exist?
        $modules = BackendModel::getModulesOnFilesystem();
        if(!in_array($module, $modules)) {
            // set correct headers
            SpoonHTTP::setHeadersByCode(403);

            // throw exception
            throw new BackendException('Module not allowed.');
        }

        // set property
        $this->module = $module;
    }

    /**
     * Set the possible actions, based on files in folder
     * You can disable action in the config file. (Populate $disabledActions)
     */
    public function setPossibleActions()
    {
        $path = BACKEND_MODULES_PATH . '/' . $this->getModule();

        if(is_dir($path . '/actions')) {
            $finder = new Finder();
            foreach($finder->files()->name('*.php')->in($path . '/actions') as $file) {
                $action = strtolower(str_replace('.php', '', $file->getBasename()));

                // if the action isn't disabled add it to the possible actions
                if(!in_array($action, $this->disabledActions)) $this->possibleActions[$file->getBasename()] = $action;
            }
        }

        if(is_dir($path . '/ajax')) {
            $finder = new Finder();
            foreach($finder->files()->name('*.php')->in($path . '/ajax') as $file) {
                $action = strtolower(str_replace('.php', '', $file->getBasename()));

                // if the action isn't disabled add it to the possible actions
                if(!in_array($action, $this->disabledAJAXActions)) $this->possibleAJAXActions[$file->getBasename()] = $action;
            }
        }
    }
}

/**
 * This is the base-object for cronjobs. The module-specific cronjob-files can extend the functionality from this class
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class BackendBaseCronjob extends BackendBaseObject
{
    /**
     * The current id
     *
     * @var	int
     */
    protected $id;

    /**
     * Clear/removed the busy file
     */
    protected function clearBusyFile()
    {
        // build path
        $path = BACKEND_CACHE_PATH . '/cronjobs/' . $this->getId() . '.busy';

        // remove the file
        $fs = new Filesystem();
        $fs->remove($path);
    }

    public function execute()
    {
    }

    /**
     * Get the id
     *
     * @return int
     */
    public function getId()
    {
        return strtolower($this->getModule() . '_' . $this->getAction());
    }

    /**
     * Set the action
     *
     * We can't rely on the parent setModule function, because a cronjob requires no login
     *
     * @param string $action The action to load.
     * @param string[optional] $module The module to load.
     */
    public function setAction($action, $module = null)
    {
        // set module
        if($module !== null) $this->setModule($module);

        // check if module is set
        if($this->getModule() === null) throw new BackendException('Module has not yet been set.');

        // path to look for actions based on the module
        if($this->getModule() == 'core') $path = BACKEND_CORE_PATH . '/cronjobs';
        else $path = BACKEND_MODULES_PATH . '/' . $this->getModule() . '/cronjobs';

        // check if file exists
        if(!is_file($path . '/' . $action . '.php')) {
            SpoonHTTP::setHeadersByCode(403);
            throw new BackendException('Action not allowed.');
        }

        // set property
        $this->action = (string) $action;
    }

    /**
     * Set the busy file
     */
    protected function setBusyFile()
    {
        // do not set busy file in debug mode
        if(SPOON_DEBUG) return;

        // build path
        $fs = new Filesystem();
        $path = BACKEND_CACHE_PATH . '/cronjobs/' . $this->getId() . '.busy';

        // init var
        $isBusy = false;

        // does the busy file already exists.
        if($fs->exists($path)) {
            $isBusy = true;

            // grab counter
            $counter = (int) file_get_contents($path);

            // check the counter
            if($counter > 9) {
                // build class name
                $className = 'Backend' . SpoonFilter::toCamelCase($this->getModule() . '_cronjob_' . $this->getAction());

                // notify user
                throw new BackendException('Cronjob (' . $className . ') is still busy after 10 runs, check it out!');
            }
        }

        // set counter
        else $counter = 0;

        // increment counter
        $counter++;

        // store content
        $fs->dumpFile($path, $counter);

        // if the cronjob is busy we should NOT proceed
        if($isBusy) exit;
    }

    /**
     * Set the module
     *
     * We can't rely on the parent setModule function, because a cronjob requires no login
     *
     * @param string $module The module to load.
     */
    public function setModule($module)
    {
        // does this module exist?
        $modules = BackendModel::getModulesOnFilesystem();
        if(!in_array($module, $modules)) {
            // set correct headers
            SpoonHTTP::setHeadersByCode(403);

            // throw exception
            throw new BackendException('Module not allowed.');
        }

        // set property
        $this->module = $module;
    }
}

/**
 * This is the base-object for widgets
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendBaseWidget extends KernelLoader
{
    /**
     * The column wherein the widget should be shown
     *
     * @var	string
     */
    private $column = 'left';

    /**
     * The header object
     *
     * @var	BackendHeader
     */
    protected $header;

    /**
     * The position in the column where the widget should be shown
     *
     * @var	int
     */
    private $position;

    /**
     * Required rights needed for this widget.
     *
     * @var	array
     */
    protected $rights = array();

    /**
     * The template to use
     *
     * @var	string
     */
    private $templatePath;

    /**
     * A reference to the current template
     *
     * @var	BackendTemplate
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
     * @param string[optional] $template The template to use.
     */
    protected function display($template = null)
    {
        if($template !== null) $this->templatePath = (string) $template;
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
        foreach($this->rights as $rights) {
            list($module, $action) = explode('/', $rights);

            // check action rights
            if(isset($module) && isset($action)) {
                if(!BackendAuthentication::isAllowedAction($action, $module)) return false;
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
        $this->column = SpoonFilter::getValue((string) $column, $allowedColumns, 'left');
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
}
