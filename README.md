# README

## Installation

1. Upload all of the content into your document root
3. Browse to your website
4. Follow the steps on-screen
5. Have fun!

### Dependencies

**Remark**: If you are using GIT instead of the zip-file from [http://www.fork-cms.com](http://www.fork-cms.com), you
should install our dependencies. The dependencies are handled by [composer](http://getcomposer.org/)

To install the dependencies, you can run the command below in the document-root:

	composer install -o

## Bugs

If you encounter any bugs, please create an issue on [Github](https://github.com/forkcms/forkcms/issues).
If you're stuck or would like to discuss Fork CMS, check out the [forum](http://forum.fork-cms.com)!

## Running the tests

We use phpunit as a test framework. It's installed when using composer install.
To be able to run them, make sure you have a database with the same credentials as
your normal database and with the name suffixed with _test.

Running the tests:

    vender/bin/phpunit -c app/

Running only the unit tests or the functional tests

    vendor/bin/phpunit -c app --testsuite=functional
    vendor/bin/phpunit -c app --testsuite=unit

## Discussion
- IRC: irc.freenode.org #forkcms
- E-mail: <info@fork-cms.com> for any questions or remarks.



_The Fork CMS team_
