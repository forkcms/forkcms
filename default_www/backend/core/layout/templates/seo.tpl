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
				{option:detailURL}<span id="urlFirstPart">{$detailURL}/</span>{/option:detailURL}{$txtUrl} {$txtUrlError}
			</li>
		</ul>
	</div>
</div>