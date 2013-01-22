{*
	variables that are available:
	- {$category}: contains data about the category
	- {$items}: contains an array with all posts, each element contains data about the post
*}

{option:items}
	<section id="blogCategory" class="blog">
		{iteration:items}
			<article class="article" itemscope itemtype="http://schema.org/Blog">
			<meta itemprop="interactionCount" content="UserComments:{$items.comment_count}">
			<meta itemprop="author" content="{$items.user_id|usersetting:'nickname'}">
				<header>
					<div class="row-fluid title">
						<div class="span10">
							<h3><a href="{$items.full_url}" title="{$items.title}">{$items.title}</a></h3>
						</div>
						<div class="span2 commentCount">
							{option:items.comments}
								<i class="icon-comment"></i>
								{option:items.comments_multiple}<a href="{$items.full_url}#{$actComments}">{$msgBlogNumberOfComments|sprintf:{$items.comments_count}}</a>{/option:items.comments_multiple}
								{option:!items.comments_multiple}<a href="{$items.full_url}#{$actComments}">{$msgBlogOneComment}</a>{/option:!items.comments_multiple}
							{/option:items.comments}
						</div>
					</div>
					<div class="row-fluid muted meta">
						<div class="span6">
							<span class="hideText">{$msgWrittenBy|ucfirst|sprintf=''} </span>{$items.user_id|usersetting:'nickname'}
							<span class="hideText">{$lblOn}</span> <time itemprop="datePublished" datetime="{$items.publish_on|date:'Y-m-d\TH:i:s'}">{$items.publish_on|date:{$dateFormatLong}:{$LANGUAGE}}</time>
						</div>
						<div class="span6 metaExtra">
							<span class="hideText">{$lblInThe} </span>{$lblCategory|ucfirst}: <a itemprop="articleSection" href="{$items.category_full_url}">{$items.category_title}</a>{option:!item.tags}.{/option:!item.tags}
							{option:items.tags}
								<span class="hideText">{$lblWithThe}</span> {$lblTags|ucfirst}:
								<span itemprop="keywords">
		    		    			{iteration:items.tags}
		    		    				<a class="tag" href="{$item.tags.full_url}" rel="tag">{$items.tags.name}</a>{option:!items.tags.last}<span class="hideText">,</span> {/option:!items.tags.last}
		    		    			{/iteration:items.tags}
		    		    		</span>
		    		    	{/option:items.tags}
						</div>
					</div>
				</header>
				<div class="articleBody" itemprop="articleBody">
					{option:items.image}<img itemprop="image" class="img-polaroid span4 pull-right" src="{$FRONTEND_FILES_URL}/blog/images/source/{$items.image}" alt="{$items.title}" />{/option:items.image}
					{option:!items.introduction}{$items.text}{/option:!items.introduction}
					{option:items.introduction}{$items.introduction}{/option:items.introduction}
				</div>
			</article>
		{/iteration:items}
	</section>
	{include:core/layout/templates/pagination.tpl}
{/option:items}
