<?php require_once '_head.php' ?>
<body>
	<div id="container">

		<?php require_once '_header.php' ?>
		<?php require_once '_toc.php' ?>

		<h2 id="themes">Themes</h2>

		<div class="cols id1">
			<div class="col col-6 content">

				<ol>
					<li><a href="themes.php#howitworks">How themes work</a></li>
					<li><a href="themes.php#structure">Theme directory structure</a></li>
					<li><a href="themes.php#layoutCode">Layout code</a></li>
				</ol>

				<h3 id="howitworks">How themes work</h3>

				<p>In order to get the most out of Fork CMS, it's very important to understand theming.</p>

				<p>For every new project, you should create a new theme. It's always easier to modify an existing theme to your needs than to build a new theme. Fork CMS provides a default theme to start from called <em>Scratch</em>. This section contains general information on how themes work.</p>

				<h3 id="structure">Theme directory structure</h3>

				<p>The themes folder is located in <code>default_www/frontend/themes</code>. After a fresh install, this folder will contain just one theme: <em>scratch</em>.</p>

				<pre class="brush: xml;">
				scratch
				`-- core
				    |-- css
				    |   |-- ie6.css
				    |   |-- ie7.css
				    |   |-- print.css
				    |   `-- screen.css
				    |-- images
				    `-- templates
				        |-- default.tpl
				        |-- home.tpl
				        `-- twocolumns.tpl
				</pre>

				<p>This theme contains a folder for your CSS and a folder for images. It also contains 3 templates: default, home and twocolumns.</p>
				
				<h3 id="layoutCode">Layout code</h3>
				<p>How does the layout code work?</p>
				<ul>
					<li>If you want to add a row, use []</li>
					<li>If you want to display a block, use the block number</li>
					<li>If you want to add an empty cell (e.g. a non-editable area) use /</li>
					<li>If you want a block to be wider or bigger in it's graphical representation, repeat the number multiple times (can be done on multiple rows too: but the shape should form a rectangle)</li>
				</ul>

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