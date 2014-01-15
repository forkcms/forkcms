<div class="box" id="widgetUsersStatistics">
	<div class="heading">
		<h3><a href="{$authenticatedUserEditUrl}">{$lblUsers|ucfirst}: {$lblStatistics|ucfirst}</a></h3>
	</div>

	<div class="options settingsUserInfo">
		<table class="infoGrid m0">
			<tr>
				<th>{$lblLastLogin|ucfirst}:</th>
				<td>
					{option:authenticatedUserLastLogin}{$authenticatedUserLastLogin|date:'{$authenticatedUserDateFormat} {$authenticatedUserTimeFormat}':{$INTERFACE_LANGUAGE}}{/option:authenticatedUserLastLogin}
					{option:!authenticatedUserLastLogin}{$lblNoPreviousLogin}{/option:!authenticatedUserLastLogin}
				</td>
			</tr>
			{option:authenticatedUserLastFailedLoginAttempt}
				<tr>
					<th>{$lblLastFailedLoginAttempt|ucfirst}:</th>
					<td>{$authenticatedUserLastFailedLoginAttempt|date:'{$authenticatedUserDateFormat} {$authenticatedUserTimeFormat}':{$INTERFACE_LANGUAGE}}</td>
				</tr>
			{/option:authenticatedUserLastFailedLoginAttempt}
			<tr>
				<th>{$lblLastPasswordChange|ucfirst}:</th>
				<td>
					{option:authenticatedUserLastPasswordChange}{$authenticatedUserLastPasswordChange|date:'{$authenticatedUserDateFormat} {$authenticatedUserTimeFormat}':{$INTERFACE_LANGUAGE}}{/option:authenticatedUserLastPasswordChange}
					{option:!authenticatedUserLastPasswordChange}{$lblNever}{/option:!authenticatedUserLastPasswordChange}
				</td>
			</tr>
			{option:showPasswordStrength}
				<tr>
					<th>{$lblPasswordStrength|ucfirst}:</th>
					<td>{$passwordStrengthLabel}</td>
				</tr>
			{/option:showPasswordStrength}
		</table>
	</div>

	<div class="footer">
		<div class="buttonHolderRight">
			<a href="{$authenticatedUserEditUrl}" class="button"><span>{$lblEditProfile|ucfirst}</span></a>
		</div>
	</div>
</div>