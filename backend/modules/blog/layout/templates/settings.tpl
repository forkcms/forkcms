{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblModuleSettings|ucfirst}: {$lblBlog}</h2>
</div>

{form:settings}
	<div class="box">
		<div class="heading">
			<h3>{$lblPagination|ucfirst}</h3>
		</div>
		<div class="options">
			<label for="overviewNumberOfItems">{$lblItemsPerPage|ucfirst}</label>
			{$ddmOverviewNumberOfItems} {$ddmOverviewNumberOfItemsError}
		</div>
		<div class="options">
			<label for="recentArticlesFullNumberOfItems">{$msgNumItemsInRecentArticlesFull|ucfirst}</label>
			{$ddmRecentArticlesFullNumberOfItems} {$ddmRecentArticlesFullNumberOfItemsError}
		</div>
		<div class="options">
			<label for="recentArticlesListNumberOfItems">{$msgNumItemsInRecentArticlesList|ucfirst}</label>
			{$ddmRecentArticlesListNumberOfItems} {$ddmRecentArticlesListNumberOfItemsError}
		</div>
	</div>

	<div class="box">
		<div class="heading">
			<h3>{$lblComments|ucfirst}</h3>
		</div>
		<div class="options">
			<ul class="inputList">
				<li><label for="allowComments">{$chkAllowComments} {$lblAllowComments|ucfirst}</label></li>
				<li><label for="moderation">{$chkModeration} {$lblEnableModeration|ucfirst}</label></li>
				<li>
					<label for="spamfilter">{$chkSpamfilter} {$lblFilterCommentsForSpam|ucfirst}</label>
					<span class="helpTxt">
						{$msgHelpSpamFilter}
						{option:noAkismetKey}<span class="infoMessage"><br />{$msgNoAkismetKey|sprintf:{$var|geturl:'index':'settings'}}</span>{/option:noAkismetKey}
					</span>
				</li>
			</ul>
			<p class="p0">{$msgFollowAllCommentsInRSS|sprintf:{$commentsRSSURL}}</p>
		</div>
	</div>

	{option:isGod}
	<div class="box">
		<div class="heading">
			<h3>{$lblImage|ucfirst}</h3>
		</div>
		<div class="options">
			<label for="showImageForm">{$chkShowImageForm} {$msgShowImageForm}</label>
		</div>
	</div>
	{/option:isGod}

	<div class="box">
		<div class="heading">
			<h3>{$lblNotifications|ucfirst}</h3>
		</div>
		<div class="options">
			<ul class="inputList p0">
				<li><label for="notifyByEmailOnNewCommentToModerate">{$chkNotifyByEmailOnNewCommentToModerate} {$msgNotifyByEmailOnNewCommentToModerate|ucfirst}</label></li>
				<li><label for="notifyByEmailOnNewComment">{$chkNotifyByEmailOnNewComment} {$msgNotifyByEmailOnNewComment|ucfirst}</label></li>
			</ul>
		</div>
	</div>

	<div class="box">
		<div class="heading">
			<h3>{$lblSEO}</h3>
		</div>
		<div class="options">
			<p>{$msgHelpPingServices}:</p>
			<ul class="inputList p0">
				<li><label for="pingServices">{$chkPingServices} {$lblPingBlogServices|ucfirst}</label></li>
			</ul>
		</div>
	</div>

	<div class="box">
		<div class="horizontal">
			<div class="heading">
				<h3>{$lblRSSFeed}</h3>
			</div>
			<div class="options">
				<label for="rssTitle">{$lblTitle|ucfirst}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
				{$txtRssTitle} {$txtRssTitleError}
				<span class="helpTxt">{$msgHelpRSSTitle}</span>
			</div>
			<div class="options">
				<label for="rssDescription">{$lblDescription|ucfirst}</label>
				{$txtRssDescription} {$txtRssDescriptionError}
				<span class="helpTxt">{$msgHelpRSSDescription}</span>
			</div>
			<div class="options">
				<label for="feedburnerUrl">{$lblFeedburnerURL|ucfirst}</label>
				{$txtFeedburnerUrl} {$txtFeedburnerUrlError}
				<span class="helpTxt">{$msgHelpFeedburnerURL}</span>
			</div>
			<div class="options">
				<p>{$msgHelpMeta}:</p>
				<ul class="inputList p0">
					<li><label for="rssMeta">{$chkRssMeta} {$lblMetaInformation|ucfirst}</label></li>
				</ul>
			</div>
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:settings}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}