# README

[![Build Status](https://travis-ci.org/forkcms/forkcms.svg?branch=testsuite)](https://travis-ci.org/forkcms/forkcms)
[![Latest Stable Version](https://poser.pugx.org/forkcms/forkcms/v/stable.svg)](https://packagist.org/packages/forkcms/forkcms)
[![License](https://poser.pugx.org/forkcms/forkcms/license.svg)](https://packagist.org/packages/forkcms/forkcms)
[![Code Coverage](https://scrutinizer-ci.com/g/forkcms/forkcms/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/forkcms/forkcms/?branch=master)
[![Slack Status](https://fork-cms.herokuapp.com/badge.svg)](https://fork-cms.herokuapp.com/)

## Installation

1. Make sure your have [bower](http://bower.io/) and [composer](https://getcomposer.org/) installed.
2. Run `composer create-project forkcms/forkcms .` in your document root.
3. Browse to your website
4. Follow the steps on-screen
5. Have fun!

### Dependencies

**Remark**: If you are using GIT instead of composer create-project or the zip-file from [http://www.fork-cms.com](http://www.fork-cms.com), you
should install our dependencies. The dependencies are handled by [composer](http://getcomposer.org/)

To install the dependencies, you can run the command below in the document-root:

	composer install -o

## Bugs

If you encounter any bugs, please create an issue on [Github](https://github.com/forkcms/forkcms/issues).
If you're stuck or would like to discuss Fork CMS, check out the [forum](http://www.fork-cms.com/community/forum)!

## Running the tests

We use phpunit as a test framework. It's installed when using composer install.
To be able to run them, make sure you have a database with the same credentials as
your normal database and with the name suffixed with _test.

Running the tests:

    ./bin/phpunit

Running only the unit tests or the functional tests

    ./bin/phpunit --testsuite=functional
    ./bin/phpunit --testsuite=unit

## Styling the backend

The backend uses [Bootstrap](http://www.getbootstrap.com) in combination with Sass. To make changes, you can either
apply your styles in /src/Backend/Core/Layout/Css/screen.css or use Sass.

To use Sass, you first need to install it on your system, more info can be found here: [http://sass-lang.com/install](http://sass-lang.com/install).
If you use the command line, you can run the following command in your document root:

    sass --watch src/Backend/Core/Layout/Sass:src/Backend/Core/Layout/Css

## Discussion

- Slack: <https://fork-cms.herokuapp.com/>


_The Fork CMS team_
