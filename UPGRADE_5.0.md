UPGRADE FROM 4.x to 5.0
=======================

## Spoon library has been updated to 3.0.0

Changes:

- We require php 7.1 from now on
- When passing booleans to spoon database they are changed into 1 and 0

Fixes:

- Fix json as parameter for datagrid functions
- Fix spoon form checkboxes not working in fork 5
- Fixes `null` values from being converted to empty string in SpoonDataGrid columnFunctions
- Bugfix when having multi-array arrays

Removed:

- spoon email
- spoon ical
- spoon rest client
- spoon xmlrpc client
- spoon log
- SpoonFilter::getGetValue
- SpoonFilter::getPostValue
- atom rss feed
- spoon cookie
- spoon session

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

Use `php bin/console forkcms:locale:import` instead.


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

You should use `$request->getHttpHost()` instead. You can request the current request object from the [Common/Frontend/Backend] model with the method `getRequest`.


## `Frontend\Core\Engine\TemplateCustom` is removed

You can use the Twig templating service instead.


## `Common\Uri::getFilename()` is removed

You should use `Common\Uri::getUrl()` instead.

## FormBuilder

### validation type enum value numeric changed to number

number is the correct name for input fields so we should mimic that

run the following queries to update your database
```mysql
ALTER TABLE forms_fields_validation CHANGE `type` `type` enum('required','email','number','time') COLLATE utf8mb4_unicode_ci NOT NULL;
UPDATE `forms_fields_validation` SET `type` = "number" WHERE `type` = "";
```
### set subject and template for formbuilder

You can now set the subject and the template for the form with formbuilder.

You do need to run the following queries for that.
```mysql
ALTER TABLE forms ADD email_template VARCHAR(255) DEFAULT "Form.html.twig";
ALTER TABLE forms ADD email_subject VARCHAR(255) NULL;
ALTER TABLE forms MODIFY `method` enum('database','database_email','email') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'database_email';
```
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
```mysql
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
```

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

## php 7 typehints and return types

Since we added these everything has gotten a little more strict but it should also be more safe to use methods now

## Template::assign no longer takes arrays

you should use Template::assignArray for that

## Cronjob action has been removed

use symfony console commands instead

## ModuleInstaller::insertExtra now needs the data as an array instead of a string

you shouldn't pass the data serialised anymore and boolean values should be passed as booleans and not 'Y' or 'N'

## new dependency injection parameters

- `fork.is_installed` used to check if fork is installed 

## Password hashing

Password hashing moved from custom sha1 and md5 hashing to PHP password_hash method. To migrate existing users and
profiles they will have to do a password reset.

## Refactored out SpoonFilter::getGetValue() and SpoonFilter::getPostValue()

Replaced those methods by using the Symfony Request object.

`SpoonFilter::getGetValue()` will become `$this->getRequest()->query->get()`

`SpoonFilter::getPostValue()` will become `$this->getRequest()->request->get()`

Also removed Action::getParameter(). You should directly call the request object through `$this->getRequest()->query->get()`

## Standardise the FRONTEND_FILES directory names

The directories now have the same name as the module

| Old directory                        | New directory                            |
|--------------------------------------|------------------------------------------|
| `src/Frontend/Files/backend_users`   | `src/Frontend/Files/Users`               |
| `src/Frontend/Files/blog`            | `src/Frontend/Files/Blog`                |
| `src/Frontend/Files/pages`           | `src/Frontend/Files/Pages`               |
| `src/Frontend/Files/UserTemplate`    | `src/Frontend/Files/Pages/UserTemplate`  |
| `src/Frontend/Files/userfiles`       | `src/Frontend/Files/Core/CKFinder`       |

## Meta URL

The detail url (without slug) must be defined for meta type, so the preview url will be correct.

## Use the HTTP status code constants from Symfony\Component\HttpFoundation\Response instead of our own

Backend\Core\Engine\Base\AjaxAction and Frontend\Core\Engine\Base\AjaxAction no longer provide the http status code constants

| Old status code       | New status code                      |
|-----------------------|--------------------------------------|
| self::OK              | Response::HTTP_OK                    |
| self::BAD_REQUEST     | Response::HTTP_BAD_REQUEST           |
| self::FORBIDDEN       | Response::HTTP_FORBIDDEN             |
| self::ERROR           | Response::HTTP_INTERNAL_SERVER_ERROR |

