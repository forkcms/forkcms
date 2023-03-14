![Fork CMS](docs/img/header.jpg)

[![Build Status](https://img.shields.io/github/workflow/status/forkcms/forkcms/run-tests)](https://github.com/forkcms/forkcms/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![Latest Stable Version](https://poser.pugx.org/forkcms/forkcms/v/stable)](https://packagist.org/packages/forkcms/forkcms)
[![License](https://poser.pugx.org/forkcms/forkcms/license)](https://packagist.org/packages/forkcms/forkcms)
[![Code Coverage](https://codecov.io/gh/forkcms/forkcms/branch/master/graph/badge.svg?token=ahj70hVO29)](http://codecov.io/github/forkcms/forkcms?branch=master)
[![Documentation Status](https://img.shields.io/badge/docs-latest-brightgreen.svg)](http://docs.fork-cms.com/)
[![huntr.dev | the place to protect open source](https://cdn.huntr.dev/huntr_security_badge.svg)](https://huntr.dev)

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

If you discover any security-related issues, please email core@fork-cms.com instead of using the issue tracker.
HTML is allowed in translations because you sometimes need it. Any reports regarding this will not be accepted as a security issue. Owners of a website can narrow down who can add/edit translation strings using the group permissions.

## Bugs

If you encounter any bugs, please create an issue on [Github](https://github.com/forkcms/forkcms/issues).
If you're stuck or would like to discuss Fork CMS: [![Join our Slack channel](https://imgur.com/zXuvRdw.png) Join our Slack Channel!](https://fork-cms.herokuapp.com)

## Running the tests

We use phpunit as a test framework. It's installed when using composer install.
To be able to run them, make sure you have a database with the same credentials as
your normal database and with the name suffixed with _test.

Because we support multiple php versions it gave some issues. Therefore we use the bridge from symfony.

Running the tests:

    composer test

Running only the unit, functional, or the installer tests

     composer test -- --testsuite=functional
     composer test -- --testsuite=unit
     composer test -- --testsuite=installer

If you want to run all the tests except the ones from the installer use

    composer test -- --exclude-group=installer

## Styling the backend

The backend uses [Bootstrap](http://www.getbootstrap.com) in combination with Sass. To make changes, you should make
the changes into the scss-files, and regenerate the real css with `gulp build`.

## Yarn

We use [yarn](https://yarnpkg.com/) to install our dependencies. For now we have a `gulp`-script that moves everything to
the correct directories. So if you change the dependencies, make sure you run `gulp build`.

## Community

[![Join our Slack channel](https://imgur.com/zXuvRdw.png) Join our Slack Channel!](https://fork-cms.herokuapp.com)


_The Fork CMS team_
