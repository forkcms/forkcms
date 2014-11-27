{include:Core/Layout/Templates/head.tpl}

<body class="{$LANGUAGE}" itemscope itemtype="http://schema.org/WebPage">
	{include:Core/Layout/Templates/notifications.tpl}

	<div class="navbar-wrapper">
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
						{* @todo {include:Core/Layout/Templates/languages.tpl} *}
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="main" class="container">
		<div class="row">
			<div class="col-xs-12">
				{* Main position *}
				<div class="row">
					<div id="errorIcon">
						<img src="{$THEME_URL}/apple-touch-icon-precomposed.png" class="img-circle">
					</div>
				</div>
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
		{include:Core/Layout/Templates/footer.tpl}
	</div>
</body>
</html>