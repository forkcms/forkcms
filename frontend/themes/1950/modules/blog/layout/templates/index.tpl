{*
	variables that are available:
	- {$items}: contains an array with all posts, each element contains data about the post
*}

{option:!items}
<div id="blogIndex">
	<section class="mod">
		<div class="inner">
			<div class="bd content">
				<p>{$msgBlogNoItems}</p>
			</div>
		</div>
	</section>
</div>
{/option:!items}
{option:items}
{iteration:items}
<article class="mod"> 
	<header> 
		<h2><a href="{$items.full_url}" title="{$items.title}">{$items.title}  <abbr title="View full blog post">&rarr;</abbr></a></h2> 
		<ul class="meta">
			<li>
				{* Date *}
				<time datetime="{$items.publish_on|date:'c':{$LANGUAGE}}">
					<span class="day">{$items.publish_on|date:'d':{$LANGUAGE}}</span>
					<span class="month">{$items.publish_on|date:'m':{$LANGUAGE}}</span>
					<span class="year">{$items.publish_on|date:'Y':{$LANGUAGE}}</span>
				</time>
			</li> 
			<li class="comments">
				{* Comments *}
				{option:!items.comments}<a href="{$items.full_url}#{$actComment}" class="nocomments" title="{$msgBlogNoComments|ucfirst}"><span class="x">{$msgBlogNoComments|ucfirst}</span></a>{/option:!items.comments}
				{option:items.comments}<a href="{$items.full_url}#{$actComments}" title="{$msgBlogComments|ucfirst}">{$items.comments_count}</a>{/option:items.comments}
			</li> 
		</ul>
	</header> 
	<div class="bd"> 
		{option:!items.introduction}{$items.text}{/option:!items.introduction}
		{option:items.introduction}{$items.introduction}{/option:items.introduction}
	</div>
	<footer>
		<ul class="meta">
			{* Tags *}
			{option:items.tags}
			<li class="tags">
				{iteration:items.tags}
					<a href="{$items.tags.full_url}" rel="tag" title="{$items.tags.name}">{$items.tags.name}</a>{option:!items.tags.last}, {/option:!items.tags.last}{option:items.tags.last}{/option:items.tags.last}
				{/iteration:items.tags}
			</li>
			{/option:items.tags}
			<li class="share"><a href="#">Share</a>
				<ul class="shareButtons"> 
						<li><div class="g-plusone" data-size="medium" data-count="false" href="{$SITE_URL}{$items.full_url}"></div></li>
						<li><a href="http://twitter.com/share" class="twitter-share-button" data-count="none" data-url="{$SITE_URL}{$items.full_url}">Tweet</a></li> 
						<li><iframe src="https://www.facebook.com/plugins/like.php?href={$SITE_URL}{$items.full_url}&amp;layout=button_count&amp;show_faces=false&amp;font=Arial&amp;locale=en_US" scrolling="no" frameborder="0" style="border:none; width:50px; height:20px"></iframe> 
						</li>
				</ul>
			</li>
		</ul>
		
	</footer>
</article>
{/iteration:items}
{include:core/layout/templates/pagination.tpl}
{/option:items}