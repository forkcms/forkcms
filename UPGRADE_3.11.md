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
file_get_content($url)
```

> Remark: fopen wrapper should be enabled.

### SpoonHTTP::redirect()

Before:

```php
\SpoonHTTP::redirect($url);
```

After:

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

But you really should think about using this, as the correct way is to bubble 
set the headers on the response object and bubble it up.
