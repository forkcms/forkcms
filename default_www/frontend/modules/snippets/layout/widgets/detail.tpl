{*
	In most cases a snippet should be styled in a very specif way. In the array there is an variable 'is<ID>'
	So if you want to style the snippet with id 1 totally different you can use the option: snippet['is1'].
	eg:

	{option:snippet['is1']}
		<div class="snippet">
			{$snippet|dump}
		</div>
	{/option:snippet['is1']}
 *}

{option:snippet}
	<div class="widget">
		<h3>{$snippet['title']}</h3>
		{$snippet['content']}
	</div>
{/option:snippet}