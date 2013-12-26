{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
<body id="login">

	{include:{$BACKEND_MODULES_PATH}/{$MODULE}/layout/templates/ie6.tpl}

	{option:debug}<div id="debugnotify">WARNING: This Fork is<br /> in debug mode</div>{/option:debug}

	<table id="loginHolder">
		<tr>
			<td>
				<div id="loginBox">
					<p>{$msgHelpResetPassword}</p>
					{form:authenticationResetPassword}
							{option:error}
							<div class="errorMessage singleMessage">
								<p>{$error}</p>
							</div>
							{/option:error}

							<p>
								<label for="backendNewPassword">{$lblNewPassword|ucfirst}</label>
								{$txtBackendNewPassword} {$txtBackendNewPasswordError}
							</p>

							<p>
								<label for="backendNewPasswordRepeated">{$lblRepeatPassword|ucfirst}</label>
								{$txtBackendNewPasswordRepeated} {$txtBackendNewPasswordRepeatedError}
							</p>
							<p>
								<input id="resetPassword" class="inputButton button mainButton" type="submit" name="reset" value="{$lblResetAndSignIn|ucfirst}" />
							</p>
					{/form:authenticationResetPassword}
				</div>
			</td>
		</tr>
	</table>

{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}