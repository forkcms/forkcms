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

			{* Main position *}
			{iteration:main}
				{option:main.isHTML}
					<section class="mod">
						<div class="inner">
							<div class="bd content">
								{$main.content}
							</div>
						</div>
					</section>
				{/option:main.isHTML}
				{option:!main.isHTML}
					{$main.content}
				{/option:!main.isHTML}
			{/iteration:main}
		</section>

		<footer>
			{include:core/layout/templates/footer.tpl}
		</footer>
	</div>

	{* General Javascript *}
	{iteration:javascriptFiles}
		<script src="{$javascriptFiles.file}"></script>
	{/iteration:javascriptFiles}

	{* Site wide HTML *}
	{$siteHTMLFooter}
</body>
</html>