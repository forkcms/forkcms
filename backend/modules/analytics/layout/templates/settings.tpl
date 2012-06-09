{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

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
				<p>{$msgLinkGoogleAccount}</p>
				<div class="buttonHolder">
					<a href="{$googleAccountAuthenticationForm}" class="submitButton button inputButton"><span>{$msgAuthenticateAtGoogle}</span></a>
				</div>
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

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}