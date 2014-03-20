{*
	variables that are available:
	- {partners}
*}
<section id="blogRecentCommentsWidget" class="mod">
	<div class="inner">
		{option:partners}
			<ul class="bxslider">
				{iteration:partners}
					<li>
						<a href="{$partners.url}">
							<img src="{$FRONTEND_FILES_URL}/partners/{$partners.widget}/source/{$partners.img}" alt="{$partners.name}" />
						</a>
					</li>
				{/iteration:partners}
			</ul>
		{/option:partners}
	</div>
</section>
