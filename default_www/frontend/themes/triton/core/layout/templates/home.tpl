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
					{option:top.blockIsHTML}
						<div id="headerSearch">
							{$top.blockContent}
						</div>
					{/option:top.blockIsHTML}
					{option:!top.blockIsHTML}
						{$top.blockContent}
					{/option:!top.blockIsHTML}
				{/iteration:top}

				{* Breadcrumb *}
				<div id="breadcrumb">
					<h4>{$lblBreadcrumb|ucfirst}</h4>
					{include:core/layout/templates/breadcrumb.tpl}
				</div>

				{* Advertisement position *}
				{iteration:advertisement}
					{option:advertisement.blockIsHTML}
						<div id="headerAd">
							{$advertisement.blockContent}
						</div>
					{/option:advertisement.blockIsHTML}
					{option:!advertisement.blockIsHTML}
						{$advertisement.blockContent}
					{/option:!advertisement.blockIsHTML}
				{/iteration:advertisement}

			</div>

		</header>
		<div id="main">
			<div class="container">

				{* Main column *}
				<div class="col col-12 lastCol">

					{* Page title *}
					{option:!hideContentTitle}
						<header class="mainTitle">
							<h1>{$page.title}</h1>
						</header>
					{/option:!hideContentTitle}

					{* Main position *}
					{iteration:main}
						{option:main.blockIsHTML}
							<section class="mod">
								<div class="inner">
									<div class="bd content">
										{$main.blockContent}
									</div>
								</div>
							</section>
						{/option:main.blockIsHTML}
						{option:!main.blockIsHTML}
							{$main.blockContent}
						{/option:!main.blockIsHTML}
					{/iteration:main}

				</div>

				{* Left column *}
				<div class="col col-6">

				{* Left position *}
				{iteration:left}
					{option:left.blockIsHTML}
						<section class="mod">
							<div class="inner">
								<div class="bd content">
									{$left.blockContent}
								</div>
							</div>
						</section>
					{/option:left.blockIsHTML}
					{option:!left.blockIsHTML}
						{$left.blockContent}
					{/option:!left.blockIsHTML}
				{/iteration:left}

				</div>

				{* Right column *}
				<div class="col col-6 lastCol">

				{* Right position *}
				{iteration:right}
					{option:right.blockIsHTML}
						<section class="mod">
							<div class="inner">
								<div class="bd content">
									{$right.blockContent}
								</div>
							</div>
						</section>
					{/option:right.blockIsHTML}
					{option:!right.blockIsHTML}
						{$right.blockContent}
					{/option:!right.blockIsHTML}
				{/iteration:right}

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