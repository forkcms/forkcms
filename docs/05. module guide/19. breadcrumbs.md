# Breadcrumbs

Each blog article will have a proper page title. Adding this title to the breadcrumb allows users to easily keep track of their location.

To add data to the breadcrumb, you'll add the following lines to the parse method in detail.php.

```
// add into breadcrumb
$this->breadcrumb->addElement($this->record['title'], $this->record['full_url']);
```

This way, we'll add the title to the breadcrumb and if this is not the last item (e.g. if we're using categories), the second parameter will be activated and it will become a link so the user can navigate back to that.

The first parameter is manditory, the second is optional.