{*
	variables that are available:
	- {$widgetFaqMostRead}: contains an array with all posts, each element contains data about the post
*}

{option:widgetFaqMostRead}
	<section id="faqMostReadWidget" class="well faq">
		<header role="banner">
			<h3>{$lblMostReadQuestions|ucfirst}</h3>
		</header>
		<ul>
			{iteration:widgetFaqMostRead}
				<li><a href="{$widgetFaqMostRead.full_url}">{$widgetFaqMostRead.question}</a></li>
			{/iteration:widgetFaqMostRead}
		</ul>
	</section>
{/option:widgetFaqMostRead}