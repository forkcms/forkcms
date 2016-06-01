{*
variables that are available:
- {$category}: contains data about the category
- {$items}: contains an array with all posts, each element contains data about the post
*}

{option:items}
<section id="blogCategory" class="blog">
    {iteration:items}
    <article class="article" itemscope itemtype="http://schema.org/Blog" role="main">
        <meta itemprop="interactionCount" content="UserComments:{$items.comment_count}">
        <meta itemprop="author" content="{$items.user_id|usersetting:'nickname'}">
        <header role="banner">
            <div class="title">
                <div class="row">
                    <div class="col-md-10">
                        <h2><a href="{$items.full_url}" title="{$items.title}">{$items.title}</a></h2>
                    </div>
                    {option:items.allow_comments}
                    <div class="col-md-2 commentCount">
                        {option:items.comments}
                        <i class="icon-comment"></i>
                        {option:items.comments_multiple}<a href="{$items.full_url}#{$actComments}">{$msgBlogNumberOfComments|sprintf:{$items.comments_count}}</a>{/option:items.comments_multiple}
                        {option:!items.comments_multiple}<a href="{$items.full_url}#{$actComments}">{$msgBlogOneComment}</a>{/option:!items.comments_multiple}
                        {/option:items.comments}
                    </div>
                    {/option:items.allow_comments}
                </div>
            </div>
            <div class="row muted meta">
                <div class="col-md-6">
                    <span class="hideText">{$msgWrittenBy|ucfirst|sprintf=''} </span>{$items.user_id|usersetting:'nickname'}
                    <span class="hideText">{$lblOn}</span>
                    <time itemprop="datePublished" datetime="{$items.publish_on|date:'Y-m-d\TH:i:s'}">{$items.publish_on|date:{$dateFormatLong}:{$LANGUAGE}}</time>
                </div>
                <div class="col-md-6 metaExtra">
                    <span class="hideText">{$lblInThe} </span>{$lblCategory|ucfirst}:
                    <a itemprop="articleSection" href="{$items.category_full_url}">{$items.category_title}</a>{option:!items.tags}.{/option:!items.tags}
                    {option:items.tags}
                    <span class="hideText">{$lblWithThe}</span> {$lblTags|ucfirst}:
								<span itemprop="keywords">
		    		    			{iteration:items.tags}
		    		    				<a class="label" href="{$item.tags.full_url}" rel="tag">{$items.tags.name}</a>{option:!items.tags.last}<span class="hideText">,</span> {/option:!items.tags.last}
		    		    			{/iteration:items.tags}
		    		    		</span>
                    {/option:items.tags}
                </div>
            </div>
        </header>
        <div class="articleBody" itemprop="articleBody">
            {option:items.image}<img itemprop="image" class="img-polaroid col-md-4 img-responsive pull-right" src="{$FRONTEND_FILES_URL}/blog/images/source/{$items.image}" alt="{$items.title}" />{/option:items.image}
            {option:!items.introduction}{$items.text}{/option:!items.introduction}
            {option:items.introduction}{$items.introduction}{/option:items.introduction}
        </div>
    </article>
    {/iteration:items}
</section>
{include:Core/Layout/Templates/Pagination.tpl}
{/option:items}
