{*
	variables that are available:
	- {$widgetBlogRecentArticlesFull}: contains an array with all posts, each element contains data about the post
*}

{option:widgetBlogRecentArticlesFull}
	<section class="mod">
		<div class="inner">
			<header class="mainTitle">
				<h3>{$lblRecentArticles|ucfirst}</h3>
			</header>
			<div class="bd">
				{iteration:widgetBlogRecentArticlesFull}
					<article class="mod col col-2{option:widgetBlogRecentArticlesFull.last} lastCol{/option:widgetBlogRecentArticlesFull.last}">
						<div class="inner">
							<header>
								<h4><a href="{$widgetBlogRecentArticlesFull.full_url}" title="{$widgetBlogRecentArticlesFull.title}">{$widgetBlogRecentArticlesFull.title}</a></h4>
								<ul>
									<li>{$msgWrittenBy|ucfirst|sprintf:{$widgetBlogRecentArticlesFull.user_id|usersetting:'nickname'}} {$lblOn} {$widgetBlogRecentArticlesFull.publish_on|date:{$dateFormatLong}:{$LANGUAGE}}</li>
									<li>
										{option:!widgetBlogRecentArticlesFull.comments}<a href="{$widgetBlogRecentArticlesFull.full_url}#{$actComment}">{$msgBlogNoComments|ucfirst}</a>{/option:!widgetBlogRecentArticlesFull.comments}
										{option:widgetBlogRecentArticlesFull.comments}
											{option:widgetBlogRecentArticlesFull.comments_multiple}<a href="{$widgetBlogRecentArticlesFull.full_url}#{$actComments}">{$msgBlogNumberOfComments|sprintf:{$widgetBlogRecentArticlesFull.comments_count}}</a>{/option:widgetBlogRecentArticlesFull.comments_multiple}
											{option:!widgetBlogRecentArticlesFull.comments_multiple}<a href="{$widgetBlogRecentArticlesFull.full_url}#{$actComments}">{$msgBlogOneComment}</a>{/option:!widgetBlogRecentArticlesFull.comments_multiple}
										{/option:widgetBlogRecentArticlesFull.comments}
									</li>
									<li><a href="{$widgetBlogRecentArticlesFull.category_full_url}" title="{$widgetBlogRecentArticlesFull.category_title}">{$widgetBlogRecentArticlesFull.category_title}</a></li>
								</ul>
		 					</header>
							<div class="bd">
								<p>Nunc auctor bibendum eros. Maecenas porta accumsan mauris. Etiam enim enim, elementum sed, bibendum quis, rhoncus...</p>
								<p class="clearfix"><a <a href="{$widgetBlogRecentArticlesFull.full_url}" title="{$widgetBlogRecentArticlesFull.title}" class="blueButton">Read more</a></p>
							</div>
						</div>
					</article>
				{/iteration:widgetBlogRecentArticlesFull}
			</div>
			<footer class="ft">
				<p>
					<a href="{$var|geturlforblock:'blog'}">{$lblBlogArchive|ucfirst}</a>
				</p>
			</footer>
		</div>
	</section>
{/option:widgetBlogRecentArticlesFull}