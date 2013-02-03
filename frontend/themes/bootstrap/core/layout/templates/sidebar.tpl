{include:core/layout/templates/head.tpl}

<body class="{$LANGUAGE}" itemscope itemtype="http://schema.org/WebPage">
	<noscript>
		<div class="fullWidthAlert alert">
			<button type="button" class="close" data-dismiss="alert">×</button>
			<strong>{$lblWarning|ucfirst}:</strong> {$msgEnableJavascript}
		</div>
	</noscript>
	<!-- Warning for people that still use IE7 or below -->
	<!--[if lt IE 8 ]>
		<div id="ie" class="fullWidthAlert alert">
			<button type="button" class="close" data-dismiss="alert">×</button>
			<strong>{$lblWarning|ucfirst}:</strong> {$msgOldBrowser}
		</div>
	<![endif]-->
	<a href="#main" class="muted hide">{$lblSkipToContent|ucfirst}</a>

	<div class="navbar-wrapper navbar-fixed-top">
		<div class="navbar navbar-inverse">
			<div class="navbar-inner">
				<div class="container">
					<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</a>
					<a class="brand" href="/">{$siteTitle}</a>

					<div class="nav-collapse collapse">
						{$var|getnavigation:'page':0:1}
						{iteration:positionTop}
							{$positionTop.blockContent}
						{/iteration:positionTop}
						{* @todo {include:core/layout/templates/languages.tpl} *}
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="main" class="container">
		{include:core/layout/templates/breadcrumb.tpl}

		<div class="row">
			<div class="span9">
				{* Page title *}
				{option:!hideContentTitle}
					<header class="page-header" role="banner">
						<h1 itemprop="name">{$page.title}</h1>
					</header>
				{/option:!hideContentTitle}

				{* Main position *}
				{iteration:positionMain}
					{option:positionMain.blockIsHTML}
						{$positionMain.blockContent}
					{/option:positionMain.blockIsHTML}
					{option:!positionMain.blockIsHTML}
						{$positionMain.blockContent}
					{/option:!positionMain.blockIsHTML}
				{/iteration:positionMain}
			</div>
			<div class="span3">
				<div class="row">
					{* Right position *}
					{iteration:positionRight}
						<div class="span3">
							{option:positionRight.blockIsHTML}
								{$positionRight.blockContent}
							{/option:positionRight.blockIsHTML}
							{option:!positionRight.blockIsHTML}
								{$positionRight.blockContent}
							{/option:!positionRight.blockIsHTML}
						</div>
					{/iteration:positionRight}
				</div>
			</div>
		</div>
		{include:core/layout/templates/footer.tpl}
	</div>
</body>
</html>