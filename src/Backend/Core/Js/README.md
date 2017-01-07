Readme CKEditor & CKFinder
===========================

# CKEditor
## Upgrading CKEditor
The `ckeditor` folder does not contain manual changes. Therefore, we can generate a new build that contains our plugins and replace the entire folder.

* Open `ckeditor/build-config.js` file and visit (2): custom the online builder link
* Modify the build and download the optimized version of CKEditor.
* Remove the current ckeditor folder in Fork CMS and replace it with your new build
* Test if all functionality still works

## Plugins
We use the "Full" preset of CKEditor which already contains a lot of toolbar icons and plugins. We also install some additional plugins:

* Ajax Data Loading: provides additional api's to work with Ajax/XML
* CodeMirror Source syntax highlighting: enables syntax highlighting in the Source tab of CKEditor.
* Document Properties: manipulate the metadata of a HTML Document when in full-page mode
* Extra form attributes: lets you specify a class & id in forms
* File tools: exposes tools like UploadRepository and FileLoader to simplify operations on files like loading/uploading.
* IFrame Dialog Field: for embedding html pages into content
* Keep TextSelection: in combination with the CodeMirror plugin, this keep the text-selection when switching between WYSIWYG and source mode.
* Media Embed: paste embedded code from Youtube, Vimeo, ...
* Notification Aggregator: allows you to aggregate multiple tasks into a single notification
* Placeholder: lets you create and edit placeholders (non-editable text fragments).
* Stylesheet parser: will parse the frontend stylesheet and fill the Styles dropdown
* Table Resize: adds support for table column resizing with your mouse.
* UI Color Picker: provides a color picker dialog to select and preview the editor user interface color.

