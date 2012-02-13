<div class="subtleBox">
	<div class="heading">
		<h3>
			{$lblPermissions|ucfirst}
		</h3>
	</div>
	<div class="options">
		<p>
			<label for="isSecured">{$chkIsSecured} {$msgIsSecured}</label>
		</p>

		{option:profileGroups}
			<p><label>{$lblOnlyFor|ucfirst}</label></p>
			<ul class="inputList">
				{iteration:profileGroups}
				<li>
					{$profileGroups.element} <label for="{$profileGroups.id}">{$profileGroups.label}</label>
				</li>
				{/iteration:profileGroups}
			</ul>
			{$chkProfileGroupsError}
		{/option:profileGroups}
	</div>
</div>