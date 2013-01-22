{*
	variables that are available:
	- {$items}: contains an array with all posts, each element contains data about the post
*}

{option:!items}
	<div id="blogIndex" class="blog">
		<section>
			<div class="alert" role="alert">
				{$msgBlogNoItems}
			</div>
		</section>
	</div>
{/option:!items}
{option:items}
	<div id="blogIndex" class="blog">
		{iteration:items}
			<article class="article" itemscope itemtype="http://schema.org/Blog" role="main">
				<meta itemprop="interactionCount" content="UserComments:{$items.comments_count}">
				<meta itemprop="author" content="{$items.user_id|usersetting:'nickname'}">
				<header role="banner">
				    <div class="row-fluid title">
				    	<div class="span10">
				    		<h2 itemprop="name"><a href="{$items.full_url}" title="{$items.title}">{$items.title}</a></h2>
				    	</div>
				    	<div class="span2 commentCount">
				    		<i class="icon-comment"></i>
				    		{option:!items.comments}<a href="{$items.full_url}#{$actComment}" itemprop="discussionUrl">{$msgBlogNumberOfComments|sprintf:{$items.comments_count}}</a>{/option:!items.comments}
				    		{option:items.comments}
				    			{option:items.comments_multiple}<a href="{$items.full_url}#{$actComments}" itemprop="discussionUrl">{$msgBlogNumberOfComments|sprintf:{$items.comments_count}}</a>{/option:items.comments_multiple}
				    			{option:!items.comments_multiple}<a href="{$items.full_url}#{$actComments}" itemprop="discussionUrl">{$msgBlogOneComment}</a>{/option:!items.comments_multiple}
				    		{/option:items.comments}
				    	</div>
				    </div>
				    <div class="row-fluid muted meta">
				    	<div class="span6">
				    		<span class="hideText">{$msgWrittenBy|ucfirst|sprintf:''}</span> {$items.user_id|usersetting:'nickname'}
				    		<span class="hideText">{$lblOn}</span> <time itemprop="datePublished" datetime="{$items.publish_on|date:'Y-m-d\TH:i:s'}">{$items.publish_on|date:{$dateFormatLong}:{$LANGUAGE}}</time>
				    	</div>
				    	<div class="span6 metaExtra">
				    		<span class="hideText">{$lblInThe} </span>{$lblCategory|ucfirst}: <a itemprop="articleSection" href="{$items.category_full_url}">{$items.category_title}</a>{option:!items.tags}.{/option:!items.tags}
				    		{option:items.tags}
				    			<span class="hideText">{$lblWithThe}</span> {$lblTags|ucfirst}:
				    			<span itemprop="keywords">
				    				{iteration:items.tags}
				    					<a class="tag" href="{$items.tags.full_url}" rel="tag">{$items.tags.name}</a>{option:!items.tags.last}<span class="hideText">,</span> {/option:!items.tags.last}
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
	</div>
	{include:core/layout/templates/pagination.tpl}
{/option:items}
