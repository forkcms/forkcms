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

{* @TODO Remove when needed *}
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>