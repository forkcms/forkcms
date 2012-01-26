3.2.3 (xxxx-xx-xx)
--
Bugfixes:

* Core: snippets: made the languages to get the templates for dynamic.
* Blog: fixed improper redirect that caused blog archive pagination to malfunction.


3.2.2 (2012-01-24)
--
Improvements:

* Core: added an isPrice filter, also for text fields.
* Core: added the text color for the hover states of buttons.
* Core: when a datagrid column has a certain column title(hidden, visible, published, active), the datagrid will now automatically detect non-visible rows and mark them this way.
* Core: init Facebook for its JS SDK when an admin or app id is set.
* API: Added API::isValidRequestMethod($method) that checks if the request method of an incoming API call is valid for a given API method'.
* Analytics: Fixed the cronjobs execution time, should only run once a day.
* Blog, content blocks, pages: replaced the buttons for the use of versions or drafts by links with icons for consistency.
* Blog: API methods are now limited to their correct request methods.
* Extensions: improved the validation of the positions, as mention on http://forkcms.lighthouseapp.com/projects/61890/tickets/256 by Dieter W.
* Formbuilder: altered the splitchar, so "," can be used in values for dropdowns, checkboxes or radiobuttons.
* Pages: editor will be larger by default.
* Search: use a saveIndex function instead of addIndex and editIndex.

Bugfixes:

* Core: module specific locale are now parsed in the templates when used in cronjobs, thanks to annelyze.
* Core: Click To Edit above the editor should behave from now on.
* Core: added the options for the theme-specific editor_content.css and and screen.css that will be loaded in the editor.
* Analytics: Fixed the labels for keywords and referrers when updating through ajax.
* Extensions: Made clear in cronjob info text that cronjob execution times have to be spread on servers with multiple fork installations.
* Extensions: a notice was triggered when using invalid templatesyntax, as mentioned on http://forkcms.lighthouseapp.com/projects/61890/tickets/257.
* Mailmotor: improve visibility of ckeditor in mailmotor.


3.2.1 (2012-01-17)
--
Improvements:

* Core: upgraded jQueryUI to 1.8.17
* Core: added a generic method to output CSV-files, which uses the user-settings for splitchar and line-ending.
* Core: it is now possible to set an empty string as recipient name in the mailers.
* Extensions: only modules with a valid name will be included in the list of installable modules.
* Blog: added an option for the god user to enable or disable the upload image functionallity for the blog module.
* Installer: added a check for subfolders.
* All: template-options for available actions are now available for all modules and thus also prefixed with the modulename.

Bugfixes:

* Core: added missing locale for ckeditor & ckfinder.
* Core: when not in debugmode the dialog-patch wasn't included in the minified JS-file.
* Pages: fixed reset previous value when editing editor block.
* Spoon: when deleting a cookie we now set the expiration date far in the past to prevent that users with an incorrect system time can still use deleted cookies.
* API: all illegal characters are now wrapped with CDATA tags.
* Blog: API calls now show the most recent version of a blog title.


3.2.0 (2012-01-10)
--
Improvements:

* Core: integrated CKEditor into Fork CMS.
* Core: added an extra check (parent has to be td.checkbox) for the row selection within tables in the backend.
* Core: added cookie containing unique visitor id.
* Core: add a class 'noSelectedState' to the table of a dataGrid to prevent the selected state to show for every row in the datagrid with a checked checkbox.
* Core: added maxItems and afterAdd options for the multipleSelectbox.
* Core: added a possibility to add an extra to all pages when installing forkcms with the installer function addDefaultExtra. The extra will be added to all pages without this extra.
* Core: you can now add items to the search index in the installer of your module.
* Core: fixed core engine url notice in frontend/ and backend/ (Notice: Undefined offset: 1) by removing an unused $get var.
* Pages: when adding an editor field, the editor will immediately open.
* Pages: the sitemap now correctly displays subpages.
* Extensions: modules may now also include files in /library/external. 
* All: actions where the user has no rights for, are no longer shown.

Bugfixes:

* Core: fixed core template override from within module action.
* Core: added #xfbml=1 to the Facebook connect URL so Facebook plugins also work when there's no Facebook app id given in the settings tab.
* ContentBlocks: Fixed a bug where a hidden content block assigned to a page would trigger a PHP Notice.
* Extensions: fixed module-warnings system.
* Extensions: fixed module upload.
* Users: Fixed a bug that was triggered when editing a user that was not the loggedin user and when the loggedin user was not a god user.
* Spoon: dropdown opt-group's values were reset by the array_merge function.


3.1.9 (2012-01-03)
--
Improvements:

* Core: the frontend CSS-minifier supports @import-statements from now on.
* Core: you can't select redirect-languages that aren't active.

Bugfixes:

* Blog: meta should be deleted before the items are deleted.


3.1.8 (2011-12-27)
--
Improvements:

* Core: added public methods to FrontendPage to fetch page id & page record.
* Core: split instantiation & execution of extras, allowing extra's to be aware of other extra's on a page.
* All: fixed a lot of <label>-tags, which improves the accessibility.
* All: added some hidden labels for formelements that doesn't have a <label>-tag linked, which improves the accessibility.
* Authentication: don't mention which field is required seperatly.
* Core: no more need to use the addslashes-modifier in JS-files, it will be handled by Fork. Introduced while fixing the bug mentioned by Tristan Charbonnier on http://forkcms.lighthouseapp.com/projects/61890/tickets/249.
* Core: added a generic class that will enable you to use iCal-feeds.

