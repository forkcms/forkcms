{include:core/layout/templates/head.tpl}

<body class="{$LANGUAGE}" itemscope itemtype="http://schema.org/WebPage">
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

			{* Main position *}
			{iteration:positionMain}
				{option:positionMain.blockIsHTML}
					<section class="mod">
						<div class="inner">
							<div class="bd content">
								{$positionMain.blockContent}
							</div>
						</div>
					</section>
				{/option:positionMain.blockIsHTML}
				{option:!positionMain.blockIsHTML}
					{$positionMain.blockContent}
				{/option:!positionMain.blockIsHTML}
			{/iteration:positionMain}
		</section>

		<footer>
			{include:core/layout/templates/footer.tpl}
		</footer>
	</div>

	{* General Javascript *}
	{iteration:jsFiles}
		<script src="{$jsFiles.file}"></script>
	{/iteration:jsFiles}

	{* Site wide HTML *}
	{$siteHTMLFooter}
</body>
</html>