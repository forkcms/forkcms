{include:{$THEME_PATH}/core/templates/head.tpl}

<body id="default" class="{$LANGUAGE} frontend">
	<div id="container">

		<div id="header">
			<h1><a href="/">{$siteTitle}</a></h1>
			<div id="language">
				{include:{$FRONTEND_CORE_PATH}/layout/templates/languages.tpl}
			</div>
			<div id="navigation">
				{$var|getnavigation:'page':0:1}
			</div>
		</div>

		<div id="main">
			<div id="subnavigation">
				{$var|getsubnavigation:'page':{$page.id}:2}
				&nbsp;
			</div>

			<div id="content">
				{include:{$FRONTEND_CORE_PATH}/layout/templates/breadcrumb.tpl}

				{option:!hideContentTitle}
					<div class="pageTitle">
						<h2>{$page.title}</h2>
					</div>
				{/option:!hideContentTitle}

				{* Block 1 (default: Editor) *}
				{option:block1IsHTML}
					{option:block1}
						<div class="mod">
							<div class="inner">
								<div class="bd">
									{$block1}
								</div>
							</div>
						</div>
					{/option:block1}
				{/option:block1IsHTML}
				{option:!block1IsHTML}
					{include:{$block1}}
				{/option:!block1IsHTML}

				{* Block 2 (default: Module) *}
				{option:block2IsHTML}
					{option:block2}
						<div class="mod">
							<div class="inner">
								<div class="bd">
									{$block2}
								</div>
							</div>
						</div>
					{/option:block2}
				{/option:block2IsHTML}
				{option:!block2IsHTML}
					{include:{$block2}}
				{/option:!block2IsHTML}
			</div>
			<div id="sidebar">
				&nbsp;
			</div>
		</div>

		<div id="footer">
			{include:{$FRONTEND_CORE_PATH}/layout/templates/footer.tpl}
		</div>
	</div>
</body>
</html>