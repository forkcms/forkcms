{include:file='{$FRONTEND_CORE_PATH}/layout/templates/head.tpl'}

<body class="{$LANGUAGE} frontend">
	<div id="container">

		<div id="header">
			<h1><a href="/">{$siteTitle}</a></h1>
			{include:file='{$FRONTEND_CORE_PATH}/layout/templates/languages.tpl'}
		</div>

		<div id="main">
			<div id="navigation">
				{$var|getnavigation}
			</div>

			<div id="content">
				<!-- {include:file='{$FRONTEND_CORE_PATH}/layout/templates/breadcrumb.tpl'} -->

				{option:!hideContentTitle}<h2 class="pageTitle">{$page['title']}</h2>{/option:!hideContentTitle}

				<h1>block 1</h1>
				{option:block1IsHTML}{$block1}{/option:block1IsHTML}
				{option:!block1IsHTML}{include:file='{$block1}'}{/option:!block1IsHTML}

				<h1>block 2</h1>
				{option:block2IsHTML}{$block2}{/option:block2IsHTML}
				{option:!block2IsHTML}{include:file='{$block2}'}{/option:!block2IsHTML}

				<h1>block 3</h1>
				{option:block3IsHTML}{$block3}{/option:block3IsHTML}
				{option:!block3IsHTML}{include:file='{$block3}'}{/option:!block3IsHTML}

				<h1>block 4</h1>
				{option:block4IsHTML}{$block4}{/option:block4IsHTML}
				{option:!block4IsHTML}{include:file='{$block4}'}{/option:!block4IsHTML}

				<h1>block 5</h1>
				{option:block5IsHTML}{$block5}{/option:block5IsHTML}
				{option:!block5IsHTML}{include:file='{$block5}'}{/option:!block5IsHTML}

				<h1>block 6</h1>
				{option:block6IsHTML}{$block6}{/option:block6IsHTML}
				{option:!block6IsHTML}{include:file='{$block6}'}{/option:!block6IsHTML}

				<h1>block 7</h1>
				{option:block7IsHTML}{$block7}{/option:block7IsHTML}
				{option:!block7IsHTML}{include:file='{$block7}'}{/option:!block7IsHTML}

				<h1>block 8</h1>
				{option:block8IsHTML}{$block8}{/option:block8IsHTML}
				{option:!block8IsHTML}{include:file='{$block8}'}{/option:!block8IsHTML}

				<h1>block 9</h1>
				{option:block9IsHTML}{$block9}{/option:block9IsHTML}
				{option:!block9IsHTML}{include:file='{$block9}'}{/option:!block9IsHTML}

			</div>
		</div>

		{include:file='{$FRONTEND_CORE_PATH}/layout/templates/footer.tpl'}
	</div>
</body>
</html>