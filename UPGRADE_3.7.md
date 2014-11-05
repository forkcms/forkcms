UPGRADE FROM 3.6 to 3.7
=======================

## DependencyInjection component

Spoon Library implemented the registery pattern. This has been removed in favor of the [Dependency Injection Component](http://symfony.com/doc/current/components/dependency_injection/index.html)
of Symfony.

### Upgrading your module

* Getting

Before:
```
Spoon::get(<name>);
```

After:
```
// in actions
$this->getContainer()->get(<name>);

// in static classes (models for example)
FrontendModel::getContainer()->get(<name>);
BackendModel::getContainer()->get(<name>);
```

* Setting

Before:
```
Spoon::set(<name>, <value>);
```

After:
```
// in actions
$this->getContainer()->set(<name>, <value>);

// in static classes (models for example)
FrontendModel::getContainer()->set(<name>, <value>);
BackendModel::getContainer()->set(<name>, <value>);
```

* Checking for existance

Before:
```
Spoon::exists(<name>);
```

After:
```
// in actions
$this->getContainer()->has(<name>);

// in static classes (models for example)
FrontendModel::getContainer()->has(<name>);
BackendModel::getContainer()->has(<name>);
```

* Using the API

 The function to check if a user is authorized with the API has been renamed.

 Before:
 ```
 API::authorize()
 ```

 After:
 ```
 API::isAuthorized()
 ```

## Namespaces

Fork used a custom autoloader to be able to have it's folder structure and to
autoload classes in it. This has been removed in favor of the PSR-0 compliant
namespaces.

### Folder structure

* The 4 Fork applications (Backend, Frontend, Install and API) have been moved to
the src directory. This makes sure they can be autoloaded with the composer autolader.

* All Module names are now CamelCased. In code and in Folder names.

* All folder names containing code are now CamelCased.

Before:

    frontend
    |–– modules
       |–– your_module
          |–– actions
          |  |–– index.php
          |–– engine
             |–– model.php

after:

    src
    |–– Frontend
       |–– Modules
          |–– YourModule
             |–– Actions
                |–– Index.php
             |–– Engine
                |–– Model.php

* The extensions/upload_module action now requires this folder structure (as root the src and if necessary the library folder)

### Namespaces and use statements

* Every class name is now the exact same as the class itself

* Every class starts (in the applications) starts with the 'namespaces' statement,
stating it's exact place in the directory structure.

* Class names are now always the same as their filename (without the extension)

Before

    <?php

    class FrontendBlogModel

after

    <?php

    namespace Frontend\Modules\Blog\Engine;

    class Model

* Class names in the global namespaces should be escaped or have a usestatement.

Before

    $url = CommonUri::getUrl($nameOfMyObject);

After

    use CommonUri

    class Foo
    {
        ...
        $url = CommonUri::getUrl($nameOfMyObject);
    }

or

    $url = \CommonUri::getUrl($nameOfMyObject);

### Autoloading

* To autoload a class in another directory, you need to add use statements in
the head of your file

* Classes with a custom name (fe. the CMHelper in the Mailmotor module) don't need
require(_once) statements anymore.

Before

    require_once PATH_WWW . /frontend/modules/mailmotor/engine/cmhelper.php
    $cmHelper = new MailmotorCMHelper;

After

    use Frontend\Modules\Mailmotor\Engine\CMHelper as MailmotorCMHelper
    class ...
    {
        ...
        $cmHelper = new MailmotorCMHelper;
        ...
    }

### Misc

* Using backend datagrid functions now requires you to send the class name including namespaces or an instance of the class to work.

Before

    $dg->setColumnFunction('BackendDatagridFunctions', 'getTimeAgo', '[time]', 'time');

After

    use Backend\Core\Engine\DataGridFunctions;

    ...
    $dg->setColumnFunction(array(new DataGridFunctions(), 'getTimeAgo'), '[time]', 'time');

or

    $dg->setColumnFunction('Backend\\Core\\Engine\\DataGridFunctions', 'getTimeAgo', '[time]', 'time');

* Cronjobs now have a different url

Before

    fork.dev/backend/cronjob.php?module=core&action=send_queued_emails

After

    fork.dev/backend/cronjob?module=Core&action=SendQueuedEmails
    
* Setting a url callback now use namespaces

Before

    $this->meta->setUrlCallback('BackendBlogModel', 'getURL', array($this->record['id']));

After

    $this->meta->setUrlCallback('Backend\Modules\Blog\Engine\Model', 'getURL', array($this->record['id']));

## API isAuthorized() instead of authorize()

Before

    Api::authorize();

After

    use Api\V1\Engine\Api;
    ...
    Api::isAuthorized();

## CommonUri and CommonCookie are placed in the src/Common folder

Before

    \CommonUri::getUrl();

After

    use Common\Uri;
    ...
    Uri::getUrl();

## Mailer is now a service

The mailer can now be fetched from the container with this code:

    // in actions, widgets, ...
    $mailer = $this->getContainer()->get('mailer');

    // or shorter
    $mailer = $this->get('mailer');

    // in models or static functions
    $mailer = FrontendModel::getContainer()->get('mailer');

    // or shorter
    $mailer = FrontendModel::get('mailer');

    // adding an email is now not a static call anymore
    $mailer->addEmail($subject, $template, $variables);

The FrontendMailer and BackendMailer classes are removed in favor of this service.
