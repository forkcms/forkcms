{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblEmailAddresses|ucfirst}</h2>
</div>

{form:edit}
	<div class="box">
		<div class="heading ">
			<h3>{$lblEditEmail|ucfirst}</h3>
		</div>
		<div class="horizontal">
			<div class="options">
				<p>
					<label for="email">{$lblEmailAddress|ucfirst}</label>
					{$txtEmail} {$txtEmailError}
				</p>
			</div>
		</div>
		<div class="horizontal">
			<div class="options">
				{option:groups}
					<ul class="inputList">
						{iteration:groups}
							<li>{$groups.chkGroups} <label for="{$groups.id}">{$groups.label|ucfirst}</label></li>
						{/iteration:groups}
					</ul>
				{/option:groups}
			</div>
		</div>
	</div>


	{option:ddmSubscriptions}
	<div class="box">
		<div class="heading ">
			<h3>{$lblCustomFields|ucfirst}</h3>
		</div>
		<div class="horizontal">
			<div class="options">
				<p>
					<label for="subscriptions">{$lblGroup|ucfirst}</label>
					{$ddmSubscriptions} {$ddmSubscriptionsError}
				</p>
			</div>

			{option:fields}
			<div class="options">
				{iteration:fields}
					<p>
						<label for="{$fields.name}">[{$fields.label}]</label>
						{$fields.txtField} {$fields.txtFieldError}
					</p>
				{/iteration:fields}
			</div>
			{/option:fields}
		</div>
	</div>
	{/option:ddmSubscriptions}

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblEdit|ucfirst}" />
		</div>
	</div>
{/form:edit}

<script type="text/javascript">
	//<![CDATA[
		var variables = new Array();
		variables['email'] = '{$address.email}';
	//]]>
</script>

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}