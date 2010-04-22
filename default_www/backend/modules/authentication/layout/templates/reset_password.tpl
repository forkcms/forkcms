{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
<body id="login">

	{option:debug}<div id="debugnotify">WARNING: This Fork is<br /> in debug mode</div>{/option:debug}

	<table border="0" cellspacing="0" cellpadding="0" id="loginHolder">
		<tr>
			<td>
				<div id="loginBox">
					<p>{$msgResetPasswordFormHelp}</p>
						{form:authenticationResetPassword}
								{option:error}
								<div class="errorMessage singleMessage">
									<p>{$error}</p>
								</div>
								{/option:error}

								<p>
									<label for="backendPassword">{$lblNewPassword|ucfirst}</label>
									{$txtBackendNewPassword} {$txtBackendNewPasswordError}
								</p>

								<p>
									<label for="backendPasswordRepeated">{$lblRepeatPassword|ucfirst}</label>
									{$txtBackendNewPasswordRepeated} {$txtBackendNewPasswordRepeatedError}
								</p>
								<p>
									<input id="resetPassword" class="inputButton button mainButton" type="submit" name="reset" value="{$msgResetPasswordAndSignIn|ucfirst}" />
								</p>
						{/form:authenticationResetPassword}
					</td>
					
				</div>
		</tr>
	</table>

{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}