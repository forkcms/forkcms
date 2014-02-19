{include:core/layout/templates/head.tpl}

<body class="{$LANGUAGE}" itemscope itemtype="http://schema.org/WebPage">
	<!--[if lt IE 8]>
		<p>You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser to improve your experience.</p>
	<![endif]-->

	{* Header *}
	{include:core/layout/templates/header.tpl}

	<main id="main" role="main">
		{include:core/layout/templates/breadcrumb.tpl}

		{* Page title *}
		{option:!hideContentTitle}
			<header>
				<h1>{$page.title}</h1>
			</header>
		{/option:!hideContentTitle}

		{* Main position *}
		{option:positionMain}
			{iteration:positionMain}
				{$positionMain.blockContent}
			{/iteration:positionMain}
		{/option:positionMain}
	</main>

	{* Footer *}
	{include:core/layout/templates/footer.tpl}

</body>
</html>