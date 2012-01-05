{include:core/layout/templates/head.tpl}
<body>
<div id="wrap" class="home">
	<header id="head" role="banner">
		<h1><a href="/" title="{$siteTitle}">{$siteTitle}</a></h1>
		<p id="skip"><a href="#main">{$lblSkipToContent|ucfirst}</a></p>
		<nav>
			{$var|getnavigation:'page':0:1}
		</nav>
		{* Top (default: search widget) *}
		{iteration:positionTop}
			{option:!positionTop.blockIsHTML}
			{$positionTop.blockContent}
			{/option:!positionTop.blockIsHTML}
		{/iteration:positionTop}
	</header>
	<section id="content" class="cols">
		{* Advertisement (default: editor for image) *}
		{option:positionAdvertisement}
		{iteration:positionAdvertisement}
			{option:positionAdvertisement.blockIsHTML}
			<div id="intropic">{$positionAdvertisement.blockContent}</div>
			{/option:positionAdvertisement.blockIsHTML}
		{/iteration:positionAdvertisement}
		{/option:positionAdvertisement}

		{* Main (default: editor for intro text) *}
		{option:positionMain}
		<section id="main" class="c8">
			<h1>{$page.title}</h1>
			{iteration:positionMain}
			{option:positionMain.blockIsHTML}
			<article class="c4 before1">
				{$positionMain.blockContent}
			</article>
			{/option:positionMain.blockIsHTML}
			{option:!positionMain.blockIsHTML}
				{$positionMain.blockContent}
			{/option:!positionMain.blockIsHTML}
			{/iteration:positionMain}
		</section>
		{/option:positionMain}

		{* Widgets (default: recent articles and recent comments widget) *}
		{option:positionWidgets}
		<section id="widgets" class="c7 before1">
			{iteration:positionWidgets}
			<section class="c3 {cycle: 'first':'last'} ">
				{$positionWidgets.blockContent}
			</section>
			{/iteration:positionWidgets}
		</section>
		{/option:positionWidgets}
	</section>
	{include:core/layout/templates/footer.tpl}
</div>
{* General Javascript *}
{iteration:jsFiles}
<script src="{$jsFiles.file}"></script>
{/iteration:jsFiles}
</body>
</html>