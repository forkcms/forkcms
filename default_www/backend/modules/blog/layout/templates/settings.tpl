{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

<div class="pageTitle">
	<h2>{$lblModuleSettings|ucfirst}: {$lblBlog|ucfirst}</h2>
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
			<label for="recentArticlesNumberOfItems">{$msgNumberOfItemsInRecentArticlesWidget|ucfirst}</label>
			{$ddmRecentArticlesNumberOfItems} {$ddmRecentArticlesNumberOfItemsError}
		</div>
	</div>

	<div class="box">
		<div class="heading">
			<h3>{$lblComments|ucfirst}</h3>
		</div>
		<div class="options">
			<ul class="inputList p0">
				<li>{$chkAllowComments} <label for="allowComments">{$lblAllowComments|ucfirst}</label></li>
				<li>{$chkModeration} <label for="moderation">{$lblEnableModeration|ucfirst}</label></li>
				<li>
					{$chkSpamfilter} <label for="spamfilter">{$lblFilterCommentsForSpam|ucfirst}</label>
					<span class="helpTxt">{$msgHelpSpamFilter}</span>
				</li>
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
				<li>{$chkPingServices}<label for="pingServices">{$lblPingBlogServices|ucfirst}</label></li>
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
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:settings}

{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}