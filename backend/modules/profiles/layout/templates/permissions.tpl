<div class="subtleBox">
	<div class="heading">
		<h3>
			{$lblPermissions|ucfirst}
		</h3>
	</div>
	<div class="options">
		<p>
			<label for="isSecured">{$chkIsSecured} {$lblIsSecured|ucfirst}</label>
		</p>

		<div id="advancedPermissionsContainer">
			{option:profileGroups}
				<p>
					<label for="forProfileGroups">{$chkForProfileGroups} {$lblForProfileGroups|ucfirst}</label>
				</p>

				<div id="groupsContainer" style="margin-left: 25px">
					<p><label>{$lblSelectAllowedProfileGroups|ucfirst}</label></p>
					<ul class="inputList">
						{iteration:profileGroups}
						<li>
							{$profileGroups.element} <label for="{$profileGroups.id}">{$profileGroups.label}</label>
						</li>
						{/iteration:profileGroups}
						{option:chkProfileGroupsError}<li>{$chkProfileGroupsError}</li>{/option:chkProfileGroupsError}
					</ul>
				</div>
			{/option:profileGroups}

			<ul class="inputList">
				<li>
					<label for="showInNavigation">{$chkShowInNavigation} {$lblShowInNavigation|ucfirst}</label>
					<span class="helpTxt">{$msgHelpShowInNavigation}</span>
				</li>
			</ul>
		</div>
	</div>
</div>