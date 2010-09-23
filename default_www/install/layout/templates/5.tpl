{include:file='{$PATH_WWW}/install/layout/templates/head.tpl'}
<body id="installer">
	<div id="installHolder" class="step5">
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
				<table id="passwordStrengthMeter" class="passwordStrength" rel="password" cellspacing="0">
					<tr>
						<td class="strength" id="passwordStrength">
							<p class="strength none">/</p>
							<p class="strength weak" style="background: red;">Weak</p>
							<p class="strength ok" style="background: orange;">OK</p>
							<p class="strength strong" style="background: green;">Strong</p>
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
	</div>
</body>
</html>