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
				{iteration:positionTop}
					{$positionTop.blockContent}
				{/iteration:positionTop}

				{* Breadcrumb *}
				<aside id="breadcrumb">
					<h4>{$lblBreadcrumb|ucfirst}</h4>
					{include:core/layout/templates/breadcrumb.tpl}
				</aside>

			</div>
		</header>

		<div id="main">
			<div class="container">
				<div class="col col-2">

					{* Subnavigation *}
					<nav class="sideNavigation">
						<h4>{$lblSubnavigation|ucfirst}</h4>
						{$var|getsubnavigation:'page':{$page.id}:2}
					</nav>

					{* Left position *}
					{iteration:positionLeft}
						{option:positionLeft.blockIsHTML}
							<section class="mod">
								<div class="inner">
									<div class="bd content">
										{$positionLeft.blockContent}
									</div>
								</div>
							</section>
						{/option:positionLeft.blockIsHTML}
						{option:!positionLeft.blockIsHTML}
							{$positionLeft.blockContent}
						{/option:!positionLeft.blockIsHTML}
					{/iteration:positionLeft}

					&nbsp;

				</div>
				<div class="col col-4">

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

					&nbsp;

				</div>
				<div class="col col-2 lastCol">

					{* Right position *}
					{iteration:positionRight}
						{option:positionRight.blockIsHTML}
							<section class="mod">
								<div class="inner">
									<div class="bd content">
										{$positionRight.blockContent}
									</div>
								</div>
							</section>
						{/option:positionRight.blockIsHTML}
						{option:!positionRight.blockIsHTML}
							{$positionRight.blockContent}
						{/option:!positionRight.blockIsHTML}
					{/iteration:positionRight}

					&nbsp;

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
		<footer id="footer">
			<div class="container">

				{* Footer logo *}
				<div id="footerLogo">
					<p><a href="#">{$siteTitle}</a></p>
				</div>

				{* Footer navigation *}
				<nav id="footerNavigation">
					<h4>{$lblFooterNavigation}</h4>
					{$var|getnavigation:'page':0:1}
				</nav>

				{* Footer meta navigation *}
				<nav id="metaNavigation">
					<h4>{$lblFooterMetaNavigation}</h4>
					<ul>
						{iteration:footerLinks}
							<li{option:footerLinks.selected} class="selected"{/option:footerLinks.selected}>
								<a href="{$footerLinks.url}" title="{$footerLinks.title}"{option:footerLinks.rel} rel="{$footerLinks.rel}"{/option:footerLinks.rel}>
									{$footerLinks.navigation_title}
								</a>
							</li>
						{/iteration:footerLinks}
						<li><a href="http://www.fork-cms.com" title="Fork CMS">Fork CMS</a></li>
					</ul>
				</nav>

				{* Footer position *}
				{iteration:positionFooter}
					<aside id="footerSearch">
						{$positionFooter.blockContent}
					</aside>
				{/iteration:positionFooter}

			</div>
		</footer>
	</div>

	{* Site wide HTML *}
	{$siteHTMLFooter}

	{* General Javascript *}
	{iteration:jsFiles}
		<script src="{$jsFiles.file}"></script>
	{/iteration:jsFiles}

	{* Theme specific Javascript *}
	<script src="{$THEME_URL}/core/js/goldengate.js"></script>

</body>
</html>