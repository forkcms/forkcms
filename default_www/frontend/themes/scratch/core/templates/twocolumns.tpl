{include:{$THEME_PATH}/core/templates/head.tpl}

<body id="twocolumns" class="{$LANGUAGE} frontend">
	<div id="container">

		<div id="header">
			<h2><a href="/">{$siteTitle}</a></h2>
			<div id="language">
				{include:{$FRONTEND_CORE_PATH}/layout/templates/languages.tpl}
			</div>
			<div id="navigation">
				{$var|getnavigation:'page':0:1}
			</div>
		</div>

		<div id="main">

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

				{* Block 3 (default: Categories) *}
				{option:block3IsHTML}
					{option:block3}
						<div class="mod">
							<div class="inner">
								<div class="bd">
									{$block3}
								</div>
							</div>
						</div>
					{/option:block3}
				{/option:block3IsHTML}
				{option:!block3IsHTML}
					{include:{$block3}}
				{/option:!block3IsHTML}

				{* Block 4 (default: Archive) *}
				{option:block4IsHTML}
					{option:block4}
						<div class="mod">
							<div class="inner">
								<div class="bd">
									{$block4}
								</div>
							</div>
						</div>
					{/option:block4}
				{/option:block4IsHTML}
				{option:!block4IsHTML}
					{include:{$block4}}
				{/option:!block4IsHTML}

				{* Block 5 (default: Recent articles) *}
				{option:block5IsHTML}
					{option:block5}
						<div class="mod">
							<div class="inner">
								<div class="bd">
									{$block5}
								</div>
							</div>
						</div>
					{/option:block5}
				{/option:block5IsHTML}
				{option:!block5IsHTML}
					{include:{$block5}}
				{/option:!block5IsHTML}

				{* Block 6 (default: Editor) *}
				{option:block6IsHTML}
					{option:block6}
						<div class="mod">
							<div class="inner">
								<div class="bd">
									{$block6}
								</div>
							</div>
						</div>
					{/option:block6}
				{/option:block6IsHTML}
				{option:!block6IsHTML}
					{include:{$block6}}
				{/option:!block6IsHTML}
			</div>
		</div>

		<div id="footer">
			{include:{$FRONTEND_CORE_PATH}/layout/templates/footer.tpl}
		</div>
	</div>
</body>
</html>