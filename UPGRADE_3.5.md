UPGRADE FROM 3.5 to 3.6
=======================

### Filesystem component

With this release we introduce the Filesystem component into Fork CMS. This
component allows us to interact with the filesystem

More info about the Console component can be found on:
http://symfony.com/doc/current/components/filesystem.html

#### Upgrading your module

There are several pieces of code that should be replaced

##### Backend

	SpoonFile::exists(...)

Should become

	BackendModel::getContainer()->get('filesystem')->exists(...)