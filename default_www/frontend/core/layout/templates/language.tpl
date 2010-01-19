			{option:languages}
			<div id="language">
				<ul>
					{iteration:languages}
					<li{option:languages.current} class="selected"{/option:languages.current}>
						<a href="{$languages.url}">{$languages.label}</a>
					</li>
					{/iteration:languages}
					<li class="selected"><a href="/nl">NL</a></li>
					<li><a href="/fr">FR</a></li>
					<li><a href="/en">EN</a></li>
				</ul>
			</div>
			{/option:languages}