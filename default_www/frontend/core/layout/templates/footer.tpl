		<div id="footer">
			<ul>
				<li>© {$currentTimestamp|format:"Y"} {$siteTitle}</li>
				{iteration:footerLinks}
				<li{option:footerLinks.selected} class="selected"{/option:footerLinks.selected}>
					<a href="{$footerLinks.url}">{$footerLinks.navigationTitle}</a>
				</li>
				{/iteration:footerLinks}
				<li><a href="http://www.netlash.com">Webdesign Netlash</a></li>
			</ul>
		</div>

		<!-- Site wide HTML -->
		{$siteWideHTML}