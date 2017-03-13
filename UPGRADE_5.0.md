UPGRADE FROM 4.x to 5.0
=======================

## API is removed

The full API is removed. If you need to expose an API for your project you should look into:

* [FOSRestBundle](https://github.com/FriendsOfSymfony/FOSRestBundle)
* [NelmioApiDocBundle](https://github.com/nelmio/NelmioApiDocBundle)

You should remove all usages of:

* `API_CORE_PATH`-constant
* `fork_api_private_key`-setting
* `fork_api_public_key`-setting
* `BackendModel::ping()`-method
* `ping_services`-setting from the Blog-module
* `ForkAPI`-class
* `api_access`-usersetting
* `ApiTestCase`-class
* `FrontendModel::pushToAppleApp()`-method


## triggerEvent

Our own implementation of events has been removed. If you need to implement the
same behaviour you should look into [Symfony's EventDispatcher Component](http://symfony.com/doc/current/components/event_dispatcher.html).

You should remove all usages of:

* `Backend\Core\Installer\ModuleInstaller->subscribeToEvent()`
* `BackendModel::triggerEvent()`
* `Common\Core\Model::subscribeToEvent()`
* `Common\Core\Model::triggerEvent()`
* `Common\Core\Model::startProcessingHooks()`
* `Common\Core\Model::unsubscribeFromEvent()`
* `FrontendModel::triggerEvent()`


## Backend\Core\Engine\Language moved to its namespace

Before:

```php
use Backend\Core\Engine\Language as ...;
```

After:

```php
use Backend\Core\Language\Language as ...;
```


## Frontend\Core\Engine\Language moved to its namespace 

Before:

```php
use Frontend\Core\Engine\Language as ...;
```

After:

```php
use Frontend\Core\Language\Language as ...;
```

## invalidateFrontendCache is removed

Since we moved to Twig, and Twig does not use the same caching mechanisme. The ``-method is removed.
If you need template caching you can take a look at [Twig cache extension](https://github.com/asm89/twig-cache-extension).

You should remove all usages of:

* `Backend\Core\Engine\Model::invalidateFrontendCache()`


## `getGroups` in favor of `getGroupId`

If your code uses `getGroupId` you should rewrite it to use `getGroups` instead.


## spoon2twig.php-script is removed

If you want to convert a Spoon template to Twig its better to take a look at:

* [Converter Collection by Thijzer](https://github.com/Thijzer/ConverterCollection)
* [Fork CMS Spoon2Twig Converter by Jesse](http://spoon2twig.jessedobbelae.re/)


## install_locale.php-script is removed

Use `php app/console forkcms:locale:import` instead.


## `sprintf`-filter is removed

In the past you could use `|sprintf` in your templates, this was a non-standard Twig Filter.
You should rewrite your code to use `|format(args)` instead.


## ContentBlocks uses Doctrine

The ContentBlocks-module now uses Doctrine.

You should remove all usages of:

* `Frontend\Modules\ContentBlocks\Engine\Model::get()`


## `Backend\Core\Engine\Base\Config->getPossibleAJAXActions()` and `Backend\Core\Engine\Base\Config->getPossibleActions` are removed

You can use `Backend\Core\Engine\Base\Config->isActionAvailable($action)` instead.


## `Frontend\Core\Engine\Url->getHost()` and `Backend\Core\Engine\Url->getHost()` are removed

You should use `$request->getHttpHost()` instead. The request-object is available in the container.


## `Frontend\Core\Engine\TemplateCustom` is removed

You can use the Twig templating service instead.


## `Common\Uri::getFilename()` is removed

You should use `Common\Uri::getUrl()` instead.

## Formbuilder validation type enum value numeric changed to number

number is the correct name for input fields so we should mimic that

run the following queries to update your database

    ALTER TABLE forms_fields_validation CHANGE `type` `type` enum('required','email','number','time') COLLATE utf8mb4_unicode_ci NOT NULL;
    UPDATE `forms_fields_validation` SET `type` = "number" WHERE `type` = "";

## Removed constants

They where in the upgrade guide of 4.0 but now we no longer assign them.

### SPOON_DEBUG

SPOON_DEBUG is removed. From now on you need to check if DEBUG is on by using the kernel.debug parameter, f.e.

	if ($this->getContainer()->getParameter('kernel.debug')) { ...

### SPOON_DEBUG_EMAIL

SPOON_DEBUG_EMAIL is removed. From now on you need to get the debug email address by using the fork.debug_email parameter, f.e.

	if ($this->getContainer()->getParameter('fork.debug_email')) { ...

### SPOON_DEBUG_MESSAGE

SPOON_DEBUG_MESSAGE is removed. From now on you need to get the debug message by using the fork.debug_message parameter, f.e.

	if ($this->getContainer()->getParameter('fork.debug_message')) { ...

## SPOON_CHARSET

SPOON_CHARSET is removed. From now on you need to get the charset by using the kernel.charset parameter, f.e.

	if ($this->getContainer()->getParameter('kernel.charset')) { ...

## PATH_WWW

PATH_WWW is removed. From now on you need to get the path to the web directory by using the site.path_www parameter, f.e.
Twig has trouble with traversing directories, so in that or similar cases you can wrap it with the `realpath` function.

    $this->getContainer()->getParameter('site.path_www')

### PATH_LIBRARY

PATH_LIBRARY is removed.

### site.path_library

site.path_library is removed.
    
## meta table is now using InnoDB

In order to use constraints in mysql 5.5 we need to use InnoDB
Execute the following queries to migrate

    RENAME TABLE meta TO old_meta;
    CREATE TABLE `meta` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `keywords` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
      `keywords_overwrite` enum('Y','N') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N' COMMENT '(DC2Type:enum_bool)',
      `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
      `description_overwrite` enum('Y','N') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N' COMMENT '(DC2Type:enum_bool)',
      `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
      `title_overwrite` enum('Y','N') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N' COMMENT '(DC2Type:enum_bool)',
      `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
      `url_overwrite` enum('Y','N') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N' COMMENT '(DC2Type:enum_bool)',
      `custom` longtext COLLATE utf8mb4_unicode_ci,
      `data` longtext COLLATE utf8mb4_unicode_ci,
      PRIMARY KEY (`id`),
      KEY `idx_url` (`url`(191))
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    INSERT INTO meta SELECT * FROM old_meta;
    DROP TABLE old_meta;

## PSR-4

We are now using PSR-4

As part of this transition the classes in the app directory are now also autoloaded and can be accessed via the ForkCMS\App namespace

routing.php has been renamed to ForkController because we need the classname to match the filename

| Old classname         | New classname                     |
|-----------------------|-----------------------------------|
| \KernelLoader         | \ForkCMS\App\KernelLoader         |
| \Kernel               | \ForkCMS\App\Kernel               |
| \ApplicationRouting   | \ForkCMS\App\ForkController       |
| \BaseModel            | \ForkCMS\App\BaseModel            |
| \ApplicationInterface | \ForkCMS\App\ApplicationInterface |
| \AppKernel            | \ForkCMS\App\AppKernel            |