The response class has many more constants like this for all the other status codes

## Backend Navigation

`Backend\Core\Engine\Navigation` also known as the service `navigation` when you are in the backend was stripped of some functions that where available because it extended a base class. 
This is no longer the case.
The dropped methods are:
* getAction
* getModule
* setAction
* setModule
* getContent
* redirect
* redirectToErrorPage
* getConfig

See the `Backend\Core\Engine\Base\Object` table below to find their new location.

## Backend Header

`Backend\Core\Engine\Header` also known as the service `header` when you are in the backend was stripped of some functions that where available because it extended a base class. 
This is no longer the case.
The dropped methods are:
* getAction
* getModule
* setAction
* setModule
* getContent
* redirect
* redirectToErrorPage
* getConfig

See the `Backend\Core\Engine\Base\Object` table below to find their new location.

## The class `Backend\Core\Engine\Base\Object` has been removed and the methods moved to more specific classes

These changes are mostly internal but it might be good to know in case you did something with the Object class

| Old method                    | New method                                      |
|-------------------------------|-------------------------------------------------|
| `Object::getContent`          | `\Backend\Core\Engine\Base\Action::getContent`  |
| `Object::getConfig`           | `\Backend\Core\Engine\Action::getConfig`        |
| `Object::redirect`            | `\Backend\Core\Engine\Url::redirect`            |
| `Object::redirectToErrorPage` | `\Backend\Core\Engine\Url::redirectToErrorPage` |
| `Object::getAction`           | `\Backend\Core\Engine\Url::getAction`           |
| `Object::getModule`           | `\Backend\Core\Engine\Url::getModule`           |

 The `\Backend\Core\Engine\Base\Action` class has a redirect helper method that uses the `\Backend\Core\Engine\Url` implementation so you don't need to change anything in your code for this

You can no longer access module and action as a protected property on the action or ajax class but you need to use the getters as they are now only stored in the url service

We did add helper getters so you can keep doing `$this->getAction()` and `$this->getModule()` as they will get the correct values from the url service

## Removed some abbreviations and incorrect casings

