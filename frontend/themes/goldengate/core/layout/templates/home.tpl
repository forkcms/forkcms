{include:core/layout/templates/head.tpl}

<body id="home" class="{$LANGUAGE}">
	<div id="topWrapper">
		<header id="header">
			<div class="container">

				{* Logo *}
				<div id="logo">
					<h1><a href="/">{$siteTitle}</a></h1>
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

				{* Unknown position *}
				{iteration:positionHeader}
					<section id="headerFocus" class="content">
						{$positionHeader.blockContent}
						<div class="guillotineBugFix"></div>
					</section>
				{/iteration:positionHeader}

			</div>
		</header>
		<div id="main">
			<div class="container">

				{* Page title *}
				{option:!hideContentTitle}
					<header class="mainTitle">
						<h2>{$page.title}</h2>
					</header>
				{/option:!hideContentTitle}

				{* Main position *}
				{iteration:positionMain}
					<section class="mod">
						<div class="inner">
							{option:positionMain.blockIsHTML}
								<div class="bd content">
									{$positionMain.blockContent}
								</div>
							{/option:positionMain.blockIsHTML}
							{option:!positionMain.blockIsHTML}
								{$positionMain.blockContent}
							{/option:!positionMain.blockIsHTML}
						</div>
					</section>
				{/iteration:positionMain}

				{option:positionItems}
				<section class="mod">
					<div class="inner">
				{/option:positionItems}

					{* Title position *}
					{iteration:positionTitle}
						{option:positionTitle.blockIsHTML}
							<header class="mainTitle">
								<h3>{$positionTitle.blockContent|striptags}</h3>
							</header>
						{/option:positionTitle.blockIsHTML}
						{option:!positionTitle.blockIsHTML}
							{$positionTitle.blockContent}
						{/option:!positionTitle.blockIsHTML}
					{/iteration:positionTitle}

						<div class="bd">

							{* Items position *}
							{iteration:positionItems}
								<section class="mod col col-4{option:positionItems.last} lastCol{/option:positionItems.last}">
									<div class="inner">
										{option:positionItems.blockIsHTML}
											<div class="bd content">
												{$positionItems.blockContent}
											</div>
										{/option:positionItems.blockIsHTML}
										{option:!positionItems.blockIsHTML}
											{$positionItems.blockContent}
										{/option:!positionItems.blockIsHTML}
									</div>
								</section>
							{/iteration:positionItems}

						</div>

				{option:positionItems}
					</div>
				</section>
				{/option:positionItems}

				{* Wide position *}
				{iteration:positionWide}
					<section class="mod">
						<div class="inner">
							{option:positionWide.blockIsHTML}
								<div class="bd content">
									{$positionWide.blockContent}
								</div>
							{/option:positionWide.blockIsHTML}
							{option:!positionWide.blockIsHTML}
								{$positionWide.blockContent}
							{/option:!positionWide.blockIsHTML}
						</div>
					</section>
				{/iteration:positionWide}

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