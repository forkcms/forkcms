![Fork CMS](docs/img/header.jpg)

[![Build Status](https://travis-ci.org/forkcms/forkcms.svg?branch=testsuite)](https://travis-ci.org/forkcms/forkcms)
[![Latest Stable Version](https://poser.pugx.org/forkcms/forkcms/v/stable.svg)](https://packagist.org/packages/forkcms/forkcms)
[![License](https://poser.pugx.org/forkcms/forkcms/license.svg)](https://packagist.org/packages/forkcms/forkcms)
[![Code Coverage](https://scrutinizer-ci.com/g/forkcms/forkcms/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/forkcms/forkcms/?branch=master)
[![Slack Status](https://fork-cms.herokuapp.com/badge.svg)](https://fork-cms.herokuapp.com/)
[![Documentation Status](https://img.shields.io/badge/docs-latest-brightgreen.svg)](http://docs.fork-cms.com/)

## Installation

1. Make sure you have [composer](https://getcomposer.org/) installed.
2. Run `composer create-project forkcms/forkcms .` in your document root.
3. Browse to your website
4. Follow the steps on-screen
5. Have fun!

### Dependencies

**Remark**: If you are using GIT instead of composer create-project or the zip-file from [http://www.fork-cms.com](http://www.fork-cms.com), you
should install our dependencies. The dependencies are handled by [composer](http://getcomposer.org/)

To install the dependencies, you can run the command below in the document-root:

	composer install -o

## Security

If you discover any security related issues, please email core@fork-cms.com instead of using the issue tracker.


## SSL

If you want to activate https redirection, go into the .htaccess file and uncomment the lines about https.

## Bugs

If you encounter any bugs, please create an issue on [Github](https://github.com/forkcms/forkcms/issues).
If you're stuck or would like to discuss Fork CMS, talk to us on [slack](https://fork-cms.herokuapp.com/)!

## Running the tests

We use phpunit as a test framework. It's installed when using composer install.
To be able to run them, make sure you have a database with the same credentials as
your normal database and with the name suffixed with _test.

Because we support multiple php versions it gave some issues. Therefore we use the bridge from symfony.

Running the tests:

    composer test

Running only the unit tests or the functional tests

     composer test -- --testsuite=functional
     composer test -- --testsuite=unit

## Styling the backend

The backend uses [Bootstrap](http://www.getbootstrap.com) in combination with Sass. To make changes, you should make
the changes into the scss-files, and regenerate the real css with `gulp build`.

## Yarn

We use [yarn](https://yarnpkg.com/) to install our dependencies. For now we have a `gulp`-script that moves everything to
the correct directories. So if you change the dependencies, make sure you run `gulp build`.

## Discussion

- Slack: <https://fork-cms.herokuapp.com/>

_The Fork CMS team_
