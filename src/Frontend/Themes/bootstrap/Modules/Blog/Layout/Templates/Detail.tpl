{*
variables that are available:
- {$item}: contains data about the post
- {$comments}: contains an array with the comments for the post, each element contains data about the comment.
- {$commentsCount}: contains a variable with the number of comments for this blog post.
- {$navigation}: contains an array with data for previous and next post
*}
<div id="blogDetail" class="blog">
    <article class="article" itemscope itemtype="http://schema.org/Blog" role="main">
        <meta itemprop="interactionCount" content="UserComments:{$commentsCount}">
        <meta itemprop="author" content="{$item.user_id|usersetting:'nickname'}">
        <header role="banner">
            <div class="title">
                <div class="row">
                    <div class="col-md-10">
                        <h1 itemprop="name">{$item.title}</h1>
                    </div>
                    {option:item.allow_comments}
                    <div class="col-md-2 commentCount">
                        {option:comments}
                        <i class="icon-comment"></i>
                        {option:blogCommentsMultiple}<a href="{$item.full_url}#{$actComments}" itemprop="discussionUrl">{$msgBlogNumberOfComments|sprintf:{$commentsCount}}</a>{/option:blogCommentsMultiple}
                        {option:!blogCommentsMultiple}<a href="{$item.full_url}#{$actComments}" itemprop="discussionUrl">{$msgBlogOneComment}</a>{/option:!blogCommentsMultiple}
                        {/option:comments}
                    </div>
                    {/option:item.allow_comments}
                </div>
            </div>
            <div class="row muted meta" role="contentinfo">
                <div class="col-md-6">
                    <span class="hideText">{$msgWrittenBy|ucfirst|sprintf:''} </span>{$item.user_id|usersetting:'nickname'}
                    <span class="hideText">{$lblOn}</span>
                    <time itemprop="datePublished" datetime="{$item.publish_on|date:'Y-m-d\TH:i:s'}">{$item.publish_on|date:{$dateFormatLong}:{$LANGUAGE}}</time>
                </div>

                <div class="col-md-6 metaExtra" role="contentinfo">
                    <span class="hideText">{$lblInThe} </span>{$lblCategory|ucfirst}:
                    <a itemprop="articleSection" href="{$item.category_full_url}">{$item.category_title}</a>{option:!item.tags}.{/option:!item.tags}
                    {option:item.tags}
                    <span class="hideText">{$lblWithThe}</span> {$lblTags|ucfirst}:
		    		    <span itemprop="keywords">
		    		    	{iteration:item.tags}
		    		    		<a class="label" href="{$item.tags.full_url}" rel="tag">{$item.tags.name}</a>{option:!item.tags.last}<span class="hideText">,</span> {/option:!item.tags.last}
		    		    	{/iteration:item.tags}
		    		    </span>
                    {/option:item.tags}
                </div>
            </div>
        </header>

        <div class="articleBody" itemprop="articleBody">
            {option:item.image}<img class="img-polaroid col-md-4 img-responsive pull-right" src="{$FRONTEND_FILES_URL}/blog/images/source/{$item.image}" alt="{$item.title}" itemprop="image" />{/option:item.image}
            {$item.text}
        </div>
        <footer role="contentinfo">
            <div class="row social">
                <div class="col-xs-12 well">
                    <div class="shareButton">
                        {$lblShare|ucfirst}:
                    </div>
                    <div class="facebook shareButton">
                        <div class="fb-like" data-send="false" data-layout="button_count" data-width="90" data-show-faces="false"></div>
                    </div>
                    <div class="twitter shareButton">
                        <a href="https://twitter.com/share" class="twitter-share-button">Tweet</a>
                    </div>
                </div>
            </div>
            <nav>
                <ul class="pager" role="navigation">
                    {option:navigation.previous}
                    <li class="previous">
                        <a href="{$navigation.previous.url}" rel="prev" title="{$navigation.previous.title}">&larr;
                            <span class="hideText">{$lblPreviousArticle|ucfirst}: </span><span class="title">{$navigation.previous.title}</span></a>
                    </li>
                    {/option:navigation.previous}
                    {option:navigation.next}
                    <li class="next">
                        <a href="{$navigation.next.url}" rel="next" title="{$navigation.next.title}"><span class="hideText">{$lblNextArticle|ucfirst}: </span><span class="title">{$navigation.next.title}</span> &rarr;
                        </a>
                    </li>
                    {/option:navigation.next}
                </ul>
            </nav>
        </footer>
    </article>

    <section id="{$actComments}" class="comments">
        <header role="banner">
            <h3>{$lblComments|ucfirst}</h3>
        </header>
        {option:!comments}
        <div class="alert" role="alert">{$msgBlogNoComments}</div>
        {/option:!comments}
        {option:comments}
        {iteration:comments}
        {* Do not alter the id! It is used as an anchor *}
        <div id="comment-{$comments.id}" class="comment row {option:comments.last}lastChild{/option:comments.last}" itemprop="comment" itemscope itemtype="http://schema.org/UserComments">
            <div class="col-sm-1 avatar">
                <meta itemprop="discusses" content="{$item.title}" />
                {option:comments.website}<a href="{$comments.website}">{/option:comments.website}
                <img src="{$FRONTEND_CORE_URL}/layout/images/default_author_avatar.gif" width="48" height="48" alt="{$comments.author}" class="replaceWithGravatar img-circle" data-gravatar-id="{$comments.gravatar_id}" />
                {option:comments.website}</a>{/option:comments.website}
            </div>
            <div class="col-sm-7">
                <div class="meta" itemscope itemtype="http://schema.org/Person">
                    {option:comments.website}<a href="{$comments.website}" itemprop="url">{/option:comments.website}
                    <span itemprop="creator name">{$comments.author}</span>{option:comments.website}</a>{/option:comments.website}
                    <span class="hideText">{$lblWrote}</span>
                    <time class="muted" itemprop="commentTime" datetime="{$comments.created_on|date:'Y-m-d\TH:i:s'}">{$comments.created_on|timeago}</time>
                </div>
                <div class="commentText content" itemprop="commentText">
                    {$comments.text|cleanupplaintext}
                </div>
            </div>
        </div>
        {/iteration:comments}
        {/option:comments}
    </section>

    {option:item.allow_comments}
    <section id="{$actComment}" class="commentForm">
        <header role="banner">
            <h3>{$msgComment|ucfirst}</h3>
        </header>
        <div class="well">
            {option:commentIsInModeration}
            <div class="alert" role="alert">{$msgBlogCommentInModeration}</div>
            {/option:commentIsInModeration}
            {option:commentIsSpam}
            <div class="alert alert-danger" role="alert">{$msgBlogCommentIsSpam}</div>
            {/option:commentIsSpam}
            {option:commentIsAdded}
            <div class="alert alert-success" role="alert">{$msgBlogCommentIsAdded}</div>
            {/option:commentIsAdded}
            {form:commentsForm}
            <div class="row">
                <div class="col-sm-7">
                    <div class="control-group {option:txtMessageError}error{/option:txtMessageError}">
                        <label class="control-label" for="message">{$lblMessage|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>

                        <div class="controls">
                            {$txtMessage} {$txtMessageError}
                        </div>
                    </div>
                </div>
                <div class="col-sm-5 authorInfo">
                    <div class="control-group {option:txtAuthorError}error{/option:txtAuthorError}">
                        <label class="control-label" for="author">{$lblName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>

                        <div class="controls">
                            {$txtAuthor} {$txtAuthorError}
                        </div>
                    </div>
                    <div class="control-group {option:txtEmailError}error{/option:txtEmailError}">
                        <label class="control-label" for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>

                        <div class="controls">
                            {$txtEmail} {$txtEmailError}
                        </div>
                    </div>
                    <div class="control-group {option:txtWebsiteError}error{/option:txtWebsiteError}">
                        <label class="control-label" for="website">{$lblWebsite|ucfirst}</label>

                        <div class="controls">
                            {$txtWebsite} {$txtWebsiteError}
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <input class="btn-primary btn" type="submit" name="comment" value="{$msgComment|ucfirst}" />
            </div>
            {/form:commentsForm}
        </div>
    </section>
    {/option:item.allow_comments}
</div>
