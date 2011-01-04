{*
	variables that are available:
	- {$widgetTestimonialsRandomTestimonial}: contains data about a random testimonial
*}

{option:widgetTestimonialsRandomTestimonial}
	<div id="testimonialsRandomTestimonialWidget" class="mod">
		<div class="inner">
			<div class="hd">
				<h3>{$msgRandomTestimonial|ucfirst}</h3>
			</div>
			<blockquote class="bd">{$widgetTestimonialsRandomTestimonial['testimonial']}</blockquote>
			<p class="name">{$widgetTestimonialsRandomTestimonial['name']}</p>
			<a href="{$var|geturlforblock:'testimonials':'all_testimonials'}">{$msgReadMoreTestimonials}</a>
		</div>
	</div>
{/option:widgetTestimonialsRandomTestimonial}