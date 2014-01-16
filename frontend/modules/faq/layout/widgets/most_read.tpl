{*
	variables that are available:
	- {$widgetFaqMostRead}: contains an array with all posts, each element contains data about the post
*}

{option:widgetFaqMostRead}
	<section id="faqMostReadWidget">
		<header>
			<h2>{$lblMostReadQuestions|ucfirst}</h2>
		</header>
		<ul>
			{iteration:widgetFaqMostRead}
				<li><a href="{$widgetFaqMostRead.full_url}" title="{$widgetFaqMostRead.question}">{$widgetFaqMostRead.question}</a></li>
			{/iteration:widgetFaqMostRead}
		</ul>
	</section>
{/option:widgetFaqMostRead}
