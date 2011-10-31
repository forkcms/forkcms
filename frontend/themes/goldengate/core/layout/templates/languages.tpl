{option:languages}
	<ul>
		{iteration:languages}
			{option:languages.current}
				<li class="selected firstChild">
					<a href="{$languages.url}"><span class="icon worldIcon"></span><span class="iconWrapper">{$languages.label}</span><span class="icon dropdownIcon"></span></a>
				</li>
			{/option:languages.current}
		{/iteration:languages}
		{iteration:languages}
			{option:!languages.current}
				<li>
					<a href="{$languages.url}"><span class="icon worldIcon"></span><span class="iconWrapper">{$languages.label}</span><span class="icon dropdownIcon"></span></a>
				</li>
			{/option:!languages.current}
		{/iteration:languages}
	</ul>
{/option:languages}