| Old method                                                     | New method                                                     |
|----------------------------------------------------------------|----------------------------------------------------------------|
| `\Common\Doctrine\Repository::generateURL`                     | `\Common\Doctrine\Repository::generateUrl`                     |
| `\Backend\Core\Engine\Meta::generateURL`                       | `\Backend\Core\Engine\Meta::generateUrl`                       |
| `\Backend\Core\Engine\Model::createURLForAction`               | `\Backend\Core\Engine\Model::createUrlForAction`               |
| `\Backend\Core\Engine\Model::getUrl`                           | `\Backend\Core\Engine\Model::getUrl`                           |
| `\Backend\Core\Engine\Model::getUrlForBlock`                   | `\Backend\Core\Engine\Model::getUrlForBlock`                   |
| `\Backend\Core\Engine\Meta::setURLCallback`                    | `\Backend\Core\Engine\Meta::setUrlCallback`                    |
| `\Backend\Core\Engine\Meta::getURL`                            | `\Backend\Core\Engine\Meta::getUrl`                            |
| `\Backend\Core\Engine\Meta::getURLOverwrite`                   | `\Backend\Core\Engine\Meta::getUrlOverwrite`                   |
| `\Backend\Core\Installer\ModuleInstaller::getDB`               | `\Backend\Core\Installer\ModuleInstaller::getDatabase`         |
| `\Backend\Modules\Blog\Engine\Model::getURL`                   | `\Backend\Modules\Blog\Engine\Model::getUrl`                   |
| `\Backend\Modules\Blog\Engine\Model::getURLForCategory`        | `\Backend\Modules\Blog\Engine\Model::getUrlForCategory`        |
| `\Backend\Modules\Faq\Engine\Model::getURL`                    | `\Backend\Modules\Faq\Engine\Model::getUrl`                    |
| `\Backend\Modules\Faq\Engine\Model::getURLForCategory`         | `\Backend\Modules\Faq\Engine\Model::getUrlForCategory`         |
| `\Backend\Modules\Locale\Engine\Model::buildURLQueryByFilter`  | `\Backend\Modules\Locale\Engine\Model::buildUrlQueryByFilter`  |
| `\Backend\Modules\Pages\Engine\Model::getFullURL`              | `\Backend\Modules\Pages\Engine\Model::getFullUrl`              |
| `\Backend\Modules\Pages\Engine\Model::getEncodedRedirectURL`   | `\Backend\Modules\Pages\Engine\Model::getEncodedRedirectUrl`   |
| `\Backend\Modules\Pages\Engine\Model::getURL`                  | `\Backend\Modules\Pages\Engine\Model::getUrl`                  |
| `\Backend\Modules\Tags\Engine\Model::getURL`                   | `\Backend\Modules\Tags\Engine\Model::getUrl`                   |
| `\Frontend\Core\Engine\Model::addURLParameters`                | `\Frontend\Core\Engine\Model::addUrlParameters`                |
| `\Frontend\Core\Engine\Model::getURL`                          | `\Frontend\Core\Engine\Model::getUrl`                          |
| `\Frontend\Core\Engine\Navigation::getURLForBlock`             | `\Frontend\Core\Engine\Navigation::getUrlForBlock`             |
| `\Frontend\Core\Engine\Navigation::getBackendURLForBlock`      | `\Frontend\Core\Engine\Navigation::getBackendUrlForBlock`      |
| `\Frontend\Core\Engine\Navigation::getURLForExtraId`           | `\Frontend\Core\Engine\Navigation::getUrlForExtraId`           |
| `\Frontend\Core\Engine\TemplateModifiers::getURL`              | `\Frontend\Core\Engine\TemplateModifiers::getUrl`              |
| `\Frontend\Core\Engine\TemplateModifiers::getURLForBlock`      | `\Frontend\Core\Engine\TemplateModifiers::getUrlForBlock`      |
| `\Frontend\Core\Engine\TemplateModifiers::getURLForExtraId`    | `\Frontend\Core\Engine\TemplateModifiers::getUrlForExtraId`    |
| `\Frontend\Modules\Tags\Engine\Model::getIdByURL`              | `\Frontend\Modules\Tags\Engine\Model::getIdByUrl`              |

| Old parameter                 | New parameter                                   |
|-------------------------------|-------------------------------------------------|
| `$this->URL`                  | `$this->url`                                    |
| `$this->frm`                  | `$this->form`                                   |
| `$this->tpl`                  | `$this->template`                               |

| Old classname                                 | New classname                                   |
|-----------------------------------------------|-------------------------------------------------|
| `\Backend\Core\Engine\Model\DataGridDB`       | `\Backend\Core\Engine\Model\DataGridDatabase`   |

## Bye bye enums

Because doctrine doesn't support enums out of the box and adding, removing, or changing an enum value is a lot of work we changed them all to varchars

We made the changes in de code and you can find the migration queries below

