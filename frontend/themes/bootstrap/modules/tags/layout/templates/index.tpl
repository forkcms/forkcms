{*
	variables that are available:
	- {$tags}: contains an array with all tags that are used on the site, each element contains data about the tag
*}
<section id="tagsIndex" class="tags">
	{option:!tags}
		<div class="alert alert-info" role="alert">{$msgTagsNoItems}</div>
	{/option:!tags}
	{option:tags}
		<ul>
			{iteration:tags}
				<li>
					<a href="{$var|geturlforblock:'tags':'detail'}/{$tags.url}" rel="tag">
						{$tags.name}
					</a>
					<span class="badge hidden-phone">{$tags.number}</span>
				</li>
			{/iteration:tags}
		</ul>
	{/option:tags}
</section>