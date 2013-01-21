{*
	variables that are available:
	- {$widgetBlogRecentArticlesFull}: contains an array with all posts, each element contains data about the post
*}

{option:widgetBlogRecentArticlesFull}
	<section id="blogRecentArticlesFullWidget" class="mod">
		<header>
		    <h3>{$lblRecentArticles|ucfirst}</h3>
		</header>
		    {iteration:widgetBlogRecentArticlesFull}
		    	<article class="article" itemscope itemtype="http://schema.org/Blog">
		    		<meta itemprop="interactionCount" content="UserComments:{$widgetBlogRecentArticlesFull.comments_count}">
		    		<meta itemprop="author" content="{$widgetBlogRecentArticlesFull.user_id|usersetting:'nickname'}">
		    		<header>
		    			<div class="row-fluid title">
		    				<div class="span10">
		    					<h4><a href="{$widgetBlogRecentArticlesFull.full_url}" title="{$widgetBlogRecentArticlesFull.title}">{$widgetBlogRecentArticlesFull.title}</a></h4>
		    				</div>
		    				<div class="span2 commentCount">
		    					<i class="icon-comment"></i>
		    					{option:!widgetBlogRecentArticlesFull.comments}<a href="{$widgetBlogRecentArticlesFull.full_url}#{$actComment}">{$msgBlogNumberOfComments|sprintf:{$widgetBlogRecentArticlesFull.comments_count}}</a>{/option:!widgetBlogRecentArticlesFull.comments}
		    		    		{option:widgetBlogRecentArticlesFull.comments}
		    		    			{option:widgetBlogRecentArticlesFull.comments_multiple}<a href="{$widgetBlogRecentArticlesFull.full_url}#{$actComments}">{$msgBlogNumberOfComments|sprintf:{$widgetBlogRecentArticlesFull.comments_count}}</a>{/option:widgetBlogRecentArticlesFull.comments_multiple}
		    		    			{option:!widgetBlogRecentArticlesFull.comments_multiple}<a href="{$widgetBlogRecentArticlesFull.full_url}#{$actComments}">{$msgBlogOneComment}</a>{/option:!widgetBlogRecentArticlesFull.comments_multiple}
		    		    		{/option:widgetBlogRecentArticlesFull.comments}
		    				</div>
		    			</div>
		    			<div class="row-fluid muted meta">
		    				<div class="span6">
		    					<span class="hideText">{$msgWrittenBy|ucfirst|sprintf:''} </span>{$widgetBlogRecentArticlesFull.user_id|usersetting:'nickname'}
		    					<span class="hideText">{$lblOn}</span> <time itemprop="datePublished" datetime="{$widgetBlogRecentArticlesFull.publish_on|date:'Y-m-d\TH:i:s'}">{$widgetBlogRecentArticlesFull.publish_on|date:{$dateFormatLong}:{$LANGUAGE}}</time>
		    				</div>
		    				<div class="span6 metaExtra">
		    					<span class="hideText">{$lblInThe} </span>{$lblCategory|ucfirst}: <a href="{$widgetBlogRecentArticlesFull.category_full_url}" title="{$widgetBlogRecentArticlesFull.category_title}">{$widgetBlogRecentArticlesFull.category_title}</a>
		    				</div>
		    			</div>
		    		</header>
		    		<div class="bd content" itemprop="articleBody">
		    			{option:widgetBlogRecentArticlesFull.image}<img itemprop="image" class="img-polaroid span4 pull-right" src="{$FRONTEND_FILES_URL}/blog/images/source/{$widgetBlogRecentArticlesFull.image}" alt="{$widgetBlogRecentArticlesFull.title}" />{/option:widgetBlogRecentArticlesFull.image}
		    		    {option:!widgetBlogRecentArticlesFull.introduction}{$widgetBlogRecentArticlesFull.text}{/option:!widgetBlogRecentArticlesFull.introduction}
		    		    {option:widgetBlogRecentArticlesFull.introduction}{$widgetBlogRecentArticlesFull.introduction}{/option:widgetBlogRecentArticlesFull.introduction}
		    		</div>
		    	</article>
		    {/iteration:widgetBlogRecentArticlesFull}
		<footer>
		    <p class="btn-group">
		    	<a class="btn" href="{$var|geturlforblock:'blog'}">{$lblBlogArchive|ucfirst}</a>
		    	<a class="btn" href="{$widgetBlogRecentArticlesFullRssLink}">{$lblSubscribeToTheRSSFeed|ucfirst}</a>
		    </p>
		</footer>
	</section>
{/option:widgetBlogRecentArticlesFull}