```mysql
ALTER TABLE blog_comments MODIFY type VARCHAR(255) NOT NULL default 'comment';
ALTER TABLE blog_comments MODIFY status VARCHAR(249) NOT NULL default 'moderation'; -- (we cant do 255 because that is too big for the index)
ALTER TABLE blog_posts MODIFY status VARCHAR(244) NOT NULL; -- (we cant do 255 because that is too big for the index)
ALTER TABLE blog_posts MODIFY hidden VARCHAR(1) NOT NULL default 'N';
ALTER TABLE blog_posts MODIFY allow_comments VARCHAR(1) NOT NULL default 'N';
ALTER TABLE content_blocks MODIFY hidden VARCHAR(1) NOT NULL default 'N';
ALTER TABLE content_blocks MODIFY status VARCHAR(255) NOT NULL DEFAULT 'active' COMMENT '(DC2Type:content_blocks_status)';
ALTER TABLE faq_feedback MODIFY processed VARCHAR(1) NOT NULL default 'N';
ALTER TABLE faq_questions MODIFY hidden VARCHAR(1) NOT NULL default 'N';
ALTER TABLE forms MODIFY method VARCHAR(255) NOT NULL default 'database_email';
ALTER TABLE forms_fields MODIFY type VARCHAR(255) NOT NULL;
ALTER TABLE forms_fields_validation MODIFY type VARCHAR(255) NOT NULL;
ALTER TABLE locale MODIFY type VARCHAR(110) NOT NULL DEFAULT 'lbl'; -- (we cant do 255 because that is too big for the index)
ALTER TABLE location MODIFY show_overview VARCHAR(1) NOT NULL DEFAULT 'Y';
ALTER TABLE meta MODIFY keywords_overwrite VARCHAR(1) NOT NULL default 'N';
ALTER TABLE meta MODIFY description_overwrite VARCHAR(1) NOT NULL default 'N';
ALTER TABLE meta MODIFY title_overwrite VARCHAR(1) NOT NULL default 'N';
ALTER TABLE meta MODIFY url_overwrite VARCHAR(1) NOT NULL default 'N';
ALTER TABLE modules_extras MODIFY type VARCHAR(255) NOT NULL;
ALTER TABLE modules_extras MODIFY hidden VARCHAR(1) NOT NULL default 'N';
ALTER TABLE pages MODIFY type VARCHAR(255) NOT NULL DEFAULT 'root' COMMENT 'page, header, footer, ...';
ALTER TABLE pages MODIFY status VARCHAR(243) NOT NULL DEFAULT 'active' COMMENT 'is this the active, archive or draft version';
ALTER TABLE pages MODIFY navigation_title_overwrite VARCHAR(1) NOT NULL DEFAULT 'N' COMMENT 'should we override the navigation title';
ALTER TABLE pages MODIFY hidden VARCHAR(1) NOT NULL DEFAULT 'N' COMMENT 'is the page hidden?';
ALTER TABLE pages MODIFY allow_move VARCHAR(1) NOT NULL DEFAULT 'Y';
ALTER TABLE pages MODIFY allow_children VARCHAR(1) NOT NULL DEFAULT 'Y';
ALTER TABLE pages MODIFY allow_edit VARCHAR(1) NOT NULL DEFAULT 'Y';
ALTER TABLE pages MODIFY allow_delete VARCHAR(1) NOT NULL DEFAULT 'Y';
ALTER TABLE pages_blocks MODIFY visible VARCHAR(1) NOT NULL DEFAULT 'Y';
ALTER TABLE profiles MODIFY status VARCHAR(255) NOT NULL;
ALTER TABLE search_index MODIFY active VARCHAR(1) NOT NULL DEFAULT 'N';
ALTER TABLE search_modules MODIFY searchable VARCHAR(1) NOT NULL DEFAULT 'N';
ALTER TABLE themes_templates MODIFY active VARCHAR(1) NOT NULL DEFAULT 'Y' COMMENT 'Is this template active (as in: will it be used).';
ALTER TABLE users MODIFY active VARCHAR(1) NOT NULL DEFAULT 'Y' COMMENT 'is this user active?';
ALTER TABLE users MODIFY deleted VARCHAR(1) NOT NULL DEFAULT 'N' COMMENT 'is the user deleted?';
ALTER TABLE users MODIFY is_god VARCHAR(1) NOT NULL DEFAULT 'N';
```

## SEOIndex and SEOFollow are now fields in the meta table

The public api didn't change for the Meta entity but if you use the array you will now find `seo_follow` and `seo_index` in the `meta` array instead of the `meta['data']` array

```mysql
ALTER TABLE meta ADD seo_follow VARCHAR(255) DEFAULT NULL COMMENT '(DC2Type:seo_follow)', ADD seo_index VARCHAR(255) DEFAULT NULL COMMENT '(DC2Type:seo_index)';
UPDATE meta
SET seo_index = "noindex"
WHERE data IS NOT NULL AND data LIKE '%seo_index";s:7:"noindex"%';
UPDATE meta
SET seo_index = "index"
WHERE data IS NOT NULL AND data LIKE '%seo_index";s:5:"index"%';
UPDATE meta
SET seo_index = "none"
WHERE data IS NOT NULL AND data LIKE '%seo_index";s:4:"none"%';
UPDATE meta
SET seo_follow = "nofollow"
WHERE data IS NOT NULL AND data LIKE '%seo_follow";s:8:"nofollow"%';
UPDATE meta
SET seo_follow = "follow"
WHERE data IS NOT NULL AND data LIKE '%seo_follow";s:6:"follow"%';
UPDATE meta
SET seo_follow = "none"
WHERE data IS NOT NULL AND data LIKE '%seo_follow";s:4:"none"%';
```

