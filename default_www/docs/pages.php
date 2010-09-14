<?php require_once '_head.php' ?>
<body>
	<div id="container">

		<?php require_once '_header.php' ?>
		<?php require_once '_toc.php' ?>

		<h2 id="pages">Pages</h2>

		<div class="cols id1">
			<div class="col col-6 content">

				<ol>
					<li><a href="#howTheTreeWorks">How the tree works</a></li>
					<li><a href="#pageInformationTitle">Page information - Title</a></li>
					<li><a href="#pageInformationTitleMeta">Page information - Meta</a></li>
					<li><a href="#hidingPages">Hiding pages</a></li>
					<li><a href="#deletingPages">Deleting pages</a></li>
					<li><a href="#versioning">Versioning</a></li>
				</ol>

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

				<h3 id="hidingPages">Hiding pages</h3>

				<div class="figure figureLeft" style="width: 181px;">
					<img src="images/hidden_page.png" width="181" height="86" alt="Hidden Page" />
					<p class="caption">"History" is hidden in this example.</p>
				</div>

				<p>To hide a page, click the page you want to hide in the tree, click "Settings" and then change "Published" to "Hidden".</p>

				<p>When you hide a page it will appear greyed out in the tree. Note that when you hide a page all of it's subpages will be hidden too.</p>

				<h3 id="deletingPages">Deleting pages</h3>

				<div class="figure figureLeft" style="width: 73px;">
					<img src="images/deletebutton.png" width="73" height="33" alt="Delete button" />
					<p class="caption">Look for this.</p>
				</div>

				<p>To delete a page, click the page in the tree, and click the delete button. Deleting a page will delete it's contents and revisions and there is no way to get it back. So be careful ;).</p>

				<p>A page can only be deleted if it has no subpages (children).</p>
				<p>Consider the following tree structure:</p>

				<img src="images/deletion_example.png" width="186" height="154" alt="Deletion Example" />

				<p><em>About us</em> can be deleted because it has no subpages <em>First half</em> and <em>Second half</em> can be deleted too. To delete <em>15th century</em>, you'd have to move or delete <em>First Half</em> and <em>Second Half</em>.</p>
				
				<p>Note that we know this can be tedious when restructuring large websites; we intend to resolve this in the future.</p>

				<h3 id="versioning">Versioning</h3>

				<p>When you edit a page, the tab "versioning" allows you to revert to a previous version of that page. The change will not take place until you save that page again.</p>

				<p>Tip: if you accidentally deleted earlier page content, revert to the older version, copy/paste the content, don't save, and click the page again in the tree. Paste your earlier content and voila: you saved yourself some work.</p>

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

