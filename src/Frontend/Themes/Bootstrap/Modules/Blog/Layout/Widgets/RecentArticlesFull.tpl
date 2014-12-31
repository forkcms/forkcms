{*
	variables that are available:
	- {$widgetBlogRecentArticlesFull}: contains an array with all posts, each element contains data about the post
*}

{option:widgetBlogRecentArticlesFull}
	<section id="blogRecentArticlesFullWidget" class="blog">
		<header role="banner">
		    <h2>{$lblRecentArticles|ucfirst}</h2>
		</header>
		{iteration:widgetBlogRecentArticlesFull}
			<article class="article" itemscope itemtype="http://schema.org/Blog" role="main">
				<meta itemprop="interactionCount" content="UserComments:{$widgetBlogRecentArticlesFull.comments_count}">
				<meta itemprop="author" content="{$widgetBlogRecentArticlesFull.user_id|usersetting:'nickname'}">
				<header role="banner">
					<div class="row title">
						<div class="col-md-10">
							<h3><a href="{$widgetBlogRecentArticlesFull.full_url}" title="{$widgetBlogRecentArticlesFull.title}">{$widgetBlogRecentArticlesFull.title}</a></h3>
						</div>
						{option:widgetBlogRecentArticlesFull.allow_comments}
							<div class="col-md-2 commentCount">
								<i class="icon-comment"></i>
								{option:!widgetBlogRecentArticlesFull.comments}<a href="{$widgetBlogRecentArticlesFull.full_url}#{$actComment}">{$msgBlogNumberOfComments|sprintf:{$widgetBlogRecentArticlesFull.comments_count}}</a>{/option:!widgetBlogRecentArticlesFull.comments}
								{option:widgetBlogRecentArticlesFull.comments}
									{option:widgetBlogRecentArticlesFull.comments_multiple}<a href="{$widgetBlogRecentArticlesFull.full_url}#{$actComments}">{$msgBlogNumberOfComments|sprintf:{$widgetBlogRecentArticlesFull.comments_count}}</a>{/option:widgetBlogRecentArticlesFull.comments_multiple}
									{option:!widgetBlogRecentArticlesFull.comments_multiple}<a href="{$widgetBlogRecentArticlesFull.full_url}#{$actComments}">{$msgBlogOneComment}</a>{/option:!widgetBlogRecentArticlesFull.comments_multiple}
								{/option:widgetBlogRecentArticlesFull.comments}
							</div>
						{/option:widgetBlogRecentArticlesFull.allow_comments}
					</div>
					<div class="row muted meta">
						<div class="col-md-6">
							<span class="hideText">{$msgWrittenBy|ucfirst|sprintf:''} </span>{$widgetBlogRecentArticlesFull.user_id|usersetting:'nickname'}
							<span class="hideText">{$lblOn}</span> <time itemprop="datePublished" datetime="{$widgetBlogRecentArticlesFull.publish_on|date:'Y-m-d\TH:i:s'}">{$widgetBlogRecentArticlesFull.publish_on|date:{$dateFormatLong}:{$LANGUAGE}}</time>
						</div>
						<div class="col-md-6 metaExtra">
							<span class="hideText">{$lblInThe} </span>{$lblCategory|ucfirst}: <a href="{$widgetBlogRecentArticlesFull.category_full_url}" title="{$widgetBlogRecentArticlesFull.category_title}">{$widgetBlogRecentArticlesFull.category_title}</a>
						</div>
					</div>
				</header>
				<div class="bd content" itemprop="articleBody">
					{option:widgetBlogRecentArticlesFull.image}<img itemprop="image" class="img-polaroid col-md-4 img-responsive pull-right" src="{$FRONTEND_FILES_URL}/blog/images/source/{$widgetBlogRecentArticlesFull.image}" alt="{$widgetBlogRecentArticlesFull.title}" />{/option:widgetBlogRecentArticlesFull.image}
					{option:!widgetBlogRecentArticlesFull.introduction}{$widgetBlogRecentArticlesFull.text}{/option:!widgetBlogRecentArticlesFull.introduction}
					{option:widgetBlogRecentArticlesFull.introduction}{$widgetBlogRecentArticlesFull.introduction}{/option:widgetBlogRecentArticlesFull.introduction}
				</div>
			</article>
		{/iteration:widgetBlogRecentArticlesFull}
		<footer role="contentinfo">
		    <p class="btn-group">
		    	<a class="btn btn-default" href="{$var|geturlforblock:'blog'}">{$lblBlogArchive|ucfirst}</a>
		    	<a class="btn btn-default" href="{$widgetBlogRecentArticlesFullRssLink}">{$lblSubscribeToTheRSSFeed|ucfirst}</a>
		    </p>
		</footer>
	</section>
{/option:widgetBlogRecentArticlesFull}