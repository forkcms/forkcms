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
	{option:positionMain}
	<section id="main" class="c4 before1">
		{iteration:positionMain}
		{option:!positionMain.blockIsHTML}
			{$positionMain.blockContent}
		{/option:!positionMain.blockIsHTML}
		{option:positionMain.blockIsHTML}
		<article class="content">
			{$positionMain.blockContent}
		</article>
		{/option:positionMain.blockIsHTML}
		{/iteration:positionMain}
	</section>
	{/option:positionMain}

	{* Right *}

	{option:positionRight}
	<section id="widgets" class="c3">
		{iteration:positionRight}
		{option:!positionRight.blockIsHTML}
		<div class="before1 c2">
			{$positionRight.blockContent}
		</div>
		{/option:!positionRight.blockIsHTML}
		{option:positionRight.blockIsHTML}
		<aside class="page-aside c3">
			{$positionRight.blockContent}
		</aside>
		{/option:positionRight.blockIsHTML}
		{/iteration:positionRight}
	</section>
	{/option:positionRight}

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