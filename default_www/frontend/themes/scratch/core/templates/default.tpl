{include:file='{$FRONTEND_CORE_PATH}/layout/templates/head.tpl'}

<body id="default" class="{$LANGUAGE} frontend">
	<div id="container">

		<div id="header">
			<h1><a href="/">{$siteTitle}</a></h1>
			<div id="language">
				{include:file='{$FRONTEND_CORE_PATH}/layout/templates/languages.tpl'}
			</div>
			<div id="navigation">
				{$var|getnavigation:'page':0:1}
			</div>
		</div>

		<div id="main">
			<div id="subnavigation">
				{$var|getsubnavigation:'page':{$page['id']}:2}
				&nbsp;
			</div>

			<div id="content" class="content">
				{include:file='{$FRONTEND_CORE_PATH}/layout/templates/breadcrumb.tpl'}
				{option:!hideContentTitle}<h2 class="pageTitle">{$page['title']}</h2>{/option:!hideContentTitle}

				{* Block 1 *}
				{option:block1IsHTML}
					<div class="content">
						{$block1}
					</div>
				{/option:block1IsHTML}
				{option:!block1IsHTML}
					{include:file='{$block1}'}
				{/option:!block1IsHTML}

				{* Block 2 *}
				{option:block2IsHTML}
					<div class="content">
						{$block2}
					</div>
				{/option:block2IsHTML}
				{option:!block2IsHTML}
					{include:file='{$block2}'}
				{/option:!block2IsHTML}
			</div>
			<div id="sidebar">
				&nbsp;
			</div>
		</div>

		{include:file='{$FRONTEND_CORE_PATH}/layout/templates/footer.tpl'}
	</div>
</body>
</html>