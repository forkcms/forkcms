<div id="seoMeta" class="subtleBox">
	<div class="heading">
		<h3>{$lblMetaInformation|ucfirst}</h3>
	</div>
	<div class="options">
		<p>
			<label for="pageTitleOverwrite">{$lblPageTitle|ucfirst}</label>
			<span class="helpTxt">{$msgHelpPageTitle}</span>
		</p>
		<ul class="inputList checkboxTextFieldCombo">
			<li>
				{$chkPageTitleOverwrite}
				<label for="pageTitle" class="visuallyHidden">{$lblPageTitle|ucfirst}</label>
				{$txtPageTitle} {$txtPageTitleError}
			</li>
		</ul>
		<p>
			<label for="metaDescriptionOverwrite">{$lblDescription|ucfirst}</label>
			<span class="helpTxt">{$msgHelpMetaDescription}</span>
		</p>
		<ul class="inputList checkboxTextFieldCombo">
			<li>
				{$chkMetaDescriptionOverwrite}
				<label for="metaDescription" class="visuallyHidden">{$lblDescription|ucfirst}</label>
				{$txtMetaDescription} {$txtMetaDescriptionError}
			</li>
		</ul>
		<p>
			<label for="metaKeywordsOverwrite">{$lblKeywords|ucfirst}</label>
			<span class="helpTxt">{$msgHelpMetaKeywords}</span>
		</p>
		<ul class="inputList checkboxTextFieldCombo">
			<li>
				{$chkMetaKeywordsOverwrite}
				<label for="metaKeywords" class="visuallyHidden">{$lblKeywords|ucfirst}</label>
				{$txtMetaKeywords} {$txtMetaKeywordsError}
			</li>
		</ul>
		{option:txtMetaCustom}
			<div class="textareaHolder">
				<p>
					<label for="metaCustom">{$lblExtraMetaTags|ucfirst}</label>
					<span class="helpTxt">{$msgHelpMetaCustom}</span>
				</p>
				{$txtMetaCustom} {$txtMetaCustomError}
			</div>
		{/option:txtMetaCustom}
	</div>
</div>

<div class="subtleBox">
	<div class="heading">
		<h3>{$lblURL|uppercase}</h3>
	</div>
	<div class="options">
		<p>
			<label for="urlOverwrite">{$lblCustomURL|ucfirst}</label>
			<span class="helpTxt">{$msgHelpMetaURL}</span>
		</p>
		<ul class="inputList checkboxTextFieldCombo">
			<li>
				{$chkUrlOverwrite}
				<label for="url" class="visuallyHidden">{$lblCustomURL|ucfirst}</label>
				{option:detailURL}<span id="urlFirstPart">{$detailURL}/</span>{/option:detailURL}{$txtUrl} {$txtUrlError}
			</li>
		</ul>
	</div>
</div>

<div class="subtleBox">
	<div class="heading">
		<h3>{$lblSEO|uppercase}</h3>
	</div>
	<div class="options">
		<p class="label">{$lblIndex}</p>
		{$rbtSeoIndexError}
		<ul class="inputList inputListHorizontal">
			{iteration:seo_index}
				<li><label for="{$seo_index.id}">{$seo_index.rbtSeoIndex} {$seo_index.label}</label></li>
			{/iteration:seo_index}
		</ul>
		<p class="label">{$lblFollow}</p>
		{$rbtSeoFollowError}
		<ul class="inputList inputListHorizontal">
			{iteration:seo_follow}
				<li><label for="{$seo_follow.id}">{$seo_follow.rbtSeoFollow} {$seo_follow.label}</label></li>
			{/iteration:seo_follow}
		</ul>
	</div>
</div>

{* Hidden settings, used for the Ajax-call to verify the url *}
{$hidMetaId}
{$hidBaseFieldName}
{$hidCustom}
{$hidClassName}
{$hidMethodName}
{$hidParameters}
