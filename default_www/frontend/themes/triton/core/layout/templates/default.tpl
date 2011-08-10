{include:core/layout/templates/head.tpl}

<body class="{$LANGUAGE}">
	<div id="topWrapper">
		<header id="header">
			<div class="container">

				{* Logo *}
				<div id="logo">
					<h2><a href="/">{$siteTitle}</a></h2>
				</div>

				{* Skip link *}
				<div id="skip">
					<p><a href="#main">{$lblSkipToContent|ucfirst}</a></p>
				</div>

				{* Navigation *}
				<nav id="headerNavigation">
					<h4>{$lblMainNavigation|ucfirst}</h4>
					{$var|getnavigation:'page':0:1}
				</nav>

				{* Language *}
				<nav id="headerLanguage">
					<h4>{$lblLanguage|ucfirst}</h4>
					{include:core/layout/templates/languages.tpl}
				</nav>

				{* Top position *}
				{iteration:top}
					{option:top.isHTML}
						<div id="headerSearch">
							{$top.content}
						</div>
					{/option:top.isHTML}
					{option:!top.isHTML}
						{$top.content}
					{/option:!top.isHTML}
				{/iteration:top}

				{* Breadcrumb *}
				<div id="breadcrumb">
					<h4>{$lblBreadcrumb|ucfirst}</h4>
					{include:core/layout/templates/breadcrumb.tpl}
				</div>

				{* Advertisement position *}
				{iteration:advertisement}
					{option:advertisement.isHTML}
						<div id="headerAd">
							{$advertisement.content}
						</div>
					{/option:advertisement.isHTML}
					{option:!advertisement.isHTML}
						{$advertisement.content}
					{/option:!advertisement.isHTML}
				{/iteration:advertisement}
			</div>

		</header>
		<div id="main">
			<div class="container">

				{* Left column *}
				<div class="col col-3">

					{* Subnavigation *}
					<nav class="sideNavigation">
						<h4>{$lblSubnavigation|ucfirst}</h4>
						{$var|getsubnavigation:'page':{$page.id}:2}
					</nav>

					{* Left position *}
					{iteration:left}
						{option:left.isHTML}
							<section class="mod">
								<div class="inner">
									<div class="bd content">
										{$left.content}
									</div>
								</div>
							</section>
						{/option:left.isHTML}
						{option:!left.isHTML}
							{$left.content}
						{/option:!left.isHTML}
					{/iteration:left}

				</div>

				{* Main column *}
				<div class="col col-9 lastCol">

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

				</div>
			</div>
		</div>
		<noscript>
			<div class="message notice">
				<h4>{$lblEnableJavascript|ucfirst}</h4>
				<p>{$msgEnableJavascript}</p>
			</div>
		</noscript>
	</div>
	<div id="bottomWrapper">
		{include:core/layout/templates/footer.tpl}
	</div>

	{* General Javascript *}
	{iteration:javascriptFiles}
		<script src="{$javascriptFiles.file}"></script>
	{/iteration:javascriptFiles}

	{* Theme specific Javascript *}
	<script src="{$THEME_URL}/core/js/triton.js"></script>

	{* Site wide HTML *}
	{$siteHTMLFooter}
</body>
</html>