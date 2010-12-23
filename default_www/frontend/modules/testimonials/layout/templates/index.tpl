{*
	variables that are available:
	- {$testimonialsItems}: contains data about all testimonials
*}

{option:!testimonialsItems}
	<div id="testimonialsIndex">
		<div class="mod">
			<div class="inner">
				<div class="bd">
					<p>{$msgTestimonialsNoItems}</p>
				</div>
			</div>
		</div>
	</div>
{/option:!testimonialsItems}
{option:testimonialsItems}
	<div id="testimonialsIndex">
		{iteration:testimonialsItems}
			<div class="mod testimonial">
				<div class="inner">
					<blockquote class="bd">{$testimonialsItems.testimonial}</blockquote>
					<p class="name">{$testimonialsItems.name}</p>
				</div>
			</div>
		{/iteration:testimonialsItems}
	</div>
{/option:testimonialsItems}