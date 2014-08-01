{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
	<h2>{$lblAddNewMailing|ucfirst}</h2>
</div>

<div class="wizard">
	<ul>
		{iteration:wizard}
			<li{option:wizard.selected} class="selected"{/option:wizard.selected}{option:wizard.beforeSelected} class="beforeSelected"{/option:wizard.beforeSelected}>{option:wizard.stepLink}<a href="{$var|geturl:'edit'}&amp;id={$mailing.id}&amp;step={$wizard.id}">{/option:wizard.stepLink}<b><span>{$wizard.id}.</span> {$wizard.label|ucfirst}</b>{option:wizard.stepLink}</a>{/option:wizard.stepLink}</li>
		{/iteration:wizard}
	</ul>
</div>

{option:step1}
	{form:step1}
		<div class="box">
			<div class="heading ">
				<h3>{$lblSettings|ucfirst}</h3>
			</div>
			<div class="horizontal">
				<div class="options">
					<p>
						<label for="name">{$lblName|ucfirst} <abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtName} {$txtNameError}
						<span class="helpTxt">{$msgNameInternalUseOnly}</span>
					</p>
				</div>
				{option:ddmCampaign}
				<div class="options">
					<p>
						<label for="campaign">{$lblCampaign|ucfirst} <abbr title="{$lblRequiredField}">*</abbr></label>
						{$ddmCampaign} {$ddmCampaignError}
					</p>
				</div>
				{/option:ddmCampaign}
			</div>
		</div>

		<div class="box">
			<div class="heading ">
				<h3>{$lblSender|ucfirst}</h3>
			</div>
			<div class="horizontal">
				<div class="options">
					<p>
						<label for="fromName">{$lblName|ucfirst} <abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtFromName} {$txtFromNameError}
					</p>
				</div>

				<div class="options">
					<p>
						<label for="fromEmail">{$lblEmailAddress|ucfirst} <abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtFromEmail} {$txtFromEmailError}
					</p>
				</div>
			</div>
		</div>

		<div class="box">
			<div class="heading ">
				<h3>{$lblReplyTo|ucfirst}</h3>
			</div>
			<div class="horizontal">
				<div class="options">
					<p>
						<label for="replyToEmail">{$lblEmailAddress|ucfirst} <abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtReplyToEmail} {$txtReplyToEmailError}
					</p>
				</div>
			</div>
		</div>

		<div class="box">
			<div class="heading ">
				<h3>{$lblRecipients|ucfirst} <abbr title="{$lblRequiredField}">*</abbr></h3>
			</div>
			<div class="options">
				<ul class="inputList">
					{iteration:groups}
					<li>
						{$groups.chkGroups} <label for="{$groups.id}">{$groups.label|ucfirst} ({option:groups.recipients}{$groups.recipients}{/option:groups.recipients}{option:!groups.recipients}{$lblQuantityNo}{/option:!groups.recipients} {option:groups.single}{$lblEmailAddress}{/option:groups.single}{option:!groups.single}{$lblEmailAddresses}{/option:!groups.single})</label>
					</li>
					{/iteration:groups}
				</ul>
				{option:chkGroupsError}<p class="error">{$chkGroupsError}</p>{/option:chkGroupsError}
			</div>
		</div>

		<div class="box">
			<div class="heading ">
				<h3>{$msgTemplateLanguage|ucfirst} <abbr title="{$lblRequiredField}">*</abbr></h3>
			</div>
			<div class="options">
				<ul class="inputList">
					{iteration:languages}
						<li>{$languages.rbtLanguages} <label for="{$languages.id}">{$languages.label|ucfirst}</label></li>
					{/iteration:languages}
				</ul>
				{option:rbtLanguagesError}<p class="error">{$rbtLanguagesError}</p>{/option:rbtLanguagesError}
			</div>
		</div>

		<div class="fullwidthOptions">
			<div class="buttonHolderRight">
				<input id="toStep2" class="inputButton button mainButton" type="submit" name="to_step_2" value="{$lblToStep|ucfirst} 2" />
			</div>
		</div>
	{/form:step1}
{/option:step1}

{option:step2}
	{form:step2}
		<div class="box">
			<div class="heading ">
				<h3>{$lblChooseTemplate|ucfirst} <abbr title="{$lblRequiredField}">*</abbr></h3>
			</div>
			<div class="options">
				<ul id="templateSelection" class="selectThumbList clearfix">
					{iteration:templates}
						<li{option:templates.selected} class="selected"{/option:templates.selected}>
							{$templates.rbtTemplates}
							<label for="{$templates.id}">
								<img src="/src/Backend/Modules/Mailmotor/Templates/{$templates.language}/{$templates.value}/images/thumb.jpg" width="172" height="129" alt="{$templates.label|ucfirst}" />
								<span>{$templates.label|ucfirst}</span>
							</label>
						</li>
					{/iteration:templates}
				</ul>
				{option:rbtTemplatesError}<p class="error">{$rbtTemplatesError}</p>{/option:rbtTemplatesError}
			</div>
		</div>

		<div class="fullwidthOptions">
			<div class="buttonHolderRight">
				<input id="toStep3" class="inputButton button mainButton" type="submit" name="to_step_3" value="{$lblToStep|ucfirst} 3" />
			</div>
		</div>
	{/form:step2}
{/option:step2}