In the installer the method `insertMeta` has a new signature so we can add the seo_follow and seo_index

## Move from Y and N to true booleans

We used to use Y and N as booleans, since doctrine uses 1(true) and 0(false) we switched to that, if you use it somewhere in your code you will have to update them to the corresponding booleans

The DBALType `enum_bool` has also been removed because of this

```mysql
UPDATE blog_posts SET hidden = CASE WHEN hidden = "Y" THEN 1 ELSE 0 END;
ALTER TABLE blog_posts MODIFY hidden TINYINT(1) DEFAULT '0' NOT NULL;
UPDATE blog_posts SET allow_comments = CASE WHEN allow_comments = "Y" THEN 1 ELSE 0 END;
ALTER TABLE blog_posts MODIFY allow_comments TINYINT(1) NOT NULL default '0';
UPDATE content_blocks SET hidden = CASE WHEN hidden= "Y" THEN 1 ELSE 0 END;
ALTER TABLE content_blocks MODIFY hidden TINYINT(1) NOT NULL default '0';
UPDATE faq_feedback SET processed = CASE WHEN processed= "Y" THEN 1 ELSE 0 END;
ALTER TABLE faq_feedback MODIFY processed TINYINT(1) NOT NULL default '0';
UPDATE faq_questions SET hidden = CASE WHEN hidden= "Y" THEN 1 ELSE 0 END;
ALTER TABLE faq_questions MODIFY hidden TINYINT(1) NOT NULL default '0';
UPDATE location SET show_overview = CASE WHEN show_overview= "Y" THEN 1 ELSE 0 END;
ALTER TABLE location MODIFY show_overview TINYINT(1) NOT NULL DEFAULT '1';
UPDATE meta SET keywords_overwrite = CASE WHEN keywords_overwrite= "Y" THEN 1 ELSE 0 END;
ALTER TABLE meta MODIFY keywords_overwrite TINYINT(1) NOT NULL default '0';
UPDATE meta SET description_overwrite = CASE WHEN description_overwrite= "Y" THEN 1 ELSE 0 END;
ALTER TABLE meta MODIFY description_overwrite TINYINT(1) NOT NULL default '0';
UPDATE meta SET title_overwrite = CASE WHEN title_overwrite= "Y" THEN 1 ELSE 0 END;
ALTER TABLE meta MODIFY title_overwrite TINYINT(1) NOT NULL default '0';
UPDATE meta SET url_overwrite = CASE WHEN url_overwrite= "Y" THEN 1 ELSE 0 END;
ALTER TABLE meta MODIFY url_overwrite TINYINT(1) NOT NULL default '0';
UPDATE modules_extras SET hidden = CASE WHEN hidden= "Y" THEN 1 ELSE 0 END;
ALTER TABLE modules_extras MODIFY hidden TINYINT(1) NOT NULL default '0';
UPDATE pages SET navigation_title_overwrite = CASE WHEN navigation_title_overwrite= "Y" THEN 1 ELSE 0 END;
ALTER TABLE pages MODIFY navigation_title_overwrite TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'should we override the navigation title';
UPDATE pages SET allow_move = CASE WHEN allow_move= "Y" THEN 1 ELSE 0 END;
ALTER TABLE pages MODIFY allow_move TINYINT(1) NOT NULL DEFAULT '1';
UPDATE pages SET allow_children = CASE WHEN allow_children= "Y" THEN 1 ELSE 0 END;
ALTER TABLE pages MODIFY allow_children TINYINT(1) NOT NULL DEFAULT '1';
UPDATE pages SET hidden = CASE WHEN hidden= "Y" THEN 1 ELSE 0 END;
ALTER TABLE pages MODIFY hidden TINYINT(1) NOT NULL DEFAULT '1';
UPDATE pages SET allow_edit = CASE WHEN allow_edit= "Y" THEN 1 ELSE 0 END;
ALTER TABLE pages MODIFY allow_edit TINYINT(1) NOT NULL DEFAULT '1';
UPDATE pages SET allow_delete = CASE WHEN allow_delete= "Y" THEN 1 ELSE 0 END;
ALTER TABLE pages MODIFY allow_delete TINYINT(1) NOT NULL DEFAULT '1';
UPDATE pages_blocks SET visible = CASE WHEN visible= "Y" THEN 1 ELSE 0 END;
ALTER TABLE pages_blocks MODIFY visible TINYINT(1) NOT NULL DEFAULT '1';
UPDATE search_index SET active = CASE WHEN active= "Y" THEN 1 ELSE 0 END;
ALTER TABLE search_index MODIFY active TINYINT(1) NOT NULL DEFAULT '0';
UPDATE search_modules SET searchable = CASE WHEN searchable= "Y" THEN 1 ELSE 0 END;
ALTER TABLE search_modules MODIFY searchable TINYINT(1) NOT NULL DEFAULT '0';
UPDATE themes_templates SET active = CASE WHEN active= "Y" THEN 1 ELSE 0 END;
ALTER TABLE themes_templates MODIFY active TINYINT(1) NOT NULL DEFAULT '1' COMMENT 'Is this template active (as in: will it be used).';
UPDATE users SET active = CASE WHEN active= "Y" THEN 1 ELSE 0 END;
ALTER TABLE users MODIFY active TINYINT(1) NOT NULL DEFAULT '1' COMMENT 'is this user active?';
UPDATE users SET deleted = CASE WHEN deleted= "Y" THEN 1 ELSE 0 END;
ALTER TABLE users MODIFY deleted TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'is the user deleted?';
UPDATE users SET is_god = CASE WHEN is_god= "Y" THEN 1 ELSE 0 END;
ALTER TABLE users MODIFY is_god TINYINT(1) NOT NULL DEFAULT '0';
```

