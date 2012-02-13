{include:core/layout/templates/head.tpl}

<body>
<div id="wrap">
<header id="head" role="banner">
	<h1><a href="/" title="{$siteTitle}">{$siteTitle}</a></h1>
	<p id="skip"><a href="#main">{$lblSkipToContent|ucfirst}</a></p>
	<nav>
		{$var|getnavigation:'page':0:2}
	</nav>
	{* Top (default: search widget) *}
	{iteration:positionTop}
		{option:!positionTop.blockIsHTML}
		{$positionTop.blockContent}
		{/option:!positionTop.blockIsHTML}
	{/iteration:positionTop}
</header>

<section id="content" class="cols">
	<h1>{$pageTitle}</h1>

	{* Wide *}
	{option:positionWide}
	<section class="full">
		{iteration:positionWide}
		<article class="content">
			{$positionWide.blockContent}
		</article>
		{/iteration:positionWide}
	</section>
	{/option:positionWide}

	{* Main *}
	<section id="main" class="c4 before1">
	{iteration:positionMain}
	<article class="content">
		{$positionMain.blockContent}
	</article>
	{/iteration:positionMain}
	</section>

	{* Right *}
	{option:positionRight.blockIsHTML}
	{iteration:positionRight}
	<aside class="page-aside c3">
		{$positionRight.blockContent}
	</aside>
	{/iteration:positionRight}
	{/option:positionRight.blockIsHTML}

	{option:!positionRight.blockIsHTML}
	<section id="widgets" class="c2 before1">
	{iteration:positionRight}
		{$positionRight.blockContent}
	{/iteration:positionRight}
	</section>
	{/option:!positionRight.blockIsHTML}

</section>

{include:core/layout/templates/footer.tpl}

</div>
{* General Javascript *}
{iteration:jsFiles}
<script src="{$jsFiles.file}"></script>
{/iteration:jsFiles}
<script src="{$THEME_URL}/core/layout/js/scripts.js"></script>
</body>
</html>