{option:step3}
	{form:step3}
		<div class="box">
			<div class="heading ">
				<h3>{$lblSubject|ucfirst} <abbr title="{$lblRequiredField}">*</abbr></h3>
			</div>
			<div class="options bigInput">
				{$txtSubject} {$txtSubjectError}
			</div>
		</div>

		<div class="box">
			<div class="heading ">
				<h3>{$lblContent|ucfirst} <abbr title="{$lblRequiredField}">*</abbr></h3>
			</div>
			<div id="iframeBox">
				<iframe id="contentBox" src="{$var|geturl:'edit_mailing_iframe'}&amp;id={$mailing.id}" height="100%" width="100%" style="border-right: 1px solid rgb(221, 221, 221); border-width: medium 1px 1px; border-style: none solid solid; border-color: -moz-use-text-color rgb(221, 221, 221) rgb(221, 221, 221); -moz-box-sizing: border-box;"></iframe>
			</div>
		</div>

		{option:txtContentPlain}
		<div class="box">
			<div class="heading ">
				<h3>{$lblPlainTextVersion|ucfirst}</h3>
			</div>
			<div class="content">
				{$txtContentPlain} {$txtContentPlainError}
			</div>
		</div>
		{/option:txtContentPlain}

		<div class="fullwidthOptions">
			<div class="buttonHolderRight">
				<input id="sendContent" class="inputButton button mainButton" type="submit" name="to_step_4" value="{$lblToStep|ucfirst} 4" />
			</div>
		</div>
	{/form:step3}
{/option:step3}

{option:step4}
	{form:step4}
		<div class="box">
			<div class="heading">
				<h3>{$lblPreview|ucfirst}</h3>
			</div>
			{option:previewURL}
			<div id="iframeBox">
				<iframe id="contentBox" src="{$previewURL}" height="100%" width="100%" style="border-right: 1px solid rgb(221, 221, 221); border-width: medium 1px 1px; border-style: none solid solid; border-color: -moz-use-text-color rgb(221, 221, 221) rgb(221, 221, 221); -moz-box-sizing: border-box;"></iframe>
			</div>
			{/option:previewURL}
			{option:!previewURL}
			<div class="options">
				<p><span class="infoMessage">{$errNoModuleLinked}</span></p>
			</div>
			{/option:!previewURL}
		</div>

		<div class="box oneLiner">
			<div class="heading ">
				<h3>{$lblSendPreview|ucfirst}</h3>
			</div>
			<div class="options clearfix">
				<p class="oneLineWrapper">
					<label for="email">{$lblEmailAddress|ucfirst}</label>
					{$txtEmail} {$txtEmailError}
				</p>
				<p>
					<input id="sendPreview" class="inputButton button sendPreview" type="submit" name="send_preview" value="{$lblSendPreview|ucfirst}" />
				</p>
			</div>
		</div>

		<div class="box">
			<div class="heading ">
				<h3>{$lblSendOn|ucfirst}</h3>
			</div>
			<div class="options oneLiner">
				<p class="oneLineWrapper">
					<label for="sendOnDate">{$lblSendDate|ucfirst}</label>
					{$txtSendOnDate}
				</p>
				<p class="oneLineWrapper">
					<label for="sendOnTime">{$lblAt}</label>
					{$txtSendOnTime}
				</p>
			</div>
		</div>

		<div class="fullwidthOptions">
			<div class="buttonHolderRight">
				<a id="sendMailing" href="#" class="button mainButton" title="{$lblSendMailing|ucfirst}">
					<span>{$lblSendMailing|ucfirst}</span>
				</a>
			</div>
		</div>

		<div id="sendMailingConfirmationModal" title="{$msgMailingConfirmTitle}">
			<div class="ui-tabs">
				<div class="ui-tabs-panel">
					<div class="subtleBox">
						<div class="heading ">
							<h3>{$msgMailingConfirmSend}</h3>
						</div>
						<div class="options">
							<p>{$recipientStatistics} <span id="sendOn" style="display: none;">{$msgSendOn}</span></p>
							<p>{$msgPeopleGroups}</p>
							<p>
								{iteration:groups}{$groups.name} {option:groups.comma}, {/option:groups.comma}{/iteration:groups}
							</p>
						</div>
					</div>
					<div class="subtleBox">
						<div class="heading ">
							<h3>{$lblTemplateLanguage|ucfirst}</h3>
						</div>
						<div class="options">
							<p>{$templateLanguage}</p>
						</div>
					</div>
					<div class="subtleBox">
						<div class="heading ">
							<h3>{$lblPrice|ucfirst}</h3>
						</div>
						<div class="options">
							<p>&euro; {$price}</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	{/form:step4}
{/option:step4}

<script type="text/javascript">
	//<![CDATA[
		var variables = [];
		variables = { mailingId: '{$mailing.id}' };
	//]]>
</script>

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
