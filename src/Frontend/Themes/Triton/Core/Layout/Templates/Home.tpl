{include:Core/Layout/Templates/Head.tpl}

<body class="{$LANGUAGE}" itemscope itemtype="http://schema.org/WebPage">
	{include:Core/Layout/Templates/Cookies.tpl}

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
					{include:Core/Layout/Templates/Languages.tpl}
				</nav>

				{* Top position *}
				{iteration:positionTop}
					{$positionTop.blockContent}
				{/iteration:positionTop}

				{* Breadcrumb *}
				<div id="breadcrumb">
					<h4>{$lblBreadcrumb|ucfirst}</h4>
					{include:Core/Layout/Templates/Breadcrumb.tpl}
				</div>

				{* Advertisement position *}
				{iteration:positionAdvertisement}
					{option:positionAdvertisement.blockIsEditor}
						<div id="headerAd">
							{$positionAdvertisement.blockContent}
						</div>
					{/option:positionAdvertisement.blockIsEditor}
					{option:!positionAdvertisement.blockIsEditor}
						{$positionAdvertisement.blockContent}
					{/option:!positionAdvertisement.blockIsEditor}
				{/iteration:positionAdvertisement}

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
					{iteration:positionMain}
						{option:positionMain.blockIsEditor}
							<section class="mod">
								<div class="inner">
									<div class="bd content">
										{$positionMain.blockContent}
									</div>
								</div>
							</section>
						{/option:positionMain.blockIsEditor}
						{option:!positionMain.blockIsEditor}
							{$positionMain.blockContent}
						{/option:!positionMain.blockIsEditor}
					{/iteration:positionMain}

				</div>

				{* Left column *}
				<div class="col col-6">

				{* Left position *}
				{iteration:positionLeft}
					{option:positionLeft.blockIsEditor}
						<section class="mod">
							<div class="inner">
								<div class="bd content">
									{$positionLeft.blockContent}
								</div>
							</div>
						</section>
					{/option:positionLeft.blockIsEditor}
					{option:!positionLeft.blockIsEditor}
						{$positionLeft.blockContent}
					{/option:!positionLeft.blockIsEditor}
				{/iteration:positionLeft}

				</div>

				{* Right column *}
				<div class="col col-6 lastCol">

				{* Right position *}
				{iteration:positionRight}
					{option:positionRight.blockIsEditor}
						<section class="mod">
							<div class="inner">
								<div class="bd content">
									{$positionRight.blockContent}
								</div>
							</div>
						</section>
					{/option:positionRight.blockIsEditor}
					{option:!positionRight.blockIsEditor}
						{$positionRight.blockContent}
					{/option:!positionRight.blockIsEditor}
				{/iteration:positionRight}

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
		{include:Core/Layout/Templates/Footer.tpl}
	</div>

	{* General Javascript *}
	{iteration:jsFiles}
		<script src="{$jsFiles.file}"></script>
	{/iteration:jsFiles}

	{* Theme specific Javascript *}
	<script src="{$THEME_URL}/Core/Js/triton.js"></script>

	{* Site wide HTML *}
	{$siteHTMLFooter}
</body>
</html>
