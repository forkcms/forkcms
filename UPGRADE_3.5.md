UPGRADE FROM 3.5 to 3.6
=======================

## Filesystem component

With this release we introduce the Filesystem component into Fork CMS. This
component allows us to interact with the filesystem

More info about the Console component can be found on:
http://symfony.com/doc/current/components/filesystem.html

### Upgrading your module

There are several pieces of code that should be replaced

#### Backend

In the backend the filesystem component is available through the container. So
each time you need the filesystem-object you should use:

	BackendModel::getContainer()->get('filesystem');


Below you can find all changes you should apply to make your module compatible
with this release.

##### SpoonFile::exists


	SpoonFile::exists(...);

Should become

	BackendModel::getContainer()->get('filesystem')->exists(...);

##### SpoonFile::delete

	SpoonFile::delete(...);

Should become

	BackendModel::getContainer()->get('filesystem')->remove(...);

##### SpoonFile::move

	SpoonFile::move(...);

Should become

	BackendModel::getContainer()->get('filesystem')->rename(...);

##### SpoonDirectory::exists

	SpoonDirectory::exists(...);

Should become

	BackendModel::getContainer()->get('filesystem')->exists(...);

##### SpoonDirectory::create

	SpoonDirectory::create(...);

Should become

	BackendModel::getContainer()->get('filesystem')->mdir(...);
