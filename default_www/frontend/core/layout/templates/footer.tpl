<ul>
	<li>&copy; {$SITE_TITLE}</li>
	{iteration:iFooterLinks}{$TAB}<li{option:oFooterLinkSelected} class="selected"{/option:oFooterLinkSelected}><a href="{$footerLink}" title="{$footerTitle|ucfirst}">{$footerTitle|ucfirst}</a></li>{$BR}{/iteration:iFooterLinks}
	<li>v{$VERSION_NUMBER}</li>
	<li class="last"><a href="http://www.netlash.com" title="{$lblNetlashWebdesignAndGraphicDesign|ucfirst}">{$lblWebdesignNetlash|ucfirst}</a></li>
</ul>
{$siteWideFooterHTML}
