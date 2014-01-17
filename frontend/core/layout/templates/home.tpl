{include:core/layout/templates/head.tpl}

<body class="{$LANGUAGE}" itemscope itemtype="http://schema.org/WebPage">
	<!--[if lt IE 8]>
		<div class="alert-box">
			<p>You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser to improve your experience.</p>
		</div>
	<![endif]-->

	{* Header *}
	{include:core/layout/templates/header.tpl}

	<main id="main" role="main">

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