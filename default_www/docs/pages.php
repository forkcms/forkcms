<?php require_once '_head.php' ?>
<body>
	<div id="container">

		<?php require_once '_header.php' ?>
		<?php require_once '_toc.php' ?>

		<h2 id="pages">Pages</h2>

		<div class="cols id1">
			<div class="col col-6 content">

				<p><span class="markedTodo">@todo write about hiding pages; versioning</span></p>

				<h3 id="howTheTreeWorks">How the tree works</h3>

				<p>When you open the pages module, the first thing you'll notice is the tree. The tree is a visual representation of the pages in your website's navigation menu.</p>

				<p>Let's go over the different sections:</p>

				<div class="figure figureLeft" style="width: 261px;">
					<img src="images/tree.png" width="261" height="341" alt="Tree" />
					<p class="caption">The tree.</p>
				</div>

				<h4>Main navigation</h4>

				<p>This is your website's main navigation in tree form. To create <strong>subnavigation</strong> levels, drop one page on another page.</p>

				<p>You can drag and drop the pages to change the <strong>page order</strong>. The change is immediate: reload your website to see it.</p>

				<h4>Meta navigation</h4>

				<p>This is an extra, "spare" navigation for websites that require more than 1 navigation. Commonly used to link to the contact form, or as a meta navigation across a website network. The meta navigation is off by default: you can enable it in settings > modules > pages.</p>

				<h4>Bottom navigation ("footer items")</h4>

				<p>Almost every website has a footer with copyright notices, a link to the disclaimer and privacy page and sometimes some other items.</p>

				<h4>Separate pages</h4>

				<p>Separate pages cannot be reached via one of the navigation menus. They can only be reached by URL. This is useful for temporary pages (like a contest for example).</p>

				<p>Template usage: see <a href="themes.php#templatesNavigation">Using navigations in templates</a></p>

				<h3 id="pageInformationTitle">Page information - Title</h3>

				<div class="figure">
					<img src="images/title.png" alt="Title" />
					<p class="caption">A page title</p>
				</div>

				<p>To change the title of a page: go to Pages, click the page you need in the tree, and change the field labelled "Title".</p>

				<p>Note that the page title can be overrided in the SEO tab: you can enter separate titles for the page <code>&lt;title&gt;</code> and the title in the site's navigation.</p>

				<p>Template usage: see <a href="themes.php#templatesTitle">Using titles in templates</a></p>

				<h3 id="pageInformationTitleMeta">Page information - Meta</h3>

				<p>To change the meta information of a page: go to Pages, click the page you need in the tree, and look at the SEO tab. The fields are pretty self-explanatory for a developer.</p>

				<p>Template usage: see <a href="themes.php#thehead">Default <code>&lt;head&gt;</code> contents explained</a>.</p>

			</div>
		</div>

		<div class="hr"><hr /></div>

	</div>

	<script type="text/javascript">
		SyntaxHighlighter.config.clipboardSwf = 'js/syntax/scripts/clipboard.swf';
		SyntaxHighlighter.all();
	</script>

	</div>
</body>
</html>

