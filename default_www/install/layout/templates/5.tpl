{include:{$PATH_WWW}/install/layout/templates/head.tpl}

<h2>Your login info</h2>
{form:step5}
	<div class="horizontal">
		<p>Enter the e-mail address and password you'd like to use to log in.</p>
		<p>
			<label for="email">E-mail <abbr title="Required field">*</abbr></label>
			{$txtEmail} {$txtEmailError}
		</p>
		<p>
			<label for="password">Password <abbr title="Required field">*</abbr></label>
			{$txtPassword} {$txtPasswordError}
		</p>
		<table id="passwordStrengthMeter" class="passwordStrength" data-id="password" cellspacing="0">
			<tr>
				<td class="strength" id="passwordStrength">
					<p class="strength none">/</p>
					<p class="strength weak">Weak</p>
					<p class="strength ok">OK</p>
					<p class="strength strong">Strong</p>
				</td>
				<td>
					<p class="helpTxt">Strong passwords consist of a combination of capitals, small letters, digits and special characters.</p>
				</td>
			</tr>
		</table>
		<p>
			<label for="confirm">Confirm <abbr title="Required field">*</abbr></label>
			{$txtConfirm} {$txtConfirmError}
		</p>
	</div>
	<p class="spacing buttonHolder">
		<a href="index.php?step=4" class="button">Previous</a>
		<input id="installerButton" class="inputButton button mainButton" type="submit" name="installer" value="Finish installation" />
	</p>
{/form:step5}

{include:{$PATH_WWW}/install/layout/templates/foot.tpl}