<footer>
	<p class="pull-right"><a href="#">Back to top</a></p>
	<p>
		© {$now|date:'Y'} {$siteTitle} &middot;
		{iteration:footerLinks}
			<a href="{$footerLinks.url}" title="{$footerLinks.title}"{option:footerLinks.rel} rel="{$footerLinks.rel}"{/option:footerLinks.rel}>
				{$footerLinks.navigation_title}
			</a> &middot;
		{/iteration:footerLinks}
		<a href="http://www.sumocoders.be/?utm_source=...&amp;utm_medium=credits&amp;utm_campaign=client_sites" rel="external">SumoCoders</a>
	</p>
</footer>

{* Site wide HTML *}
{$siteHTMLFooter}


<address>
	SumoCoders
	Kerkstraat 108

</address>