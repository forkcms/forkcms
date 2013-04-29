{option:searchResults}
<section id="searchNotfoundWidget" class="mod">
	<div class="inner">
		<header class="hd">
			<h3>{$lblSearchedOn|ucfirst} {$searchTerm}</h3>
		</header>
		<div class="bd">
			{iteration:searchResults}
				<div class="bd">
					<section class="mod">
						<div class="inner">
							<header class="hd">
								<h3>
									<a href="{$searchResults.full_url}" title="{$searchResults.title}">
										{$searchResults.title}
									</a>
								</h3>
							</header>
							<div class="bd content">
								{option:!searchResults.introduction}{$searchResults.text|truncate:200}{/option:!searchResults.introduction}
								{option:searchResults.introduction}{$searchResults.introduction}{/option:searchResults.introduction}
							</div>
						</div>
					</section>
				</div>
			{/iteration:searchResults}
		</div>
	</div>
</section>
{/option:searchResults}