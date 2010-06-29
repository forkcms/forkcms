<div id="seoMeta" class="subtleBox">
	<div class="heading">
		<h3>{$lblMetaInformation|ucfirst}</h3>
	</div>
	<div class="options">
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
		<div class="textareaHolder">
			<label for="metaCustom">{$lblExtraMetaTags|ucfirst}</label>
			<span class="helpTxt">{$msgHelpMetaCustom}</span>
			{$txtMetaCustom} {$txtMetaCustomError}
		</div>
	</div>
</div>

<div class="subtleBox">
	<div class="heading">
		<h3>{$lblURL|uppercase}</h3>
	</div>
	<div class="options">
		<label for="urlOverwrite">{$lblCustomURL|ucfirst}</label>
		<span class="helpTxt">{$msgHelpMetaURL}</span>
		<ul class="inputList checkboxTextFieldCombo">
			<li>
				{$chkUrlOverwrite}
				<span id="urlFirstPart">{$detailURL}/</span>{$txtUrl} {$txtUrlError}
			</li>
		</ul>
	</div>
</div>