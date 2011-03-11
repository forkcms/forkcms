<?php require_once '_head.php' ?>
<body>
	<div id="container">

		<?php require_once '_header.php' ?>
		<?php require_once '_toc.php' ?>

		<h2 id="templates">Templates</h2>

		<div class="cols id1">
			<div class="col col-8 content">

				<ol>
					<li><a href="templates.php#introduction">Introduction</a></li>
					<li><a href="templates.php#overrides">Overrides</a></li>
					<li><a href="templates.php#thehead">Default <code>&lt;head&gt;</code> contents explained</a></li>
					<li><a href="templates.php#templateModifiers">Template modifiers</a></li>
					<li><a href="templates.php#constants">Available constants</a></li>
				</ol>

				<h3 id="introduction">Introduction</h3>

				<p>A template consists of several bits of information all pieced together: a page title, content (in the form of blocks), navigation menus, meta tags in the &lt;head&gt; section, static HTML, javascript includes, etc. This section explains how to display that information using Fork's template language.</p>

				<h3 id="templatesTitle">Page title</h3>

				<p>To display the current page's title in templates, use <code>{$page.title}</code>.</p>

				<h4>Hide the page title</h4>

				<p>Sometimes you don't want the current page title to show, but it shows up because a module has it's own content titles. If you don't want to show the titles on module-action pages, use the following code:</p>

				<pre class="brush: xml;">
					{option:!hideContentTitle}&lt;h2 class=&quot;pageTitle&quot;&gt;{$page[&#x27;title&#x27;]}&lt;/h2&gt;{/option:!hideContentTitle}
				</pre>

				<h3 id="templatesNavigation">Navigation menus</h3>

				<p>To get the main navigation from level 1 to 2, use the following code:</p>

				<pre class="brush: xml;">
					{$var|getnavigation:'page':0:1}
				</pre>

				<p><span class="markedTodo">@todo extend this section with the other navigations</span></p>

				<h3 id="overrides">Using overrides</h3>

				<p>Consider the directory structure of the Fork frontend (Note: simplified for this example):</p>

				<pre class="brush: xml;">
					frontend
					|-- core
					|-- modules
					|   |-- blog
					|   |-- contact
					|   |-- content_blocks
					|   |-- pages
					|   |-- ...
					`-- themes
					    `-- scratch
					        `-- core
					        `-- modules
				</pre>

				<p>Every module contains templates: e.g. the contact form template lives here in index.tpl:</p>

				<pre class="brush: xml;">
				contact
				|-- actions
				|   `-- index.php
				|-- config.php
				`-- layout
				    `-- templates
				        |-- index.tpl
				        `-- mails
				            `-- contact.tpl
				</pre>

				<p>If you want to include a contact form on your website, you can choose to do nothing and use the default template; <strong>or</strong> you can override the default template with your own. Maybe you need to throw in an extra <code>&lt;div&gt;</code> or two for styling purposes. Maybe you just don't like the defaults.</p>

				<p>To override the defaults, simply include your version of index.tpl in your theme:</p>

				<pre class="brush: xml;">
					frontend
					|-- core
					|-- modules
					|   |-- blog
					|   |-- contact
					|   |-- content_blocks
					|   |-- pages
					|   |-- ...
					`-- themes
					    `-- scratch
					        `-- core
					        `-- modules
					            `-- contact
					                `-- templates
					                    `-- index.tpl
				</pre>

				<p>When a page is requested, the template engine kicks in. It will first look for the the a file called index.tpl in your own theme. If it can't find the template, it will fallback to the defaults.</p>

				<h4>Why overrides?</h4>

				<p>Most websites have the exact same contact form; or the blog archives look the same. If you don't want to spend development time on this, simply don't, and Fork will load the default templates.</p>

				<h3 id="thehead">Default &lt;head&gt; contents</h3>

				<p>The head template contains everything in the <code>&lt;head&gt;</code> section of the website. This is a partial template: if you want, you could have the contens of _head.tpl directly in the full template (default.tpl). It's preffered to the contents of <code>&lt;head&gt;</code> in a separate template to avoid code repetition. DRY!</p>
				<h4>Doctype and &lt;html&gt;</h4>

				<pre class="brush: xml;">
					&lt;!DOCTYPE html PUBLIC &quot;-//W3C//DTD XHTML 1.0 Strict//EN&quot;
					&quot;http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd&quot;&gt;
					&lt;html xmlns=&quot;http://www.w3.org/1999/xhtml&quot; lang=&quot;{$LANGUAGE}&quot;&gt;
				</pre>

				<p>Pretty standard: note the use of the <code>{$LANGUAGE}</code> constant. <code>{$LANGUAGE}</code> prints nl, fr, en, de, es and so on depending on which language of the site you are currently visiting.</p>

				<h4>Encoding</h4>

				<pre class="brush: xml;">
				&lt;head&gt;
					&lt;meta http-equiv=&quot;content-type&quot; content=&quot;text/html; charset=utf-8&quot; /&gt;
				</pre>

				<p>Fork uses UTF-8 for character encoding so you can enter Swahili or snowmen <span style="font-size:24px;">☃</span> without any problems.</p>

				<h4>Title</h4>

				<pre class="brush: xml;">
					&lt;title&gt;{$pageTitle}&lt;/title&gt;
				</pre>

				<p>Prints out the title of the current page. You can <a href="modules.php#pageInformationTitle">change the page title</a> of every page in the backend.</p>

				<h4>Favicon</h4>

				<pre class="brush: xml;">
					&lt;link rel=&quot;shortcut icon&quot; href=&quot;/favicon.ico&quot; /&gt;
				</pre>

				<p>Links to the favicon. The default favicon is the Fork logo. It's located in <code>&lt;path-to-site&gt;/default_www/favicon.ico</code>.</p>

				<h4>X-UA-Compatible</h4>

				<pre class="brush: xml;">
					&lt;meta http-equiv=&quot;X-UA-Compatible&quot; content=&quot;IE=EmulateIE7&quot; /&gt;
					&lt;meta http-equiv=&quot;X-UA-Compatible&quot; content=&quot;chrome=1&quot;&gt;
				</pre>
				<p>These meta tags ensure better browser compatibility: the first one makes Internet Explorer 8 behave as IE7 (so you only have to test against IE7 and IE6); the second one enables <a href="http://code.google.com/chrome/chromeframe/">Google Chrome Frame</a>.</p>

				<h4>Debug mode</h4>

				<pre class="brush: xml;">
					{option:debug}&lt;meta name=&quot;robots&quot; content=&quot;noindex, nofollow&quot; /&gt;{/option:debug}
				</pre>

				<p>Several things going on here: <code>debug</code> is an option that will only display when your site is in debug mode (<a href="extra.php#debugmode">When is your site in debug mode?</a>). The robots tag prevents GoogleBot (and other robots) from indexing your website when it's not done yet.</p>

				<h4>Meta</h4>

				<pre class="brush: xml;">
					&lt;meta name=&quot;generator&quot; content=&quot;Fork CMS&quot; /&gt;
					&lt;meta name=&quot;description&quot; content=&quot;{$metaDescription}&quot; /&gt;
					&lt;meta name=&quot;keywords&quot; content=&quot;{$metaKeywords}&quot; /&gt;
					{$metaCustom}
				</pre>

				<p><code>{$metaDescription}</code>, <code>{$metaKeywords}</code> and <code>{$metaCustom}</code> are fields available in the backend for every page. See <a href="modules.php#pageInformationTitleMeta">Page information - Meta</a>.</p>

				<h4>CSS</h4>

				<pre class="brush: xml;">
				{* Stylesheets *}
				{iteration:cssFiles}
					{option:!cssFiles.condition}
						&lt;link rel=&quot;stylesheet&quot; type=&quot;text/css&quot; media=&quot;{$cssFiles.media}&quot; href=&quot;{$cssFiles.file}&quot; /&gt;
					{/option:!cssFiles.condition}
					{option:cssFiles.condition}
						&lt;!--[if {$cssFiles.condition}]&gt;&lt;link rel=&quot;stylesheet&quot; type=&quot;text/css&quot; media=&quot;{$cssFiles.media}&quot; href=&quot;{$cssFiles.file}&quot; /&gt;&lt;![endif]--&gt;
					{/option:cssFiles.condition}
				{/iteration:cssFiles}
				</pre>

				<p>This code grabs your CSS files: be sure they are named...<span class="markedTodo">@todo explain how this works (and why (caching))</span></p>

				<h4>Javascript</h4>

				<pre class="brush: xml;">
				{* Javascript *}
				{iteration:javascriptFiles}
					&lt;script type=&quot;text/javascript&quot; src=&quot;{$javascriptFiles.file}&quot;&gt;&lt;/script&gt;
				{/iteration:javascriptFiles}
				</pre>

				<p>This code grabs your JS files: be sure they are named...<span class="markedTodo">@todo explain how this works</span></p>

				<p><span class="markedTodo">@todo explain how to add a non-default script</span></p>

				<pre class="brush: xml;">
				{* Site wide HTML *}
				{$siteHTMLHeader}
				</pre>

				<pre class="brush: xml;">
				&lt;/head&gt;
				</pre>

				<p>And so there we have it: the full explanation of the contents of the <code>&lt;head&gt;</code> tag.</p>

				<h3 id="templateModifiers">Template modifiers</h3>

				<ul>
					<li>createhtmllinks</li>
					<li>date</li>
					<li>htmlentities</li>
					<li>lowercase</li>
					<li>ltrim</li>
					<li>nl2br</li>
					<li>repeat</li>
					<li>rtrim</li>
					<li>shuffle</li>
					<li>sprintf</li>
					<li>stripslashes</li>
					<li>substring</li>
					<li>trim</li>
					<li>ucfirst</li>
					<li>ucwords</li>
					<li>uppercase</li>
				</ul>

				<p>Documentation: search for &#8220;List of default modifiers&#8221; <a href="http://tutorials.spoon-library.be/details/templates-part-3">here</a></p>
				<p><span class="markedTodo">@todo check this</span></p>

				<table class="datagrid" cellspacing="0">
					<tbody>
						<tr>
							<th>Modifier</th>
							<th>Description</th>
							<th>Syntax</th>
							<th>&nbsp;</th>
						</tr>
						<tr>
								<td>cleanupplaintext</td>
								<td>Formats plain text as <span class="caps">HTML</span>, links will be detected, paragraphs will be inserted</td>
								<td><code>{$var|cleanupplainText}</code></td>
								<td>&nbsp;</td>
						</tr>
						<tr>
								<td>getnavigation</td>
								<td>Get the navigation html</td>
						                <td><code>{$var|getnavigation[:'&lt;type&gt;'][:&lt;parentId&gt;][:&lt;depth&gt;][:'&lt;excludeIds-splitted-by-dash&gt;']}</code></td>
								<td>available types: page, meta (if the user setting is enabled), footer</td>
						</tr>
						<tr>
								<td>getsubnavigation</td>
								<td>Get the navigation html</td>
						                <td><code>{$var|getsubnavigation[:'&lt;type&gt;'][:&lt;pageId&gt;][:&lt;startdepth&gt;][:&lt;endDepth&gt;][:'&lt;excludeIds-splitted-by-dash&gt;']}</code></td>
								<td>available types: page, meta (if the user setting is enabled)</td>
						</tr>
						<tr>
								<td>timeago</td>
								<td>Formats a timestamp as a string that indicates the time ago</td>
								<td><code>{$var|timeago}</code></td>
								<td>&nbsp;</td>
						</tr>
						<tr>
								<td>truncate</td>
								<td>Truncate a string</td>
								<td><code>{$var|truncate:&lt;length&gt;[:useHellip]}</code></td>
								<td>useHellip: possible values: true, false</td>

						</tr>
						<tr>
								<td>usersetting</td>
								<td>Get the value for a backend user-setting</td>
								<td><code>{$var|userSetting:'&lt;setting&gt;'[:&lt;userId&gt;]}</code></td>
								<td>&nbsp;</td>
						</tr>
					</tbody>
				</table>

				<div class="col-6">
					<h3 id="constants">Available constants</h3>

					<ul>
						<li>
							<strong>FRONTEND_CACHE_PATH</strong>: The path to the frontend cache-folder, eg: /home/fork/default_www/frontend/cache
						</li>
						<li>
							<strong>FRONTEND_CACHE_URL</strong>: The url to the frontend cache-folder, eg: /frontend/cache
						</li>
						<li>
							<strong>FRONTEND_CORE_PATH</strong>: The path to the frontend core-folder, eg: /home/fork/default_www/frontend/core
						</li>
						<li>
							<strong>FRONTEND_CORE_URL</strong>: The url to the frontend core-folder, eg: /frontend/core
						</li>
						<li>
							<strong>FRONTEND_FILES_PATH</strong>: The path to the frontend files-folder, in this folder you can store files that are uploaded by a user, eg: /home/fork/default_www/frontend/files
						</li>
						<li>
							<strong>FRONTEND_FILES_URL</strong>: Absolute url to the frontend files-folder, eg: /frontend/files
						</li>
						<li>
							<strong>FRONTEND_MODULES_PATH</strong>: Path to the frontend modules folder, eg: /home/fork/default_www/frontend/modules
						</li>
						<li>
							<strong>FRONTEND_PATH</strong>: Path to the frontend, eg: /home/fork/default_www/frontend
						</li>
						<li>
							<strong>LANGUAGE</strong>: The current language the user is working on, eg: nl
						</li>
						<li>
							<strong>PATH_LIBRARY</strong>: Path to the library, eg: /home/fork/library
						</li>
						<li>
							<strong>PATH_WWW</strong>: Path to the folder that has to be used as document-root, eg: /home/fork/default_www
						</li>
						<li>
							<strong>SITE_DOMAIN</strong>: The primary domain for the site, eg: fork.local
						</li>
						<li>
							<strong>SITE_DEFAULT_LANGUAGE</strong>: The default language for the site, eg: nl
						</li>
						<li>
							<strong>SITE_MULTILANGUAGE</strong>: Is the site available in multiple languages?, eg: true
						</li>
						<li>
							<strong>SITE_DEFAULT_TITLE</strong>: The default title for the site, can be used as fallback. eg: Fork NG
						</li>
						<li>
							<strong>SITE_TITLE</strong> The title for the site, configured by the user, eg: Fork NG
							<ul>
								<li>Usage in templates: {$SITE_TITLE}
								</li>
								<li>Usage in frontend PHP: FrontendModel::getModuleSetting(‘core’, ‘site_title_’. FRONTEND_LANGUAGE, SITE_DEFAULT_TITLE);
								</li>
							</ul>
						</li>
						<li>
							<strong>SITE_URL</strong>: The full URL for the site, eg: http://fork-cms.be
						</li>
						<li>
							<strong>THEME</strong>: The theme that is currently in use, eg: default
							<ul>
								<li>Usage in templates: {$THEME}
								</li>
								<li>Usage in frontend PHP: FrontendModel::getModuleSetting(‘core’, ‘theme’, ‘default’);
								</li>
							</ul>
						</li>
						<li>
							<strong>THEME_PATH</strong>: The path to the theme that is currently in use, eg: /home/fork/default_www/frontend/themes/default
							<ul>
								<li>Usage in templates: {$THEME_PATH}
								</li>
								<li>Usage in frontend PHP: FRONTEND_PATH . ‘/themes/’. FrontendModel::getModuleSetting(‘core’, ‘theme’, ‘default’);
								</li>
							</ul>
						</li>
						<li>
							<strong>THEME_URL</strong>: The url to the theme that is currently in use, eg: /frontend/themes/default
							<ul>
								<li>Usage in templates: {$THEME_URL}
								</li>
								<li>Usage in frontend PHP: ‘/frontend/themes/’. FrontendModel::getModuleSetting(‘core’, ‘theme’, ‘default’);
								</li>
							</ul>
						</li>
					</ul>
				</div>

			</div>
		</div>

		<div class="hr"><hr /></div>

	</div>

	<script type="text/javascript">
		SyntaxHighlighter.config.clipboardSwf = 'js/syntax/scripts/clipboard.swf';
		SyntaxHighlighter.defaults['gutter'] = false;
		SyntaxHighlighter.all();
	</script>

</body>
</html>