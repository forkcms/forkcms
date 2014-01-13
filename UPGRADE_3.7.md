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
       |–– YourModule
          |–– Actions
          |  |–– Index.php
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

    use Backend\Core\Engine\DatagridFunctions;

    ...
    $dg->setColumnFunction(new DatagridFunctions(), 'getTimeAgo', '[time]', 'time');

or

    $dg->setColumnFunction('Backend\\Core\\Engine\\DatagridFunctions', 'getTimeAgo', '[time]', 'time');

