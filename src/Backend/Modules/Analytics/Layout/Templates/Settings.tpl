{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}


<div class="pageTitle">
	<h2>{$lblModuleSettings|ucfirst}: {$lblAnalytics|ucfirst}</h2>
</div>

{option:Wizard}
	<div class="generalMessage infoMessage content">
		<p><strong>{$msgConfigurationError}</strong></p>
		<ul class="pb0">
			{option:NoSessionToken}<li>{$errNoSessionToken}</li>{/option:NoSessionToken}
			{option:NoTableId}<li>{$errNoTableId}</li>{/option:NoTableId}
		</ul>
	</div>
{/option:Wizard}

<div class="box">
	<div class="heading">
		<h3>{$lblGoogleAnalyticsLink|ucfirst}</h3>
	</div>

	<div class="options">
		{option:Wizard}
			{option:NoSessionToken}
				{form:apiKey}
					<p>{$msgLinkGoogleAccount}</p>

					<div class="inputList">
						<label for="key">{$lblApiKey|ucfirst}</label>
						{$txtKey} {$txtKeyError}
					</div>

					<div class="buttonHolder">
						<input id="submitForm" class="inputButton button mainButton" type="submit" name="submitForm" value="{$msgAuthenticateAtGoogle}" />
					</div>

				{/form:apiKey}
			{/option:NoSessionToken}

			{option:NoTableId}
				{option:accounts}
					<p>{$msgLinkWebsiteProfile}</p>
					{form:linkProfile}
					<div class="oneLiner fakeP">
						<p>
							{$ddmTableId} {option:tableIdError}<br /><span class="formerror">{$tableIdError}</span>{/option:tableIdError}
						</p>
						<div class="buttonHolder">
							<input id="submitForm" class="inputButton button mainButton" type="submit" name="submitForm" value="{$lblLinkThisProfile|ucfirst}" />
						</div>
					</div>
					{/form:linkProfile}
				{/option:accounts}

				{option:!accounts}
					<p>{$msgNoAccounts}</p>
				{/option:!accounts}

				<div class="buttonHolder">
					<a href="{$var|geturl:'settings'}&amp;remove=session_token" data-message-id="confirmDeleteSessionToken" class="askConfirmation submitButton button inputButton"><span>{$msgRemoveAccountLink}</span></a>
				</div>
			{/option:NoTableId}
		{/option:Wizard}

		{option:EverythingIsPresent}
			<p>
				{$lblLinkedAccount|ucfirst}: <strong>{$accountName}</strong><br />
				{$lblLinkedProfile|ucfirst}: <strong>{$profileTitle}</strong>
			</p>
			<div class="buttonHolder">
				<a href="{$var|geturl:'settings'}&amp;remove=table_id" data-message-id="confirmDeleteTableId" class="askConfirmation submitButton button inputButton"><span>{$msgRemoveProfileLink}</span></a>
				{option:showAnalyticsIndex}<a href="{$var|geturl:'index'}" class="mainButton button"><span>{$lblViewStatistics|ucfirst}</span></a>{/option:showAnalyticsIndex}
			</div>
		{/option:EverythingIsPresent}

		<div id="confirmDeleteSessionToken" title="{$lblDelete|ucfirst}?" style="display: none;">
			<p>
				{$msgConfirmDeleteLinkGoogleAccount}
			</p>
		</div>

		<div id="confirmDeleteTableId" title="{$lblDelete|ucfirst}?" style="display: none;">
			<p>
				{$msgConfirmDeleteLinkAccount|sprintf:{$accountName}}
			</p>
		</div>
	</div>
</div>

{option:EverythingIsPresent}
	{form:trackingType}
		<div class="box">
			<div class="heading">
				<h3>{$lblTrackingType|ucfirst}</h3>
			</div>

			<div class="options">
				<p>{$msgHelpTrackingType}</p>
				{iteration:type}
					<label for="{$type.id}">{$type.rbtType} {$type.label}</label><br />
				{/iteration:type}
				{$rbtTypeError}
			</div>
		</div>

		<div class="fullwidthOptions">
			<div class="buttonHolderRight">
				<input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
			</div>
		</div>
	{/form:trackingType}
{/option:EverythingIsPresent}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
