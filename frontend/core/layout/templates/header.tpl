<header>

	{* SEO logo: H1 for home, p for other pages *}

	{option:isPage1}
		<h1><a title="{$siteTitle}" href="/">{$siteTitle}</a></h1>
	{/option:isPage1}

	{option:!isPage1}
		<p><a title="{$siteTitle}" href="/">{$siteTitle}</a></p>
	{/option:!isPage1}

	{* Navigation *}
	<nav>
		{$var|getnavigation:'page':0:1}
	</nav>

	{* Meta *}
	<nav>
		{$var|getnavigation:'meta':0:1}
	</nav>

	{* Languages *}
	{option:languages}
		<nav>
			{include:core/layout/templates/languages.tpl}
		</nav>
	{/option:languages}

	{* Search widget: so you don't have to make a position *}
	{$var|parsewidget:'search':'form'}

</header>

