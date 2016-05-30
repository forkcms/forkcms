UPGRADE FROM 3.9 to 4.0
=======================

## SpoonHTTP is deprecated

As you all know Spoon isn't supported anymore, therefore we are trying to
replace all functionality that was provided by Spoon with SYmfony-functionality
or native code.

With this upgrade we have replace all functionality provided with SpoonHTTP.
There are several methods, each one is discussed below.

### SpoonHTTP::getContent()

Before:

```php
SpoonHTTP::getContent($url)
```

After:

```php
file_get_contents($url)
```

> Remark: fopen wrapper should be enabled.

### SpoonHTTP::redirect()

Before:

```php
\SpoonHTTP::redirect($url);
```

After:

If you are in an action you should use:

```php
$this->redirect(...)
```

In other code parts you can use:

```php
throw new RedirectException(
    'Redirect',
    new RedirectResponse($url)
);
```

### SpoonHTTP::setHeaders()

There are two use-cases that should be handled:

1. when an array was passed:

Before:

```php
\SpoonHTTP::setHeaders(
    array(
        'header 1';
        'header 2';
    )
);
```

After:

```php
header('header 1');
header('header 2');
```

2. When a single string was passed

Before:

```php
\SpoonHTTP::setHeaders('header');
```

After:

```php
header('header');
```

But you really should think about using this, as the correct way is to set the
headers on the response object and bubble it up.

### SpoonHTTP::setHeadersByCode()

This is a difficult one, as it will mostly be used to output a calculated value.
So in some cases you will need to write some logic, but you can use the example
below to output the correct header.

Before:

```php
\SpoonHTTP::setHeadersByCode(200);
\SpoonHTTP::setHeadersByCode(301);
\SpoonHTTP::setHeadersByCode(302);
\SpoonHTTP::setHeadersByCode(304);
\SpoonHTTP::setHeadersByCode(307);
\SpoonHTTP::setHeadersByCode(400);
\SpoonHTTP::setHeadersByCode(401);
\SpoonHTTP::setHeadersByCode(403);
\SpoonHTTP::setHeadersByCode(404);
\SpoonHTTP::setHeadersByCode(410);
\SpoonHTTP::setHeadersByCode(500);
\SpoonHTTP::setHeadersByCode(501);
```

After:

```php
header('HTTP/1.1 200 OK');
header('HTTP/1.1 301 Moved Permanently');
header('HTTP/1.1 302 Found');
header('HTTP/1.1 304 Not Modified');
header('HTTP/1.1 307 Temporary Redirect');
header('HTTP/1.1 400 Bad Request');
header('HTTP/1.1 401 Unauthorized');
header('HTTP/1.1 403 Forbidden');
header('HTTP/1.1 404 Not Found');
header('HTTP/1.1 410 Gone');
header('HTTP/1.1 500 Internal Server Error');
header('HTTP/1.1 501 Not Implemented');
```

## SPOON_DEBUG

SPOON_DEBUG is removed. From now on you need to check if DEBUG is on by using the kernel.debug parameter, f.e.

	if ($this->getContainer()->getParameter('kernel.debug')) { ...

## SITE_MULTILANGUAGE

SITE_MULTILANGUAGE is removed. From now on you need to check if the site is multi language by using the site.multilanguage parameter, f.e.

	if ($this->getContainer()->getParameter('site.multilanguage')) { ...

## SPOON_DEBUG_EMAIL

SPOON_DEBUG_EMAIL is removed. From now on you need to get the debug email address by using the fork.debug_email parameter, f.e.

	if ($this->getContainer()->getParameter('fork.debug_email')) { ...

## SPOON_CHARSET

SPOON_CHARSET is removed. From now on you need to get the charset by using the kernel.charset parameter, f.e.

	if ($this->getContainer()->getParameter('kernel.charset')) { ...

## Module settings

The getModuleSetting, getModuleSettings, deleteModuleSetting and setModuleSettings on the Frontend and BackendModel are now deprecated. You should use the fork.settings service. You can use it this way:

    $this->get('fork.settings')->set('Core', 'Theme', 'triton');
    $this->get('fork.settings')->get('Core', 'Theme');
    $this->get('fork.settings')->getForModule('Core');
    $this->get('fork.settings')->delete('Core', 'Theme');

This makes sure the modulesettings are more decoupled. They are now fully unit tested. We're also sure the Frontend and backend are consistent now and there is only one DB call to fetch all settings.