Bugfixes:

* Core: confirmmessages weren't working anymore, as Samuel Debruyn mentioned on http://forkcms.lighthouseapp.com/projects/61890/tickets/251
* Extensions: when a templates was edited and an form-error was shown the added blocks weren't shown correctly again.
* Tags: related widget wasn't using the current language, patch provided by czytom on http://forkcms.lighthouseapp.com/projects/61890/tickets/243
* Tags: the url for a tag that contains spaces wasn't calculated correctly, mentioned by czytom on http://forkcms.lighthouseapp.com/projects/61890/tickets/244
* Mailmotor: also replace https while linking the account
* Formbuilder: changing the value of the submitbutton wasn't working, mentioned by phill on http://forkcms.lighthouseapp.com/projects/61890/tickets/252.
* Installer: show the warning when library/external is not readable.


3.1.7 (2011-12-20)
--
Improvements:

* Core: tableSequenceByDragAndDrop allows the module to be chosen, so sequences from other modules might be used.
* Tags: tagpages don't have any SEO-value, so don't index them.
* Core: created multibyte-safe ucfirst variant and applied it throughout Fork CMS.

Bugfixes:

* Core: fixed XSS vulnerability (as mentioned on: http://packetstormsecurity.org/files/107815/forkcms-xss.txt)
* Core: fixed page-unload warning on IE.
* Core: it is now possible to use translations that don't exist in English.


3.1.6 (2011-12-13)
--
Improvements:

* Core: when not in debugmode non-existing files or faulty urls shouldn't trigger an exception but a 404.
* Core: added an getModules method to FrontendModule, analog to the backend method.
* Core: the direct actions are no longer shown in the navigation.
* Core: don't add a timestamp to the urls of well known libraries in the backend.
* Core: automagic canonical-urls.
* Core: added a new modifier stripnewlines which will remove all newlines in a string, so JS can handle it. 
* Core: added schema.org properties in the default HTML and in the Triton-theme.
* Locale: added some missing locale, thx to wouter H, http://forkcms.lighthouseapp.com/projects/61890/tickets/237
* Locale: the missing items are now sorted by application, type, module and name.
* Locale: added translations for Spanish (by Alberto Aguayo - http://www.bikumo.com)
* Location: rewrote most of the JS, because the map wasn't showing the markers correctly, as mentioned by Wouter H on http://forkcms.lighthouseapp.com/projects/61890/tickets/238

Bugfixes:

* Pages: default blocks now apply correctly on new pages.
* Pages: removed extras still linked to page now no longer trigger an error.
* Core: settings exclude & checked values on setMassActionCheckboxes now works again.
* Formbuilder: fixed a typo, as mentioned by Tommy Van de Velde on http://forkcms.lighthouseapp.com/projects/61890/tickets/239.
* Core: when adding a JS-file with a ? in it the timestamp was appended with a ?.
* Locale: improved translations for German (by Philipp Kruft - http://www.novacore.de)


3.1.5 (2011-12-06)
--
Bugfixes:

* Analytics: when refreshing the traffic sources a parseerror was thrown, as reported by Wouter H. on http://forkcms.lighthouseapp.com/projects/61890/tickets/231 


3.1.4 (2011-11-29)
--
Improvements:

* Core: upgraded jQuery to 1.7.1  
* Core: upgraded jQuery Tools to 1.2.6
* Core: direct action pages get prefilled meta information again.
* Users: when adding a user and there is only one group it will be checked by default.

Bugfixes:

* Profiles: display name was not being urilized.
* Tags: it is no longer impossible to fetch related items with the same id as your source item.
* Core: fixed js issue in triton.
* Core: fixed a typo, thx to Danny Korpan.
* Extensions: when using spaces in the format-part of the template XML, the templates weren't build correctly.


3.1.3 (2011-11-22)
--
Improvements:

* Added the possibility to easily adjust detailed page settings when you are a god user.
* SVN folders will now be skipped when running the remove_cache script.

Bugfixes:

* Core: fixed an issue with the checkboxTextfieldCombo function.
* Core: fixed minified media queries in the backend CSS manually, the minify script itself has to be adjusted though.
* Core: fixed inputCheckbox positioning inside datagrids.
* Core: fixed the row selected state in the datagrid when the selectAll checkbox was clicked.
* Core: fixed the layout dataFilter function since it scoped the wrong, lowercased class of the dataFilter.
* Extensions: prevented PHP warnings when no info.xml is available.
* Core: fixed an issue with drag and drop in the backend.
* Locale: importing other languages then EN is possible again.
* Core: fixed an issue with the user-dropdown. 
* Formbuilder: fixed an issues with the default error messages.
* Blog: deleting a blog image caused a SQL error.
* Core: upgraded the YUI-compressor to 2.4.7, see https://github.com/yui/yuicompressor/blob/master/doc/CHANGELOG.
* Core: javascript error fixed when no href is provided in the share widget.
* Core: fixed confirmation-dialog, wasn't closing when the cancel-button was clicked.
* Tools: frontend and backend globals were not deleted when running prepare_for_reinstall.


3.1.2 (2011-11-15)
--
Bugfixes:

* Core: fixed an issue with items that used .live().
* Pages: fixed an issue with dynamically added elements using .data().


3.1.1 (2011-11-15)
--
Improvements:

* Locale: refactored inportXML method to also be used by installer (rather than 2 seperate "different yet the same" functions).
* Extensions: add cronjobs info to info.xml, informational al well as for checking whether all cronjobs are set.
* Core: upgraded Highcharts to 2.1.8.
* Core: major improvements (codestyling, spelling, performance, ...) for JS, credits to Thomas.
* Core: upgraded jQuery to 1.7
* Installer: when the form in step 6 (where the actual install happens) is submitted the button will be replaced with a spinner to indicate the installer is running.
* Analytics: added a warning when trying to link a profile when no profile was selected.
* Blog: when there are 2 or more categories with at least one item in it, the category will be added in the breadcrumb.

Bugfixes:

* Editing tags wasn't working because of an error in the SQL-statement in the FAQ-module.
* Missing label, as reported by Wouter Hechtermans on http://forkcms.lighthouseapp.com/projects/61890/tickets/212.
* Pages: closing the dialog did not discard the content correctly.
* Core: autocomplete on tags wasn't working due the change of the AJAX-calls.


3.1.0 (2011-11-08)
--
Improvements:

* Core: Upgraded TinyMCE to 3.4.7
* Core: TinyMCE now includes all languages that are possible in the interface-language-dropdown.
* Core: the keys when asking for a locale item now get camelcased so you can add enum values f.e. when using them in a datagrid.
* Formbuilder: made it possible to add multiple receivers, as requested by Jeroen De Sloovere. 
* Pages: added a widget that shows the subpages as blocks with their title and meta description.

Bugfixes:

* Core: when requesting the meta-navigation while there are no items an exceptions was thrown, mentioned by Niels on http://forkcms.lighthouseapp.com/projects/61890/tickets/195.
* Core: when editing non-active languages the files parsed through javascript.php were using the default language, as pointed out by Simon on http://forkcms.lighthouseapp.com/projects/61890/tickets/200.
* Core: fix default module, action, language in JS - was messed up on dashboard.
* Core: fix issue in template compiler; nested iterations where child ends in name of parent, did not work.
* Core: removed the guessing of the library path in the installer. When Spoon can't be located a textbox will be shown wherin you can enter the path to Spoon.
* Core: fixed issue when displaying empty pages without blocks linked.
* ContentBlocks: fixed a database exception when deleting content blocks, thx to Sam Tubbax.
* Extensions: fixed typo, as mentioned on http://forkcms.lighthouseapp.com/projects/61890/tickets/207 by Bart.
* Extensions: editing a template without default-data was triggering a notices, as mentioned by Bart on http://forkcms.lighthouseapp.com/projects/61890/tickets/204.
* Extensions: confirmmessages through pure Javascript don't support sprintf through the template-engine, thx to Bart, see http://forkcms.lighthouseapp.com/projects/61890/tickets/203.
* Extensions: ignore hidden files when validating the uploaded zip-files, thx to Dieter W, see http://forkcms.lighthouseapp.com/projects/61890/tickets/208.
* Formbuilder: when a field isn't required, but should be validated as an emailaddress it was forced to be filled in.
* Formbuilder: the language wasn't saved correctly into the extras after editing a form, so it was shown for all languages, as mentioned by Simon on http://forkcms.lighthouseapp.com/projects/61890/tickets/201.
* Location: invalid item was used in the template, and the JS should only be excuted after jQuery is loaded, as mentioned by Floris on http://forkcms.lighthouseapp.com/projects/61890/tickets/205.
* Pages: classname for sitemap was wrong.
* Pages: navigation now contains valid depth-key in template.
* Tags: inline editing wasn't working anymore due the new way of using AJAX.
* Tools: improved whitespace-check in codesniffer.
* Faq: fixed collation of table faq_feedback.


3.0.0 (2011-11-01)
--
Improvements:

* Core: completely re-invented the blocks system; it's now position-driven.
* Core: introduce the concept of positions that can contain an arbitrary number of blocks.
* Pages: merged tabs "Content" & "Template" to present a more straightforward UI.
* Pages: added ability to order blocks on a page.
* Pages: created fallback-system for blocks that were assigned to no-longer-existing positions.
* Pages: added the posibility to show/hide a block.
* Pages: edit HTML content in TinyMCE in a dialog.
* Core: updated installer.
* Core: updated template creation in backend.
* Core: updated theme Triton to be position-based.
* Pages: added the possibility to either completely overwrite or re-use existing blocks when updating a template.
* Core: removed has_extra and extra_ids from pages database and replaced it with joins resulting in the same result but based upon real data (rather than just relying on the existing scripts.)
* Installer: added 'getTemplateId' function to easily fetch a template id.
* Installer: added 'warnings' to warn for less optimal systems but allow installation anyway.
* Installer: added improved test for mod_rewrite (will produce warning if not enabled.)
* Installer: refactored code: every step now doublechecks all previous steps and redirects back on error.
* Core: updated folder structure to prevent installation issues with folders needing to be outside the document root.
* Core: removed "markup" folder, this is now available at http://www.fork-cms.com/markup.
* Core: allow for non-standard characters to be used in urls.
* Core: validate slugs that are being added with javascript whilst typing the title using meta-class.
* Core: updated default favicon.
* Pages: updated pages getNavigation; the 'includeChildren' parameter was useless.
* Core: refactored javascript ajax-calls.
* Installer: refactored installation of dashboard widgets.
* Analytics: dashboard widgets are now added for all users upon installation.
* Profiles: refactored action names to better represent their purpose.
* Extensions: uploaded modules can be installed.
* Extensions: uploaded themes can be installed.
* Extensions: it is now possible to install modules via ZIP upload in the CMS.
* Extensions: it is now possible to install themes via ZIP upload in the CMS.
* Core: the active state of modules has been stripped. This is no longer useful.
* Core: new coding standards have been applied.
* Locale: added translations for Chinese (by Millie Lin - http://www.witmin.com)
* Locale: added translations for French (by Matthias Budde - http://www.flocoon.com & Jeremy Swinnen - http://blog.stratos42.com)
* Locale: added translations for German (by Philipp Kruft - http://www.novacore.de)
* Locale: added translations for Hungarian (by Bota David - http://kukac7.hu)
* Locale: added translations for Italian (by NebuLab - http://nebulab.it)
* Locale: added translations for Russian (by Медведев Илья - http://iam-medvedev.ru)
* Locale: added partial translations for Turkish (by Serkan Yildiz - http://twitter.com/#!/GeekOfWeb)
* Locale: added partial translations for Polish (by Pawel Frankowski - http://www.blog.elimu.pl & Konrad Confue Przydział - http://confue.xaa.pl)
* Blog: the blog module now standard has an image field.


2.6.13 (2011-10-18)
--
Improvements:

* Locale: make it possible to browse translations for all modules at once.
* Mailmotor: email address can't be edited; change code to reflect this.
* Core: Facebook open Graph-tags will now be parsed when an app OR admin-id is configured.
* Core: added a share-widget, see http://www.fork-cms.com/knowledge-base/detail/using-the-share-widgetmenu.

Bugfixes:

* Content Blocks: some backend functions didn't take into account the current working language.
* Content Blocks: exclude invalid templates.
* Installer: some very specific Apache-version will prepend the Apache-variables with REDIRECT_, thx to Steve De Veirman.
* Pages: when adding more then 1 module to a page you will get a nice error message instead of a PHP error.


2.6.12 (2011-10-11)
--
Improvements:

* Core: removed empty method.
* Core: detect if .htaccess is available and mod_rewrite enabled in the installer.
* Core: when adding a filefield it is now possible to easily show a label with the available extensions.


2.6.11 (2011-10-04)
--
Improvements:

* Core: Made the parent_id available in the template.
* Core: Upgraded TinyMCE to 3.4.6
* Core: Made the Facebook integration work with the signed requests.

Bugfixes:

* Core: re-added some missing locale into the imagemanager, thx to carroarmato0, see: http://forkcms.lighthouseapp.com/projects/61890/tickets/185-268-moxicode-unassigned-literals.
* Core: fixed some errors in the api-methods for blog.
* Core: fixed a bug where updating a page template tried to input data in a non-existing database column.
* Core: fixed a typo in the dutch disclaimer, thx to Bart Deslagmulder, see: http://forkcms.lighthouseapp.com/projects/61890/tickets/190.


2.6.10 (2011-09-27)
--
Improvements:

* Search: IP address is no longer shown in statistics.
* Core: Improved config to let TinyMCE cleanup Internet Explorer HTML.
* Search: Search won't show the 404 page anymore, thx to carroarmato0, see: http://forkcms.lighthouseapp.com/projects/61890/tickets/186-268-search-finds-404-page.

Bugfixes:

* Groups: when no bundled actions were available a PHP notice was thrown.
* Dashboard: validate if a position is already taken.
* Pages: sort sequences after checking its existence.


2.6.9 (2011-09-20)
--
Improvements:

* Core: Upgraded jQuery to 1.6.4.
* Core: When an image/filefield is added in the backend the max_upload_size is added as a helpmessage, thx to Martijn Dierckx, see: http://forum.fork-cms.com/discussions/general/59-display-max-upload-size-backend.
* Core: Added an api-method to remove an apple-device token.
* Core: Emails are now send base64 encoded. This to prevent that linebreaks, which are added when the max text line length is reached, corrupt the content.
* Blog: Added an api-method to grab a single comment.
* Blog: When calling blog.comments.UpdateStatus you can pass multiple ids by seperating them with a ,.
* Tags: Overview is now sorted alphabetically.

Bugfixes:

* Blog: Fixed a bug in the blog module where it called an unexisting FrontendTag-function, thx to jelmersnoeck.


2.6.8 (2011-09-13)
--
Improvements:

* Core: TinyMCE link-list is now sorted according the pages-tree, as requested by Frederik (http://forum.fork-cms.com/discussions/feature-requests/11-tinymce-linklist-sort).
* Core: Mails from formbuilder will contain the sitetitle instead of Fork CMS, thx to Frederik.
* Core: Updated the schema.

Bugfixes:

* Blog: deleting a draft no longer triggers an error.
* Blog: fix deletion of category: check for blogposts in category did not check blog status.
* Groups: permission management now works correctly in Chrome.


2.6.7 (2011-09-09)
--
Bugfixes:

* Install triggered an error "Headers already sent".

Improvements:

* Core: Upgraded TinyMCE to 3.4.5 - fixed Opera issues with editor.
* Core: Updated JS utils.urlise to better reflect the SpoonFilter::urlise (. should also convert to dash)
* Core: Shorter GA-tracking code (thx to Jeroen Desloovere)


2.6.6 (2011-09-06)
--
Bugfixes:

* Facebook-class: fixed oAuth-calls.
* Autoloader was replacing too much, when using the module name inside an action (eg: mass_files_action in the module files), thx to freshface.

Improvements:

* Core: upgraded jQuery to 1.6.3.
* Core: added two method (getDate & getTime) as BackendDatagrid-functions, as requested by Frederik (see: http://forum.fork-cms.com/discussions/general/48-shortdate-for-formatting-dat-in-datagrid).


2.6.5 (2011-08-30)
--
Improvements:

* Core: backend navigation is now dynamically generated. Module installers can set their navigation tree.
* Core: improved default-filtering in locale. From now on frontend and all types are default. 
* Core: return id of inserted mail.
* Pages: sitemap page will now also display child pages.


2.6.4 (2011-08-23)
--
Bugfixes:

* Location: it is now possible to use multiline content inside the marker.
* Core: overwriting javascript-files in a theme now works fine.

Improvements:

* Core: upgraded jQueryUI to 1.8.16.


2.6.3 (2011-08-16)
--
Bugfixes:

* Api: when the response isn't an array notices where thrown.
* Locale: analyse now correctly handles dynamic translations.
* Core: local file inclusion check was not MS Windows-proof, fixed now (thx to iarwain01)
* Core: the metaCustom was never parsed.
* Pages: when there are no footer-pages an notice was triggered (as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/176).
* Pages: When moving a page the correct page is now checked for allow_children

Improvements:

* Core: added a modifier to camelcase strings.
* Core: when adding new default blocks to an existing template, update all corresponding pages that have no content in those blocks to the new default.
* Core: when Akismet can't tell us if a comment is spam, we mark it as an item in moderation.
* Core: added functionality to set a callback after an item is saved with inline editing.
* Pages: internal redirect can have children from now on, thx to Annelyze.
* Pages: added an experimental copy-action.
* Locale: highlight empty items in the overview.


2.6.2 (2011-08-09)
--
Bugfixes:

* Core: template custom was not being parsed inside blocks.

Improvements:

* Core: upgraded jQueryUI to 1.8.15.
* Core: added a way to read a cookie through JS.
* Core: Upgraded TinyMCE to 3.4.4


2.6.1 (2011-08-02)
--
Bugfixes:

* Search: search page was installed twice.
* Core: when in debugmode the confirmation for leaving the page is disabled.
* Core: the check that decided to show the confirmation-message wan't handling empty strings very well.
* Core: fixed some JS-errors (thx to Frederik Heyninck)

Improvements:

* Core: added utils.string.html5(), when you pass a HTML5-chunk it will be converted so IE will render it correctly (based on innerShiv).


2.6.0 (2011-07-26)
--
Bugfixes:

* Blog: Tags are now correctly fetched and displayed.
* Blog: Comments-action was broken due an invalid call on $this in a static method.
* Installer: Setting the librarypath was using an array instead of the first item in that array.

Improvements:

* Core: Items marked as direct action won't show up in page-title, breadcrumb, meta, ...
* Core: Better handling of meta-information. Each item will be unique, Some new methods are introduced (addLink, addMetaData, addMetaDescription, addMetaKeywords, addOpenGraphData), they replace: setMeta*.
* Core: Added an SEO-item in the advanced-settings-section. For now only noodp and noydir are implemented.
* Core: Added advanced SEO-settings in the SEO-tab (index,follow).
* Core: Added a setting to use no-follow on links inside user-comments.
* Core: If Google Analytics is available, all outgoing links will be tracked by eventtracking.
* Core: When Google Analytics is linked, and the tracking-code isn't found in the header/footer-HTML it will be added.


2.5.2 (2011-07-19)
--
Bugfixes:

* Core: Event logging now uses absolute paths to prevent usage of undefined constants.


2.5.1 (2011-07-19)
--
Bugfixes:

* Installer: Installer now uses `is_writable` to check if a folder is writable. Thx to Mattias Geniar (http://forkcms.lighthouseapp.com/projects/61890/tickets/172).
* Spoon: On rare occasions iconv would trow an error that it can't convert strings.
* Core: js.php could be misused.


2.5.0 (2011-07-12)
--
Bugfixes:

* Pages: Don't show hidden extras in the widget- and block-dropdowns.
* Pages: hidden modules_extras don't get shown in the template anymore.
* Pages: when editing a page with a external redirect there was an error because of the disabled field, fixed the JS, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/169.

Improvements

* Core: Removed code to initialize the session, this is just useless and prevents caching-proxies to work by default, thx to Mattias Geniar.
* Core: upgraded TinyMCE to 3.4.3.2
* Core: Pub/sub-system, see: http://www.fork-cms.com/blog/detail/pubsub-in-fork


2.4.2 (2011-07-05)
--
Improvements:

* Core: Facebook doesn't provide an API-key anymore, so code is altered to reflect this.
* Core: siteHTMLFooter should be append after the JS-files.
* Core: implemented social-tracking for GA, will only be executed if Google Analytics is used, and facebook or twitter are integrated.
* Core: upgraded jQuery to 1.6.2 and jQueryUI to 1.8.14.


2.4.1 (2011-06-28)
--
Bugfixes:

* Blog: blogger import script now downloads the images correctly.


2.4.0 (2011-06-21)
--
Bugfixes:

* ContentBlocks: template wasn't selected when editing the block.

Improvements:

* Profiles: added profiles module to handle onsite (frontend) profiles.
* Groups: addes groups module to handle backend user privileges.
* Locale: added quick-edit.
* Core: extras (blocks or widgets) now simulate their own scope concerning templates.
* Core: no more language if there is just one language enabled.
* Core: handling of meta/links tags is now down through code, therefor you can overrule existing values.
* Core: removed deprecated methods.


2.3.1 (2011-06-14)
--
Bugfixes:

* Formbuilder: fix jquery error causing formbuilder to malfunction
* Proper implementation of .prop().
* Analyse-action was using invalid arguments for SpoonFilter::toCamelCase().

2.3.0 (2011-06-07)
--
Bugfixes:

* Core: when the metafields are disabled we don't have any values in the POST. When an error occurs in the other fields of the form the meta-fields would be cleared. As reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/164.
* Pages: moving pages for a non-active language failed, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/163.

Improvements:

* Core: Upgraded to jQuery 1.6.1
* Core: Upgraded to jQuery UI 1.8.13
* Core: Upgraded TinyMCE to 3.4.2

2.2.0 (2011-06-01)
--
Bugfixes:

* Bugfix: inline editing for blog-categories wasn't working anymore, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/132.
* Bugfix: when an error was thrown while inline editing, the element wasn't destroyed.
* Bugfix: title of blogpost had inline-editing enabled while this isn't implemented.
* Bugfix: options aren't visible elements for webkit-browsers. So submittinng the first parent-form was failing in mass-actions. 
* Bugfix: improve "incomplete" (autocomplete) searching for multiple words (only the last word should be considered incomplete.)
* Bugfix: removed empty widgets, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/150.
* Bugfix: hover-event wasn't unbind correctly when sorting the widgets was done.
* Bugfix: importing addresses into the mailmotor was borked, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/143.
* Bugfix: focusFirst was focusing on an element on hidden tabs, as reported on http://forkcms.lighthouseapp.com/projects/61890-fork-cms/tickets/153.
* Bugfix: click on tab wasn't working decent in IE, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/154. 
* Bugfix: page-revisions were interfering with blog-revisions, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/151.
* Bugfix: theme-css is now loaded again into TinyMCE.
* Bugfix: only remove language from querystring when we have multiple languages.
* Bugfix: backend interface language was not set according to our installer selection.
* Bugfix: added the correct anchor on the blog commentform, fixes: http://forkcms.lighthouseapp.com/projects/61890/tickets/159.
* Bugfix: create category dialog in blogmodule wasn't working when there weren't no categories, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/160
* Bugfix: datefields weren't populated with the date that was set, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/161.

Improvements:

* Core: when using datefields with till, from, range set, it will be validated according the type.
* Core: changed theme's folder layout to match codebase folder layout - folder 'layout/' should be included in theme;.
* Core: changed addJavascript function to addJS (consistency with addCSS + less typing.)
* Core: added class FrontendTheme with functions pertaining to themes. Bundled functionality to fetch a file's theme path to this class.
* Core: added template modifier 'getPath', to fetch the desired path to a file (theme file if available, core file otherwise.)
* Core: no more need to enter absolute path to core or theme template in an include (still possible though); template compiler will use theme file if available, core file otherwise.
* Core: removed scratch theme. Triton is now the default theme.
* Core: templates are now linked to a theme.
* Core: only show templates belonging to a specific selected theme.
* Core: theme switch will automatically link pages to templates of the new theme.
* Core: when a new template with less blocks is selected for a page, the redundant blocks' content will be kept.
* Core: blocks data does not get lost when switching template/theme.
* Core: content blocks can now be linked to a content block-template.
* Core: locales analyse-tool will check only the active modules from now on.
* Core: added a jQuery-plugin to implement a passwordGenerator.
* Core: added the possibility to add attachments to the frontend/backend mailers.
* Core: when calling *Form::getTemplateExample() an example that reflect the correct markup for that application will be returned.
* Core: default jQuery-theme is now Aristo (see: http://taitems.tumblr.com/post/482577430/introducing-aristo-a-jquery-ui-theme).
* Core: made datepickerstuff available in the frontend.
* Core: made it possible to change the amount of blocks for templates that are in use. When blocks are removed, the content will no longer be shown; when blocks are added, the defaults will be pushed to the existing pages.
* Blog: creating categories can now be done without leaving the add/edit screen.
* Blog: changes to improve the usability: no more default category, users are forced to select a category if there are multiple categories.
* Blog: when filtered on a category and clicked on link to add a post the category will be prefilled.
* Blog: in the dropdown to filter on a category the count is now included.
* Blog: when canceling adding a new category the previous selected one will be reselected, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/147
* Pages: Redirecting to childpages (if there is no content) will now use 301-code. 
* Pages: implemented drafts, similar to Blog.
* Pages: when changing templates the textual-content isn't deleted anymore.
* Locale: you can now import/export locale from/to xml. The installers also use xml's.
* Locale: export for missing locale.
* Locale: remove deprecated insertLocale function.
* Locale: created an incredibly nasty hotfix for some deprecated PHP functionality.
* Mailmotor: added extra validation (reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/137).
* Mailmotor: added extra validation for adding address(es).
* Mailmotor: adding multiple addresses now uses the multipleTextbox-functionality.
* Installer: refactored pages installation.
* Installer: split up step languages & modules into 2 steps; moved db step behind those.
* Installer: ask for backend interface languages seperate from frontend languages.

2.1.0 (2011-03-14)
--
* IE-stylesheets aren't loaded by default, this is the task of the slices (as requested/indicated by Yoni)
* Force forms to use UTF-8
* Blog categories now use the meta-object
* Cronjobs can now be triggerd from the CLI, as requested on http://forkcms.lighthouseapp.com/projects/61890/tickets/120
* Core: improvments for numberformatting
* Tools: scripts are now using find
* Bugfix: Disabled the imagemanagers contextmenu because there are still issues (according to the TinyMCE developers :s)
* Bugfix: $_GET-parameters were double urldecode, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/82
* Bugfix: navigation used to give notices with hidden/excluded pages
* Bugfix: autoloader path to frontendbaseajaxaction was incorrect
* Bugfix: setting a language for an ajax-call on non-multilanguage sites wat a bit * ehm * fubar
* Bugfix: when deleting a content_block, the HTML-field for the block should be set to an empty string (thx to Frederik Heyninck)
* Core: renamed addCSSFile to addCSS, to reflect the backend (thx to Frederik Heyninck)
* Bugfix: loading of classes in getWarnings should use SpoonFilter::toCamelCase instead of ucfirst (thx to Frederik Heyninck)
* Pages: added the getForTags-method
* Core: added JS to enable placeholder-behaviour in browsers that doesn't support placeholders
* Core: made it possible for cronjobs to use BackendMailer
* Core: made it possible to use setColumnConfirm on other columns that haven't a link as value
* Core: made it possible to highlight elements via a GET parameter
* Bugfix: tagBox and multipleTextbox now work as intended when typing the splitchar
* Bugfix: multipleTextbox no longer blocks the form submit in specific cases
* Bugfix: widgets now also use theme templates (if available)
* Core: using data-attribute instead of rel
* Blog: reimplemented drafts
* Bugfix: recalculate num_comments so the new revision has the correct count
* Core: fixed a lot of code to reflect the styleguide
* Testimonials: made the module language-dependant (as it should be)
* Bugfix: SpoonFileCSV was triggering a warning when no exclude-columns were provided
* Core: backendMailer will remove tags from the subject
* Core: added the passwordword generator into the frontend
* Bugfix: selecting a template in teh mailmotor wasn't working in IE, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/99
* Bugfix: non-existing items were included in the getAll-method
* Core: non-absolute urls are replaced when using Backend/Frontend-mailer
* Bugfix: multipleSelectbox is now working as it was intended
* Core: it is now possible to tell the code not to add a timestamp on the url for CSS/JS
* Bugfix: changing the working language was redirecting to dashboard instead of the module, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/91
* Bugfix: editing a tag was calling an non existing method
* Content blocks: make sure you add an extra column "extra_id"
* Bugfix: Blog was using the revision-id instead of the id for retrieving tags
* Core: Facebook should be add in the footer instead of the header because Facebook sucks
* Core: improvment for pagination (should fix http://forkcms.lighthouseapp.com/projects/61890/tickets/88)
* Blog: it is now possible to remove all spam at once
* Pages: extra validation, so home can't have any blocks
* Pages: improvement for changing extra's, as requested on http://forkcms.lighthouseapp.com/projects/61890/tickets/77
* Bugfix: mailmotor was reporting empty groups when adding a newsletter, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/111
* Bugfix: minifying the CSS files should replace path to images, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/108
* Core: dashboard can now be customized by the user
* Tools: added a script to generate statistics for the codebase
* Core: isCached now always returns false when SPOON_DEBUG is true
* FormBuilder: added the formbuilder module.
* Mailmotor: now works with CampaignMonitor API v3
* Mailmotor: reworked settings; You can now unlink accounts and choose an existing client to link with.
* Mailmotor: thanks to the reworked import functionality in the CM API v3, the address-import should go a lot faster.
* Mailmotor: you can now pick your own default groups after importing data of an existing client.
* Core: Integrated Facebook in the frontend, when an Facebook-app is configured, a facebook-instance will be available in the reference (Spoon::getObjectReference('facebook')). When the user has granted the correct permission you will be able to communicate with Facebook as that user.
* Bugfix: changing a page template to a template with more blocks caused an exception.
* Pages: use the new Triton theme when installing a new Fork with example data.
* Pages: hidden pages don't have the view-button anymore, as requested on http://forkcms.lighthouseapp.com/projects/61890/tickets/123 
* Bugfix: Metanavigation subpages not shown in backend, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/129
* Dashboard: Fixed issue with dashboard that wasn't scalling anymore, as reported on http://forkcms.lighthouseapp.com/projects/61890/tickets/130
* When FB-admin-ids are given, the facebook-statistics-tag is added on all pages

2.0.2 (2010-11-24)
--
* Added .gitignore files again
* Upgraded jQuery and jQuery UI to latest version
* Upgrades Spoon to reflect the latest changes on their repo (extra methods in SpoonLocale)
* Core: added a modifier to format a string as currency (frontend)
* Core: added a modifier to format a string as a float (backend)
* Blog: when the rss_image.png exists in your theme, it will automatically be used in your rss feed.
	* moved the code for default RSS-image into FrontendRSS
* Pages: added sorting for extras in dropdowns
* Bugfix: extra's weren't populated when the template was changed
* Bugfix: URL was changed when moved if the page was an direct subaction as reported in http://forkcms.lighthouseapp.com/projects/61890/tickets/29-url-gets-changed-when-dragging-a-page-with-isaction-checked
* Bugfix: contactmodule has no backend, so no button should appear in the pages-module, as reported on http://forkcms.lighthouseapp.com/projects/61890-fork-cms/tickets/34-edit-module-contact-no-config-file-found#ticket-34-3
* Core: password strength-meter should report passwords with less then 4 charachters as weak, as reported on http://forkcms.lighthouseapp.com/projects/61890-fork-cms/tickets/33-installer-step5-password-weakness-indicator#ticket-33-3
* Core: added a script that enables us to restore the directory/file-structure like Fork wasn't installed before
* Tags: added a tagcloud-widget
* Core: added an extra modifier to grab page related info (getpageinfo)
* Bugfix: mass checkbox and mass dropdown behaviour now function as intended
* Bugfix: z-index of modal and resize-handle, as reported in http://forkcms.lighthouseapp.com/projects/61890-fork-cms/tickets/37-design-ui-bug-mailmotor#ticket-37-4
* Mailmotor: corrected some labels
* Mailmotor: added a warning if the module isn't linked (so preview won't trigger a 404)
* Bugfix: FrontendRSS now handles an encoded RSS title as intended
* Core: added some labels
* Core: added an new modifier "formatfloat"
* Contact: added author in the subject of the mails, so spam can be detected without checking the email, and Mail.app won't mess up threads
* Tags: fixed some todo's, fixed some stupid code, wrote markup that can be used in a real life project for the default template
* Locale: Implemented some remarks on Locale-module, see http://www.fork-cms.com/blog/detail/the-translations-module
* Styled analyze function of locale module
* Added docs page on installing
* Core: added a modifier to strip the tags from a string (frontend)
* Bugfix: FrontendRSS, special chars should de decoded (thx to Unrated)
* Blog: added a method to get related
* Bugfix: fixed the config of the file* and imagemanager so they can handle symlinks. (and deployment)
* TinyMCE nows get a TinyActive class when active. Fixed Fork tinyMCE skin bugs including wide scrollbar. (always wrap a tinyMCE in `<div class="options">` or `<div class="optionsRTE">`)

2.0.1 (2010-11-03)
--
* added correct .gitignore-files and ignored .git
* fixed some stuff so app is ready for deployment with Capistrano
* added a script to minify stuff from backend (and put in correct folder)
* core: files with extension jpeg are allowed from now on in TinyMCE imagemanager.
* core: installer required javascript to be enabled, so added a check.
* core: installer will clear previous cached data
* core: database-port is now configurable
* core: minor improvements for user-interface.
* core: improved BackendMailer
* core: fixed some labels
* core: when a template used by the mailer exist in the theme it will overule the default
* core: Better styling for drag/drop tables + addded success message after reorder
* core: upgraded CSSToInlineStyles to the latest version
* core: added a method to build a backend URL from the frontend
* blog: fixed installer (comments, rights, ..)
* blog: added a feed on each article with the comments for that article
* blog: added a feed with all comments (on all articles)
* blog: added notification on new comments (settings in backend)
* pages: Made it possible to move stuff from tree into an empty meta-navigation
* mailmotor: preview is now sent with BackendMailer.
* mailmotor: utf8 instead of latin1.
* mailmotor: synced TinyMCE "look and feel" from core
* bugfix: tinyMCE stripped the embed-tag
* bugfix: comment_count on blogarticles ignored the archived/draft status
* bugfix: spam comments couldn't be removed.
* bugfix: generating an URL for a block didn't passed the language in the recursive part.
* bugfix: correct detection of sitemap-page
* bugfix: fixed some calls to BackendPagesModel::buildCache() (language should be passed)
* bugfix: deleting a blog post resulted in an error (thx to Frederik Heyninck)
* bugfix: pages disappear when moving in seperate pages
* bugfix: when deleting a blog-category blogpost were not moved into the default category
* bugfix: CURLOPT_xxx options should be integer/constants instead of strings
* bugfix: limited index length for table modules_settings to overcome SQL error 'Specified key was too long; max key length is 1000 bytes'
* bugfix: datepicker days of week are now correct
* bugfix: fixed UTF-8 issue in contact-module, remember we're using UTF-8, so mails should have teh correct meta-tag
* bugfix: fixed issue with addURLParameters-method, which fucked up URLs with a hash in them.
* bugfix: fixed comment-count on overview.
* bugfix: when a module was linked, and the block was changed, you couldn't select module again. (thx to Frederik Heyninck)

2.0.0 (2010-10-11)
-----
None
