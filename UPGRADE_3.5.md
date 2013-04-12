UPGRADE FROM 3.5 to 3.6
=======================

### Console component

With this release we introduced the Console component into Fork CMS. This
component allows us to implemente commands that can be run from the console.

More info about the Console component can be found on:
http://symfony.com/doc/current/components/console/introduction.html

In this first release we moved some existing bash-scripts into the console.

#### Using the console

You can list all available commands by typing the command below in a
terminal-window:

	app/console

As you will see a nice help is shown.

#### Available commands

* cache:remove, this will clear all the caches.
* core:prepare_for_reinstall, this will remove all caches and configuration
	files, so you can easily reinstall.