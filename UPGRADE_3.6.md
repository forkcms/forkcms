UPGRADE FROM 3.5 to 3.6
=======================

### Shorthand functions

* Getting services from the dependency injection container can now be done with less code. 

In models, you can use `FrontendModel::get('database')` instead off `FrontendModel::getcontainer()->get('database')`

In actions, you shoud use `$this->get('database')` instead off FrontendModel::getcontainer()->get('database')` to avoid static functions.

* The get function in the FrontendBreadcrumb class was changed to getItems so the FrontendModel::get function didn't get overwritten.

use `$this->breadcrumb->getItems()` instead off `$this->breadcrumb->get()`