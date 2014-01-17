<footer>
	<nav>
		<ul>
			<li>&copy; {$now|date:'Y'} {$siteTitle}</li>
			{iteration:footerLinks}
				<li{option:footerLinks.selected} class="selected"{/option:footerLinks.selected}>
					<a href="{$footerLinks.url}" title="{$footerLinks.title}"{option:footerLinks.rel} rel="{$footerLinks.rel}"{/option:footerLinks.rel}>
						{$footerLinks.navigation_title}
					</a>
				</li>
			{/iteration:footerLinks}
			<li><a href="http://www.fork-cms.be" title="Fork CMS">Fork CMS</a></li>
		</ul>
	</nav>
</footer>

<noscript>
	<h4>{$lblEnableJavascript|ucfirst}</h4>
	<p>{$msgEnableJavascript}</p>
</noscript>

{* General Javascript *}
{iteration:jsFiles}
	<script src="{$jsFiles.file}"></script>
{/iteration:jsFiles}

{* Site wide HTML *}
{$siteHTMLFooter}
