{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
	<div class="col-md-12">
		<h2>{$lblSettings|ucfirst}</h2>
	</div>
</div>
{option:Wizard}
<div class="row fork-module-messages">
	<div class="col-md-12">
		<div class="alert alert-warning" role="alert">
			<p><strong>{$msgConfigurationError}</strong></p>
			<ul>
				{option:NoSessionToken}
				<li>{$errNoSessionToken}</li>
				{/option:NoSessionToken}
				{option:NoTableId}
				<li>{$errNoTableId}</li>
				{/option:NoTableId}
			</ul>
		</div>
	</div>
</div>
{/option:Wizard}
<div class="row fork-module-content">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					{$lblGoogleAnalyticsLink|ucfirst}
				</h3>
			</div>
			{option:Wizard}
			{option:NoSessionToken}
			{form:apiKey}
				<div class="panel-body">
					<p>{$msgLinkGoogleAccount}</p>
					<div class="form-group">
						<label for="key">{$lblApiKey|ucfirst}</label>
						{$txtKey} {$txtKeyError}
					</div>
				</div>
				<div class="panel-footer">
					<div class="btn-toolbar">
						<div class="btn-group pull-right">
							<button id="submitForm" class="btn btn-primary" type="submit" name="submitForm">
								<span class="glyphicon glyphicon-pencil"></span>&nbsp;
								{$msgAuthenticateAtGoogle|ucfirst}
							</button>
						</div>
					</div>
				</div>
			{/form:apiKey}
			{/option:NoSessionToken}
			{option:NoTableId}
			{option:accounts}
			{form:linkProfile}
				<div class="panel-body">
					<p>{$msgLinkWebsiteProfile}</p>
					<div class="form-group">
						{option:tableIdError}
						<p class="text-danger">{$tableIdError}</p>
						{/option:tableIdError}
						{$ddmTableId}
					</div>
				</div>
				<div class="panel-footer">
					<div class="btn-toolbar">
						<div class="btn-group pull-left">
							<button type="button" data-toggle="modal" data-target="#confirmDeleteSessionToken" class="btn btn-danger">
								<span class="glyphicon glyphicon-log-out"></span>&nbsp;
								{$msgRemoveAccountLink}
							</button>
						</div>
						<div class="btn-group pull-right">
							<button id="submitForm" class="btn btn-primary" type="submit" name="submitForm">
								<span class="glyphicon glyphicon-log-in"></span>&nbsp;
								{$lblLinkThisProfile|ucfirst}
							</button>
						</div>
					</div>
				</div>
			{/form:linkProfile}
			{/option:accounts}
			{option:!accounts}
			<div class="panel-body">
				<p>{$msgNoAccounts}</p>
			</div>
			{/option:!accounts}
			{/option:NoTableId}
			{/option:Wizard}
			{option:EverythingIsPresent}
			<div class="panel-body">
				<p>
					{$lblLinkedAccount|ucfirst}: <strong>{$accountName}</strong><br />
					{$lblLinkedProfile|ucfirst}: <strong>{$profileTitle}</strong>
				</p>
			</div>
			<div class="panel-footer">
				<div class="btn-toolbar">
					<div class="btn-group pull-left">
						<button type="button" data-toggle="modal" data-target="#confirmDeleteTableId" class="btn btn-danger">
							<span class="glyphicon glyphicon-trash"></span>&nbsp;
							{$msgRemoveProfileLink}
						</button>
					</div>
					<div class="btn-group pull-right">
						{option:showAnalyticsIndex}
						<a href="{$var|geturl:'index'}" class="btn btn-default">
							<span class="glyphicon glyphicon-search"></span>&nbsp;
							{$lblViewStatistics|ucfirst}
						</a>
						{/option:showAnalyticsIndex}
					</div>
				</div>
			</div>
			{/option:EverythingIsPresent}
		</div>
		<div class="modal fade" id="confirmDeleteSessionToken" tabindex="-1" role="dialog" aria-labelledby="{$lblDelete|ucfirst}" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<span class="modal-title h4">{$lblDelete|ucfirst}</span>
					</div>
					<div class="modal-body">
						<p>{$msgConfirmDeleteLinkGoogleAccount}</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
						<a href="{$var|geturl:'settings'}&amp;remove=session_token" class="btn btn-primary">
							{$lblOK|ucfirst}
						</a>
					</div>
				</div>
			</div>
		</div>
		<div class="modal fade" id="confirmDeleteTableId" tabindex="-1" role="dialog" aria-labelledby="{$lblDelete|ucfirst}" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<span class="modal-title h4">{$lblDelete|ucfirst}</span>
					</div>
					<div class="modal-body">
						<p>{$msgConfirmDeleteLinkAccount|sprintf:{$accountName}}</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
						<a href="{$var|geturl:'settings'}&amp;remove=table_id" class="btn btn-primary">
							{$lblOK|ucfirst}
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
{option:EverythingIsPresent}
{form:trackingType}
	<div class="row fork-module-content">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">
						{$lblTrackingType|ucfirst}
					</h3>
				</div>
				<div class="panel-body">
					<p class="text-info">{$msgHelpTrackingType}</p>
					{option:rbtTypeError}
					<p class="text-danger">{$rbtTypeError}</p>
					{/option:rbtTypeError}
					<div class="form-group">
						<ul class="list-unstyled">
							{iteration:type}
							<li class="radio">
								<label for="{$type.id}">{$type.rbtType} {$type.label}</label>
							</li>
							{/iteration:type}
						</ul>
					</div>
				</div>
				<div class="panel-footer">
					<div class="btn-toolbar">
						<div class="btn-group pull-right">
							<button id="save" class="btn btn-primary" type="submit" name="save">
								<span class="glyphicon glyphicon-pencil"></span>&nbsp;
								{$lblSave|ucfirst}
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
{/form:trackingType}
{/option:EverythingIsPresent}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