## Move some form types to the common namespace so that they also can be used in the frontend

Now that the frontend is also bootstrap we won't need to maintain duplicate form templates.
The following form types have been moved

| Old classname                       | New classname                 |
|-------------------------------------|-------------------------------|
| `\Backend\Form\Type\FileType`       | `\Common\Form\FileType`       |
| `\Backend\Form\Type\ImageType`      | `\Common\Form\ImageType`      |
| `\Backend\Form\Type\CollectionType` | `\Common\Form\CollectionType` |

## Swiftmailer

The default used to be `mail` but that has been removed in favour of `sendmail`.
It works more or less the same. More information can be found at the [documentation of swiftmailer](https://swiftmailer.symfony.com/docs/sending.html#the-sendmail-transport)

```mysql
UPDATE modules_settings SET value = 's:8:"sendmail";' WHERE name = 'mailer_type' AND value = 's:4:"mail";';
```

## Symfony has been updated to 3.3

All things symfony have been updated to the latest stable versions.
Consult the upgrade guides of symfony to see what changed. If you don't have deprecation notices on 2.8 you can just upgrade.

- [upgrade from 2.x to 3.0 guide](https://github.com/symfony/symfony/blob/v3.3.0/UPGRADE-3.0.md)
- [upgrade from 3.0 to 3.1 guide](https://github.com/symfony/symfony/blob/v3.3.0/UPGRADE-3.1.md)
- [upgrade from 3.1 to 3.2 guide](https://github.com/symfony/symfony/blob/v3.3.0/UPGRADE-3.2.md)
- [upgrade from 3.2 to 3.3 guide](https://github.com/symfony/symfony/blob/v3.3.0/UPGRADE-3.3.md)

### Requests

Because the current request is no longer directly accessible from the container we added some helper methods to the [Common/Frontend/Backend] model class, `Model::requestIsAvailable()` and `Model::getRequest()`

### Twig

You can't assign globals to twig after initialisation anymore.
Because of that we removed the `addGlobal` function from `\Frontend\Core\Engine\TwigTemplate`

If you want to add a global anyway you should use assignGlobal as it uses a custom workaround.

### Console

Moved `app/console` to `bin/console`

### Logs

Moved `app/logs` to `var/logs`

### Cache

Moved `app/cache` to `var/cache`

## Cookies

We don't use SpoonCookie anymore and therefore also the class Common\Cookie has been removed.
You can now use the new cookie service `fork.cookie`

The values of cookies are also no longer automatically serialized.

## Sessions

We removed SpoonSession and have switched fully to the symfony sessions
``
We also added a shortcut to get the current session: \Common\Core\Model::getSession()`

## Tests

The login and logout methods in the tests now require a client as parameter since we will log in/out that specific client
