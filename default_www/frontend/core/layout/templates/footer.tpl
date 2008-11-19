		<div id="footer">
			<ul>
				<li>&copy; {$SITE_TITLE}</li>
				{iteration:iFooterLinks}<li{option:oIsCurrentPage} class="selected"{/option:oIsCurrentPage}><a href="{$url}" title="{$title|ucfirst}">{$title|ucfirst}</a></li>{/iteration:iFooterLinks}
				<li class="last"><a href="http://www.netlash.com" title="{$lblNetlashWebdesignAndGraphicDesign|ucfirst}">{$lblWebdesignNetlash|ucfirst}</a></li>
			</ul>
		</div>
		<!-- start site-wide html -->{$siteWideHtml}<!-- end site-wide html -->
