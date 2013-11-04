{* Write your section-titles likes this when you are making a one-pager: <h2><a name="usefulName" href="#" class="nonVisibleAnchor"></a></h2> *}

{include:core/layout/templates/head.tpl}

<body class="{$LANGUAGE}" itemscope itemtype="http://schema.org/WebPage">
	{include:core/layout/templates/notifications.tpl}

	<nav class="navbar navbar-default navbar-static-top" role="navigation">
    <div class="navbar-header"><button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
        <span class="sr-only"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a href="/" class="navbar-brand">{$siteTitle}</a>
    </div>
    <div class="collapse navbar-collapse navbar-ex1-collapse">
      {$var|getnavigation:'page':0:1}
      {iteration:positionTop}
				{$positionTop.blockContent}
			{/iteration:positionTop}
			{include:core/layout/templates/languages.tpl}
    </div>
  </nav>

	<div id="main" class="container">
		{include:core/layout/templates/breadcrumb.tpl}

		<div class="row">
			<div class="col-xs-12">
				{* Page title *}
				{option:!hideContentTitle}
					<header class="page-header" role="banner">
						<h1 itemprop="name">{$page.title}</h1>
					</header>
				{/option:!hideContentTitle}

				{* Main position *}
				{iteration:positionMain}
					<div class="row">
						<div class="col-xs-12">
							{option:positionMain.blockIsHTML}
								{$positionMain.blockContent}
							{/option:positionMain.blockIsHTML}
							{option:!positionMain.blockIsHTML}
								{$positionMain.blockContent}
							{/option:!positionMain.blockIsHTML}
						</div>
					</div>
				{/iteration:positionMain}
			</div>
		</div>

		{include:core/layout/templates/footer.tpl}
	</div>
</body>
</html>