UPGRADE FROM 3.5 to 3.6
=======================

## Filesystem component

With this release we introduce the Filesystem component into Fork CMS. This
component allows us to interact with the filesystem.

More info about the Filesystem component can be found on:
http://symfony.com/doc/current/components/filesystem.html

### Upgrading your module

There are several pieces of code that should be altered. Below you can find all
changes you should apply to make your module compatible with this release.

General rule of thumb is: use the filesystem-component if you are generating
files or are working with directories, in other cases the native PHP-methods
are preferred.

* SpoonFile::exists / SpoonDirectory::exists

   Before:
	```
	SpoonFile::exists(...)
	```

   After:
	```
	// depending on the situation you should use
	is_file(...)
	// or
	is_dir(...)
	// or
	file_exists(...)
	// or create a filesystem-object
	use Symfony\Component\Filesystem\Filesystem;
    use Symfony\Component\Filesystem\Exception\IOException;
    ...
	$fs = new Filesystem();
	$fs->exists(...)
	```

* SpoonFile::delete

   Before:
	```
	SpoonFile::delete(...)
	```

   After:
	```
	use Symfony\Component\Filesystem\Filesystem;
    use Symfony\Component\Filesystem\Exception\IOException;
    ...
	$fs = new Filesystem();
	$fs->remove(...)
	```

* SpoonFile::move

   Before:
	```
	SpoonFile::move(...)
	```

   After:
	```
	use Symfony\Component\Filesystem\Filesystem;
    use Symfony\Component\Filesystem\Exception\IOException;
    ...
	$fs = new Filesystem();
	$fs->rename(...)
	```

* SpoonFile::getContent

   Before:
	```
	SpoonFile::getContent(...)
	```

   After:
	```
	file_get_contents(...)
	```

Just make sure you test if the file exists, otherwise this will trigger warnings.

* SpoonFile::setContent

   Before:
	```
	SpoonFile::setContent(...)
	```

   After:
	```
	use Symfony\Component\Filesystem\Filesystem;
    use Symfony\Component\Filesystem\Exception\IOException;
	...
	$fs = new Filesystem();
	$fs->dumpFile(...)
	```

* SpoonFile::getExtension

   Before:
	```
	$extension = SpoonFile::getExtension(...)
	```

   After:
	```
	use Symfony\Component\HttpFoundation\File\File;
	...
	$file = new File(...);
	$extension = $file->getExtension();
	```

* SpoonDirectory::create

  Before:
	```
	SpoonDirectory::create(...);
	```

   After:
	```
	use Symfony\Component\Filesystem\Filesystem;
    use Symfony\Component\Filesystem\Exception\IOException;
    ...
	$fs = new Filesystem();
	$fs->mkdir(...)
	```

* SpoonFileException

  Before:
	```
	SpoonFileException(...)
	```

   After:
	```
	use Symfony\Component\Filesystem\Filesystem;
    use Symfony\Component\Filesystem\Exception\IOException;
    ...
	IOException(...)
	```

## Finder component

With this release we introduce the Finder component into Fork CMS. This
component allows us to find files and directories via an intuitive fluent
interface.

More info about the Finder component can be found on:
http://symfony.com/doc/master/components/finder.html

### Upgrading your module

* SpoonDirectory::getList / SpoonFile::getList

   Wherever you need to deal with a list of files you will probably loop them.
   Symfony has a nice way of iterating through the files in a path.

   Before:
    ```
    $files = SpoonFile::getList($path, '/(.*).php/');
	foreach($files as $file)
	{
		Spoon::dump($file);
	}
    ```

   After:
	```
	$finder = new Finder();
	$finder->name('*.php');
	foreach ($finder->files()->in($path) as $file) {
		Spoon::dump($file->getRealPath());
	}
	```

### Shorthand functions

* Getting services from the dependency injection container can now be done with less code. 

In models, you can use `FrontendModel::get('database')` instead off `FrontendModel::getcontainer()->get('database')`

In actions, you shoud use `$this->get('database')` instead off FrontendModel::getcontainer()->get('database')` to avoid static functions.

* The get function in the FrontendBreadcrumb class was changed to getItems so the FrontendModel::get function didn't get overwritten.

use `$this->breadcrumb->getItems()` instead off `$this->breadcrumb->get()`
