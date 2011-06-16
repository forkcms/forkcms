{include:core/layout/templates/head.tpl}

<body class="{$LANGUAGE}">
	<div id="container">
		<header id="header">
			{* Logo *}
			<h2>
				<a href="/">{$siteTitle}</a>
			</h2>

			{* Language *}
			<div>
				{include:core/layout/templates/languages.tpl}
			</div>
		</header>

		{* Navigation *}
		<nav>
			{$var|getnavigation:'page':0:1}
		</nav>

		<section>
			{* Breadcrumb *}
			{include:core/layout/templates/breadcrumb.tpl}

			{* Page title *}
			{option:!hideContentTitle}
				<header class="mainTitle">
					<h1>{$page.title}</h1>
				</header>
			{/option:!hideContentTitle}

			{* Block 1 (default: Editor) *}
			{option:block1IsHTML}
				{option:block1}
					<section class="mod">
						<div class="inner">
							<div class="bd content">
								{$block1}
							</div>
						</div>
					</section>
				{/option:block1}
			{/option:block1IsHTML}
			{option:!block1IsHTML}
				{$block1}
			{/option:!block1IsHTML}
		</section>

		<footer>
			{include:core/layout/templates/footer.tpl}
		</footer>
	</div>

	{* Site wide HTML *}
	{$siteHTMLFooter}

	{* General Javascript *}
	{iteration:javascriptFiles}
		<script src="{$javascriptFiles.file}"></script>
	{/iteration:javascriptFiles}

</body>
</html>