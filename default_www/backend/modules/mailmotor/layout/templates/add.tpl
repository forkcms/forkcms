{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblAddNewMailing|ucfirst}</h2>
</div>

<div class="wizard">
<ul>
	<li class="selected firstChild"><a href="{$var|geturl:'add'}"><b><span>1.</span> {$lblWizardInformation|ucfirst}</b></a></li>
	<li><b><span>2.</span> {$lblWizardTemplate|ucfirst}</b></li>
	<li><b><span>3.</span> {$lblWizardContent|ucfirst}</b></li>
	<li><b><span>4.</span> {$lblWizardSend|ucfirst}</b></li>
</ul>
</div>

{form:add}
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
		<h3>{$lblRecipients|ucfirst}</h3>
	</div>
	<div class="options">
		<ul class="inputList">
			{iteration:groups}<li>{$groups.chkGroups} <label for="{$groups.id}"><attr title="{$msgGroupsNumberOfRecipients|sprintf:{$groups.recipients}}">{$groups.label|ucfirst} ({option:groups.recipients}{$groups.recipients}{/option:groups.recipients}{option:!groups.recipients}{$lblQuantityNo}{/option:!groups.recipients} {option:groups.single}{$lblEmailAddress}{/option:groups.single}{option:!groups.single}{$lblEmailAddresses}{/option:!groups.single})</attr></label></li>{/iteration:groups}
		</ul>
		{option:chkGroupsError}<p class="error">{$chkGroupsError}</p>{/option:chkGroupsError}
	</div>
</div>

<div class="box">
	<div class="heading ">
		<h3>{$msgTemplateLanguage|ucfirst}</h3>
	</div>
	<div class="options">
		<ul class="inputList">
			{iteration:languages}<li>{$languages.rbtLanguages} <label for="{$languages.id}">{$languages.label|ucfirst}</label></li>{/iteration:languages}
		</ul>
		{option:rbtLanguagesError}<p class="error">{$rbtLanguagesError}</p>{/option:rbtLanguagesError}
	</div>
</div>

<div class="fullwidthOptions">
	<div class="buttonHolderRight">
		<input id="toStep2" class="inputButton button mainButton" type="submit" name="to_step_2" value="{$lblToStep|ucfirst} 2" />
	</div>
</div>
{/form:add}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}