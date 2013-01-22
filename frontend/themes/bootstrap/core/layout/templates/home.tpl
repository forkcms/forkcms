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

	{option:positionSlideshow}
		<div id="myCarousel" class="carousel slide">
			<div class="carousel-inner">
				{* Slideshow position *}
				{iteration:positionSlideshow}
					{option:positionSlideshow.blockIsHTML}
						{$positionSlideshow.blockContent}
					{/option:positionSlideshow.blockIsHTML}
					{option:!positionSlideshow.blockIsHTML}
						{$positionSlideshow.blockContent}
					{/option:!positionSlideshow.blockIsHTML}
				{/iteration:positionSlideshow}
			</div>

			<a class="left carousel-control" href="#myCarousel" data-slide="prev">&lsaquo;</a>
			<a class="right carousel-control" href="#myCarousel" data-slide="next">&rsaquo;</a>
		</div>
	{/option:positionSlideshow}

	<div id="main" class="container marketing">
		{include:core/layout/templates/breadcrumb.tpl}

		{option:positionFeatures}
			<div class="row">
				{iteration:positionFeatures}
					{option:positionFeatures.blockIsHTML}
						{$positionFeatures.blockContent}
					{/option:positionFeatures.blockIsHTML}
					{option:!positionFeatures.blockIsHTML}
						{$positionFeatures.blockContent}
					{/option:!positionFeatures.blockIsHTML}
				{/iteration:positionFeatures}
			</div>
		{/option:positionFeatures}

		{* Main position *}
		{iteration:positionMain}
			{option:positionMain.blockIsHTML}
				<hr class="featurette-divider">
				{$positionMain.blockContent}
			{/option:positionMain.blockIsHTML}
			{option:!positionMain.blockIsHTML}
				{$positionMain.blockContent}
			{/option:!positionMain.blockIsHTML}
		{/iteration:positionMain}

		{include:core/layout/templates/footer.tpl}
	</div>
</body>
</html>