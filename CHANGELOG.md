2.4.1 (XXXX-XX-XX)
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
