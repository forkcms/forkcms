<ul>
	<li>&copy; {$now|date:'Y'} {$siteTitle}</li>
	{iteration:footerLinks}
		<li{option:footerLinks.selected} class="selected"{/option:footerLinks.selected}>
			<a href="{$footerLinks.url}" title="{$footerLinks.title}"{option:footerLinks.rel} rel="{$footerLinks.rel}"{/option:footerLinks.rel}{option:footerLinks.redirect_blank} target="_blank"{/option:footerLinks.redirect_blank}>
				{$footerLinks.navigation_title}
			</a>
		</li>
	{/iteration:footerLinks}
	<li><a href="http://www.fork-cms.be" title="Fork CMS">Fork CMS</a></li>
</ul>